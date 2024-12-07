#!/bin/bash
set -e

set -x

# Start migration process
echo "Starting migration process..."
if [ -f /var/www/html/database/db.php ]; then
    php /var/www/html/database/db.php
else
    echo "Migration script not found at /var/www/html/database/db.php"
    exit 1
fi

echo "Migrations complete. Starting Apache server..."
exec apache2-foreground
