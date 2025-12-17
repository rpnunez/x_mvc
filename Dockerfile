# Stage 1: Build and lint
FROM php:8.2-apache as build

# 1. Install system dependencies, PHP extensions, and Git
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    git \
    && docker-php-ext-install pdo_mysql zip

# 2. Set the working directory
WORKDIR /var/www/html

# 3. Copy composer executable
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copy dependency definitions and install all dependencies (including dev)
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-plugins --no-scripts

# 5. Copy the rest of the application source code
COPY . .

# 6. Run the linter to check for code style issues
# The --dry-run option reports issues without modifying files.
# The --diff option shows the differences.
RUN ./vendor/bin/php-cs-fixer fix --dry-run --diff --config=.php-cs-fixer.dist.php || (echo "PHP-CS-Fixer found issues. Please fix them and try again." && exit 1)

# Stage 2: Production image
FROM php:8.2-apache

# 1. Install only necessary system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql zip

# 2. Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# 3. Set the Apache document root to the /public directory
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 4. Set the working directory
WORKDIR /var/www/html

# 5. Copy composer executable
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copy only production dependencies from the build stage
COPY --from=build /var/www/html/vendor /var/www/html/vendor

# 7. Copy the application source code
COPY . .

# 8. Create writable directories and set correct permissions
RUN mkdir -p storage/cache storage/logs \
    && chown -R www-data:www-data storage/cache storage/logs

EXPOSE 80