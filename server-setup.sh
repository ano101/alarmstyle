#!/bin/bash

# Server setup script for AlarmStyle production deployment
# Run this script on a fresh Ubuntu/Debian server

set -e

echo "═══════════════════════════════════════════════════"
echo "   AlarmStyle Production Server Setup"
echo "═══════════════════════════════════════════════════"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run this script as root or with sudo${NC}"
    exit 1
fi

echo -e "${GREEN}[1/8] Updating system packages...${NC}"
apt-get update
apt-get upgrade -y

echo -e "${GREEN}[2/8] Installing required packages...${NC}"
apt-get install -y \
    curl \
    git \
    wget \
    unzip \
    software-properties-common \
    ca-certificates \
    gnupg \
    lsb-release

echo -e "${GREEN}[3/8] Installing Docker...${NC}"
# Remove old Docker versions
apt-get remove -y docker docker-engine docker.io containerd runc 2>/dev/null || true

# Add Docker's official GPG key
mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg

# Set up Docker repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker Engine
apt-get update
apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Start and enable Docker
systemctl start docker
systemctl enable docker

echo -e "${GREEN}[4/8] Creating deployment user...${NC}"
if id "deploy" &>/dev/null; then
    echo "User 'deploy' already exists"
else
    useradd -m -s /bin/bash deploy
    usermod -aG docker deploy
    echo "User 'deploy' created and added to docker group"
fi

echo -e "${GREEN}[5/8] Setting up application directory...${NC}"
APP_DIR="/var/www/alarmstyle"
mkdir -p $APP_DIR
chown deploy:deploy $APP_DIR

echo -e "${GREEN}[6/8] Configuring firewall...${NC}"
# Install and configure UFW
apt-get install -y ufw

# Allow SSH, HTTP, and HTTPS
ufw --force enable
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp comment 'SSH'
ufw allow 80/tcp comment 'HTTP'
ufw allow 443/tcp comment 'HTTPS'

echo -e "${GREEN}[7/8] Setting up SSH key authentication for deploy user...${NC}"
if [ ! -d "/home/deploy/.ssh" ]; then
    mkdir -p /home/deploy/.ssh
    chmod 700 /home/deploy/.ssh
    touch /home/deploy/.ssh/authorized_keys
    chmod 600 /home/deploy/.ssh/authorized_keys
    chown -R deploy:deploy /home/deploy/.ssh
    echo ""
    echo -e "${YELLOW}Add your SSH public key to: /home/deploy/.ssh/authorized_keys${NC}"
    echo ""
fi

echo -e "${GREEN}[8/8] Installing fail2ban for SSH protection...${NC}"
apt-get install -y fail2ban
systemctl start fail2ban
systemctl enable fail2ban

echo ""
echo "═══════════════════════════════════════════════════"
echo -e "${GREEN}✅ Server setup completed!${NC}"
echo "═══════════════════════════════════════════════════"
echo ""
echo "Next steps:"
echo "1. Add your SSH public key to /home/deploy/.ssh/authorized_keys"
echo "2. Test SSH connection: ssh deploy@your-server-ip"
echo "3. Clone the repository as deploy user:"
echo "   su - deploy"
echo "   cd /var/www/alarmstyle"
echo "   git clone https://github.com/your-username/alarmstyle.git ."
echo "4. Follow QUICKSTART.md for application setup"
echo ""
echo "Installed versions:"
docker --version
docker compose version
echo ""
echo -e "${YELLOW}⚠️  Please reboot the server for all changes to take effect:${NC}"
echo "   sudo reboot"
