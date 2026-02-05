#!/bin/bash

# Quick script to add UID/GID to .env if not present
# Usage: ./set-env-uid.sh

set -e

if [ ! -f .env ]; then
    echo "Error: .env file not found"
    exit 1
fi

CURRENT_UID=$(id -u)
CURRENT_GID=$(id -g)

# Check if UID is already set
if grep -q "^UID=" .env; then
    echo "UID already set in .env"
    # Update it if different
    if ! grep -q "^UID=$CURRENT_UID" .env; then
        sed -i "s/^UID=.*/UID=$CURRENT_UID/" .env
        echo "Updated UID to $CURRENT_UID"
    fi
else
    echo "UID=$CURRENT_UID" >> .env
    echo "Added UID=$CURRENT_UID to .env"
fi

# Check if GID is already set
if grep -q "^GID=" .env; then
    echo "GID already set in .env"
    # Update it if different
    if ! grep -q "^GID=$CURRENT_GID" .env; then
        sed -i "s/^GID=.*/GID=$CURRENT_GID/" .env
        echo "Updated GID to $CURRENT_GID"
    fi
else
    echo "GID=$CURRENT_GID" >> .env
    echo "Added GID=$CURRENT_GID to .env"
fi

echo ""
echo "✓ UID/GID configured: $CURRENT_UID:$CURRENT_GID"
echo ""
echo "Next: Rebuild containers with: docker compose -f compose.prod.yaml up -d --build"
