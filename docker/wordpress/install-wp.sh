#!/bin/bash

# Install WordPress.
wp core install \
  --title="WordPress" \
  --admin_user="$WORDPRESS_ADMIN_USER" \
  --admin_password="$WORDPRESS_ADMIN_PASSWORD" \
  --admin_email="$WORDPRESS_ADMIN_EMAIL" \
  --skip-email \
  --url="https://wordpress.dev"

# Update permalink structure.
wp option update permalink_structure "/%postname%" --skip-themes --skip-plugins

# Install needed plugins
wp plugin install redis-cache --activate

# Config Redis on wp-config.php
wp config set WP_REDIS_HOST 'redis' --raw --type=string