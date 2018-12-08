#!/bin/bash
# ln -s /home/jordy-theme /var/www/html/wp-content/themes/jordy-theme

# Install WordPress.
wp core install \
  --title="Jordy" \
  --admin_user="$WORDPRESS_ADMIN_USER" \
  --admin_password="$WORDPRESS_ADMIN_PASSWORD" \
  --admin_email="$WORDPRESS_ADMIN_EMAIL" \
  --skip-email \
  --url="https://jordy.dev"

# Configuration
wp option update permalink_structure "/%postname%/" --skip-themes --skip-plugins

# Install needed plugins
wp plugin install --activate \
  wp-api-menus \
  redis-cache \
  advanced-custom-fields \
  acf-to-rest-api \
  wp-rest-api-log \
  wp-rest-api-cache \
  jwt-authentication-for-wp-rest-api

# Activate jordy and cleaning
wp theme activate jordy-theme
wp post delete 1 --force # Article exemple - no trash. Comment is also deleted
wp post delete 2 --force # page exemple
wp plugin delete hello
wp theme delete twentysixteen
wp theme delete twentyseventeen
wp theme delete twentynineteen
wp option update blogdescription ''

# Config Redis on wp-config.php
wp config set WP_REDIS_HOST 'redis' --type=constant
wp config set WP_CACHE_KEY_SALT $WP_CACHE_KEY_SALT --type=constant
wp config set CACHE true --type=constant --raw

# Config WordPress API JWT auth plugin
wp config set JWT_AUTH_SECRET_KEY $JWT_AUTH_SECRET_KEY --type=constant
wp config set JWT_AUTH_CORS_ENABLE true --type=constant --raw