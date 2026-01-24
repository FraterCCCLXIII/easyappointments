#!/bin/bash
set -e

# Copy config.php if it doesn't exist
if [ ! -f config.php ]; then
    echo "Creating config.php from config-sample.php..."
    cp config-sample.php config.php
fi

# Set permissions
chmod -R 777 storage

# Run composer install if vendor doesn't exist
if [ ! -d vendor ]; then
    echo "Installing composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# Run migrations or other setup if needed
# (Easy!Appointments usually handles this via the UI/Install page)

exec "$@"
