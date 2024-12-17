FROM php:8.1-apache

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apt-get update && apt-get install -y dos2unix

# Copy the application source
COPY ./src /var/www/html

# Set working directory
WORKDIR /var/www/html

# Ensure proper permissions for the application
RUN chmod -R 755 /var/www/html

COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Expose necessary ports
EXPOSE 80

# Use the entrypoint script to start migrations and Apache
ENTRYPOINT ["bash", "/usr/local/bin/entrypoint.sh"]
