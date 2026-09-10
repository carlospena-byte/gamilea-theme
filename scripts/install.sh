#!/bin/sh
set -eu
if ! wp core is-installed; then
  wp core install --url="$WP_URL" --title="$WP_TITLE" \
    --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="$WP_ADMIN_EMAIL" --skip-email
  wp language core install es_ES --activate
  wp rewrite structure '/%postname%/' --hard
  wp option update blogdescription 'Tu próxima compra empieza aquí'
fi
if ! wp plugin is-installed woocommerce; then
  if [ "$WC_VERSION" = latest ]; then
    wp plugin install woocommerce --activate
  else
    wp plugin install woocommerce --version="$WC_VERSION" --activate
  fi
else
  wp plugin activate woocommerce
fi
wp plugin activate advanced-custom-fields-pro
wp theme activate gamilea
wp eval 'if (class_exists("WC_Install")) { WC_Install::create_pages(); }'
wp language plugin install woocommerce es_ES
wp rewrite flush --hard
