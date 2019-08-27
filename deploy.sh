#!/bin/bash

# Clear var files
rm -rf var/cache/*
rm -rf var/logs/dev.log var/logs/prod.log var/logs/test.log
echo -e "Clearing var files was successfully done."

# Composer install
php composer install > /dev/null 2>&1
echo -e "Composer install done."

# Update DB
php bin/console doctrine:migration:migrate --no-interaction
echo -e "Database was updated successfully"

# Set needed permissions for app folders
chmod 0777 -R var/cache/ var/logs/