# Use Apache with PHP 8.2
FROM php:8.2-apache

# Install system dependencies and PostgreSQL client
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    postgresql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (including PostgreSQL)
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mysqli \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache to serve files from /var/www/html
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set ServerName to suppress warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create sessions directory if it doesn't exist
RUN mkdir -p /var/www/html/includes/sessions \
    && chmod 777 /var/www/html/includes/sessions

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Apache
CMD ["apache2-foreground"]
