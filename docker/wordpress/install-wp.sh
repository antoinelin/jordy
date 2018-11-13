#!/bin/bash

# Install WordPress.
wp core install \
  --title="WordPress Gatsby" \
  --admin_user="$WORDPRESS_ADMIN_USER" \
  --admin_password="$WORDPRESS_ADMIN_PASSWORD" \
  --admin_email="$WORDPRESS_ADMIN_EMAIL" \
  --skip-email \
  --url="http://localhost"

# Update permalink structure.
wp option update permalink_structure "/%postname%" --skip-themes --skip-plugins

# Activate plugin.
# wp plugin activate my-plugin