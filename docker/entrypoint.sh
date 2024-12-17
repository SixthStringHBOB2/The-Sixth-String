#!/bin/bash
set -e

set -x


echo "Migrations complete. Starting Apache server..."
exec apache2-foreground
