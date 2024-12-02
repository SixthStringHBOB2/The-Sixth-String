#!/bin/bash
set -e

set -x

# Start migration process
echo "Starting migration process..."
if [ -f /var/www/html/database/migrations.php ]; then
    php /var/www/html/database/migrations.php
else
    echo "Migration script not found at /var/www/html/database/migrations.php"
    exit 1
fi

echo "Migrations complete. Starting Apache server..."
exec apache2-foreground
