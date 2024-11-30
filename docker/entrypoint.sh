#!/bin/bash
set -e

echo "Starting migration process..."
php /var/www/html/database/migrations.php

echo "Migrations complete. Starting Apache server..."
exec apache2-foreground
