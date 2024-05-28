# Use the official PHP image as a base
FROM php:8.1-apache

# Install required PHP extensions and dependencies
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the document root to the public directory (change if necessary)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Copy application source code to the container
COPY . /var/www/html

# Set proper permissions for the Apache web root
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Add after other RUN instructions
COPY docker-config/igp.local.conf /etc/apache2/sites-available/igp.local.conf

# Enable the virtual host
RUN a2ensite igp.local.conf

# Ensure the public directory and index.php exist before setting permissions
RUN mkdir -p /var/www/html/public && \
    touch /var/www/html/public/index.php && \
    chmod -R 755 /var/www/html/public && \
    chmod 755 /var/www/html/public/index.php

# Restart Apache to apply changes
CMD apachectl -D FOREGROUND
