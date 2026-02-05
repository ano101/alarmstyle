.PHONY: help dev prod-build prod-up prod-down prod-restart prod-logs prod-shell deploy optimize backup monitor preflight

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
RESET := \033[0m

help: ## Show this help message
	@echo "$(BLUE)AlarmStyle - Available Commands$(RESET)"
	@echo ""
	@echo "$(GREEN)Local Development (Sail):$(RESET)"
	@echo "  make dev              - Start development environment"
	@echo "  make dev-down         - Stop development environment"
	@echo "  make dev-build        - Rebuild development containers"
	@echo ""
	@echo "$(GREEN)Production:$(RESET)"
	@echo "  make preflight        - Run pre-deployment checks"
	@echo "  make prod-build       - Build production containers"
	@echo "  make prod-up          - Start production environment"
	@echo "  make prod-down        - Stop production environment"
	@echo "  make prod-restart     - Restart production containers"
	@echo "  make prod-logs        - Show production logs"
	@echo "  make prod-shell       - Open shell in app container"
	@echo ""
	@echo "$(GREEN)Deployment & Maintenance:$(RESET)"
	@echo "  make deploy           - Deploy to production"
	@echo "  make optimize         - Optimize application"
	@echo "  make backup           - Create database backup"
	@echo "  make monitor          - Show production dashboard"
	@echo "  make health           - Run health checks"
	@echo ""

# Local Development (Sail)
dev: ## Start local development environment
	vendor/bin/sail up -d

dev-down: ## Stop local development environment
	vendor/bin/sail down

dev-build: ## Rebuild local development containers
	vendor/bin/sail build --no-cache
	vendor/bin/sail up -d

# Production
preflight: ## Run pre-deployment checks
	./preflight-check.sh

prod-build: ## Build production Docker image
	docker compose -f compose.prod.yaml build --no-cache

prod-up: ## Start production environment
	docker compose -f compose.prod.yaml up -d

prod-down: ## Stop production environment
	docker compose -f compose.prod.yaml down

prod-restart: ## Restart production containers
	docker compose -f compose.prod.yaml restart

prod-logs: ## Show production logs
	docker compose -f compose.prod.yaml logs -f

prod-shell: ## Open shell in production app container
	docker compose -f compose.prod.yaml exec app sh

# Deployment & Maintenance
deploy: ## Deploy to production
	./deploy.sh

optimize: ## Optimize production application
	docker compose -f compose.prod.yaml exec app php artisan config:cache
	docker compose -f compose.prod.yaml exec app php artisan route:cache
	docker compose -f compose.prod.yaml exec app php artisan view:cache
	docker compose -f compose.prod.yaml exec app php artisan filament:cache-components

backup: ## Create database backup
	@mkdir -p backups
	@docker compose -f compose.prod.yaml exec -T mysql mysqldump -u root -p$${DB_PASSWORD} $${DB_DATABASE} > backups/backup-$$(date +%Y%m%d-%H%M%S).sql
	@echo "Backup created successfully!"

monitor: ## Show production dashboard
	./monitor.sh dashboard

health: ## Run health checks
	./monitor.sh health
