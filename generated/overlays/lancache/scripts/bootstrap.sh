#!/bin/bash

apt-get update;
apt-get install -y \
    zip \
    git \
    nano \
    apache2 \
    composer \
    curl \
    libapache2-mod-php \
    php-mbstring \
    php-json \
    php-curl \
    php-sqlite3 \
    php \
    expect \
    ;

a2enmod rewrite;

cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:80>
    ServerName localhost
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        AllowOverride None
        Order Allow,Deny
        Allow from All
        <IfModule mod_rewrite.c>
            Options -MultiViews
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ index.php [QSA,L]
        </IfModule>
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

rm -rf /var/www/html/*

mkdir -p /var/www/html/;
git clone https://github.com/Fabioune/lancache-autofill.git /var/www/html;

cd /var/www/html && ./install.sh;

service apache2 restart;

tail -f /var/log/apache2/error.log /var/log/apache2/access.log;

