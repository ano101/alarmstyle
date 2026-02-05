#!/bin/bash

# Pre-flight check script for production deployment
# Verifies that everything is configured correctly before deployment

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

echo "════════════════════════════════════════════════════"
echo "   AlarmStyle Production Pre-flight Check"
echo "════════════════════════════════════════════════════"
echo ""

# Function to check and report
check_pass() {
    echo -e "${GREEN}✓${NC} $1"
}

check_fail() {
    echo -e "${RED}✗${NC} $1"
    ((ERRORS++))
}

check_warn() {
    echo -e "${YELLOW}⚠${NC} $1"
    ((WARNINGS++))
}

check_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

# Check Docker
echo "🐳 Checking Docker..."
if command -v docker &> /dev/null; then
    DOCKER_VERSION=$(docker --version | cut -d ' ' -f3 | cut -d ',' -f1)
    check_pass "Docker installed: $DOCKER_VERSION"

    if docker compose version &> /dev/null; then
        COMPOSE_VERSION=$(docker compose version | cut -d ' ' -f4 | cut -d ',' -f1)
        check_pass "Docker Compose installed: $COMPOSE_VERSION"
    else
        check_fail "Docker Compose not found"
    fi

    if docker ps &> /dev/null; then
        check_pass "Docker daemon running"
    else
        check_fail "Docker daemon not running or no permission"
    fi
else
    check_fail "Docker not installed"
fi

echo ""
echo "📁 Checking files and directories..."

# Check essential files
REQUIRED_FILES=(
    "compose.prod.yaml"
    "Dockerfile"
    "docker/nginx/nginx.conf"
    "docker/nginx/conf.d/default.conf"
    "docker/php/php.ini"
    "docker/php/www.conf"
    "docker/mysql/my.cnf"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        check_pass "Found: $file"
    else
        check_fail "Missing: $file"
    fi
done

# Check .env file
if [ -f ".env" ]; then
    check_pass ".env file exists"

    # Check critical env variables
    echo ""
    echo "🔐 Checking environment variables..."

    source .env 2>/dev/null || true

    [ -n "$APP_KEY" ] && check_pass "APP_KEY is set" || check_fail "APP_KEY is not set"
    [ -n "$APP_URL" ] && check_pass "APP_URL is set: $APP_URL" || check_warn "APP_URL is not set"
    [ -n "$DB_DATABASE" ] && check_pass "DB_DATABASE is set" || check_fail "DB_DATABASE is not set"
    [ -n "$DB_USERNAME" ] && check_pass "DB_USERNAME is set" || check_fail "DB_USERNAME is not set"
    [ -n "$DB_PASSWORD" ] && check_pass "DB_PASSWORD is set" || check_fail "DB_PASSWORD is not set"
    [ -n "$REDIS_PASSWORD" ] && check_pass "REDIS_PASSWORD is set" || check_warn "REDIS_PASSWORD is not set (recommended)"
    [ -n "$MEILISEARCH_KEY" ] && check_pass "MEILISEARCH_KEY is set" || check_fail "MEILISEARCH_KEY is not set"

    # Check UID/GID for proper file permissions
    CURRENT_UID=$(id -u)
    CURRENT_GID=$(id -g)
    if [ -n "$UID" ] && [ "$UID" = "$CURRENT_UID" ]; then
        check_pass "UID is set correctly: $UID"
    else
        check_warn "UID not set or incorrect. Current UID: $CURRENT_UID. Add to .env: echo 'UID=$CURRENT_UID' >> .env"
    fi

    if [ -n "$GID" ] && [ "$GID" = "$CURRENT_GID" ]; then
        check_pass "GID is set correctly: $GID"
    else
        check_warn "GID not set or incorrect. Current GID: $CURRENT_GID. Add to .env: echo 'GID=$CURRENT_GID' >> .env"
    fi

    # Check if APP_ENV is production
    if [ "$APP_ENV" = "production" ]; then
        check_pass "APP_ENV is set to production"
    else
        check_warn "APP_ENV is not 'production': $APP_ENV"
    fi

    # Check if APP_DEBUG is false
    if [ "$APP_DEBUG" = "false" ]; then
        check_pass "APP_DEBUG is false"
    else
        check_warn "APP_DEBUG should be false in production: $APP_DEBUG"
    fi
else
    check_fail ".env file not found"
    check_info "Copy .env.production.example to .env and configure it"
fi

echo ""
echo "📦 Checking dependencies..."

# Check composer.lock
if [ -f "composer.lock" ]; then
    check_pass "composer.lock exists"
else
    check_warn "composer.lock not found"
fi

# Check package-lock.json
if [ -f "package-lock.json" ]; then
    check_pass "package-lock.json exists"
else
    check_warn "package-lock.json not found"
fi

echo ""
echo "🔧 Checking permissions..."

# Check storage directory
if [ -d "storage" ]; then
    if [ -w "storage" ]; then
        check_pass "storage/ is writable"
    else
        check_fail "storage/ is not writable"
    fi
else
    check_fail "storage/ directory not found"
fi

# Check bootstrap/cache directory
if [ -d "bootstrap/cache" ]; then
    if [ -w "bootstrap/cache" ]; then
        check_pass "bootstrap/cache/ is writable"
    else
        check_fail "bootstrap/cache/ is not writable"
    fi
else
    check_fail "bootstrap/cache/ directory not found"
fi

echo ""
echo "🔍 Checking scripts..."

SCRIPTS=(
    "deploy.sh"
    "monitor.sh"
    "server-setup.sh"
)

for script in "${SCRIPTS[@]}"; do
    if [ -f "$script" ]; then
        if [ -x "$script" ]; then
            check_pass "$script is executable"
        else
            check_warn "$script is not executable (chmod +x $script)"
        fi
    else
        check_warn "$script not found"
    fi
done

echo ""
echo "📚 Checking documentation..."

DOCS=(
    "README.md"
    "QUICKSTART.md"
    "DEPLOYMENT.md"
    "CHECKLIST.md"
)

for doc in "${DOCS[@]}"; do
    if [ -f "$doc" ]; then
        check_pass "Documentation: $doc"
    else
        check_warn "Missing documentation: $doc"
    fi
done

# Git check
echo ""
echo "🔀 Checking Git..."
if git rev-parse --git-dir > /dev/null 2>&1; then
    check_pass "Git repository initialized"

    BRANCH=$(git rev-parse --abbrev-ref HEAD)
    check_info "Current branch: $BRANCH"

    if git diff-index --quiet HEAD -- 2>/dev/null; then
        check_pass "No uncommitted changes"
    else
        check_warn "There are uncommitted changes"
    fi
else
    check_warn "Not a git repository"
fi

# Summary
echo ""
echo "════════════════════════════════════════════════════"
echo "                    Summary"
echo "════════════════════════════════════════════════════"
echo ""

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed! Ready for deployment.${NC}"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠ $WARNINGS warning(s) found. Review before deployment.${NC}"
    exit 0
else
    echo -e "${RED}✗ $ERRORS error(s) and $WARNINGS warning(s) found.${NC}"
    echo ""
    echo "Please fix the errors before deployment."
    echo ""
    echo "Common fixes:"
    echo "  - Install Docker: curl -fsSL https://get.docker.com | sh"
    echo "  - Create .env: cp .env.production.example .env"
    echo "  - Generate APP_KEY: docker run --rm php:8.5-cli php -r \"echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;\""
    echo "  - Fix permissions: chmod -R 775 storage bootstrap/cache"
    echo "  - Make scripts executable: chmod +x *.sh"
    echo ""
    exit 1
fi
