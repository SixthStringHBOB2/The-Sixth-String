FROM php:8.1-apache

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy the application source
COPY ./src /var/www/html

# Set working directory
WORKDIR /var/www/html

# Ensure proper permissions
RUN chmod -R 755 /var/www/html

# Add entrypoint script
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

# Use the entrypoint script to start migrations and Apache
ENTRYPOINT ["entrypoint.sh"]
