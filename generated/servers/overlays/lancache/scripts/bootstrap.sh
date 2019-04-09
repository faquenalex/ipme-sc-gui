#!/bin/bash

SCRIPT_DIR="/var/www/html"

GREEN='\033[0;32m'
BLACK='\033[0m'

apt-get update
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
    lib32gcc1 \
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

rm -rf /var/www/html/

mkdir -p /var/www/html/
chown www-data:www-data -R /var/www/html
chmod 0777 -R /var/www/html/

git clone https://github.com/Fabioune/lancache-autofill.git /var/www/html

printf "${GREEN}Installing dependencies with Composer${BLACK}\n"
cd $SCRIPT_DIR && composer update

printf "${GREEN}Installing Steam${BLACK}\n"
mkdir -p /usr/games/steam && cd /usr/games/steam && curl -sqL "http://media.steampowered.com/client/steamcmd_linux.tar.gz" | tar zxvf -

printf "${GREEN}Creating database file${BLACK}\n"
cd $SCRIPT_DIR && touch "database.sqlite"


printf "${GREEN}Creating your enviroment file${BLACK}\n"
echo > ".env"
echo DOWNLOADS_DIRECTORY="$DOWNLOADS_DIRECTORY" >> ".env"
echo STEAMCMD_PATH="$STEAMCMD_PATH" >> ".env"
echo DEFAULT_STEAM_USER="$DEFAULT_STEAM_USER" >> ".env"

service apache2 restart

php $SCRIPT_DIR/lancache-autofill app:initialise-database

php $SCRIPT_DIR/lancache-autofill steam:update-app-list

php $SCRIPT_DIR/lancache-autofill steam:authorise-account $DEFAULT_STEAM_USER

tail -f /var/log/apache2/error.log /var/log/apache2/access.log
