#!/bin/sh
set -e

# Get current user ID and group ID
CURRENT_UID=$(id -u)
CURRENT_GID=$(id -g)

echo "Running as UID:GID = ${CURRENT_UID}:${CURRENT_GID}"

# If running as root (UID 0), use www-data defaults
if [ "$CURRENT_UID" -eq 0 ]; then
    echo "Running as root, using www-data defaults"
    exec "$@"
fi

# For Alpine Linux: Get username and groupname or use UID/GID directly
# Try to get username, fallback to UID if user doesn't exist
USER_NAME=$(getent passwd "$CURRENT_UID" | cut -d: -f1 2>/dev/null || echo "$CURRENT_UID")
GROUP_NAME=$(getent group "$CURRENT_GID" | cut -d: -f1 2>/dev/null || echo "$CURRENT_GID")

echo "Configuring PHP-FPM for user: ${USER_NAME} (${CURRENT_UID}), group: ${GROUP_NAME} (${CURRENT_GID})"

# Update PHP-FPM pool config to use current user
sed -i "s/user = www-data/user = ${USER_NAME}/" /usr/local/etc/php-fpm.d/www.conf
sed -i "s/group = www-data/group = ${GROUP_NAME}/" /usr/local/etc/php-fpm.d/www.conf
sed -i "s/listen.owner = www-data/listen.owner = ${USER_NAME}/" /usr/local/etc/php-fpm.d/www.conf
sed -i "s/listen.group = www-data/listen.group = ${GROUP_NAME}/" /usr/local/etc/php-fpm.d/www.conf

# Ensure storage and cache directories exist
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/bootstrap/cache

# Set proper permissions (775 for directories)
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

echo "✓ PHP-FPM configured successfully"

exec "$@"
