#!/usr/bin/env bash
set -e

source docker/.env

function _msg()
{
  echo -e "\e[1;32m[${BOFHERS_APP_NAME}] $1\e[0"
}
# Create an application .env file if it doesn't exist
if [[ ! -f .env ]];
then
  _msg "Generating .env file..."
  php -d variables_order=EGPCS docker/templates/environment.php > .env
else
  _msg ".env file already created, skipping."
fi

#Bring down composer dependencies, if they weren't before
if [[ ! -d vendor ]];
then
  _msg "Installing composer dependencies..."
  composer install
else
  _msg "Composer dependencies already installed, skipping."
fi

# Create laravel encryption key if it is not present on the file
if ! grep -Eq 'APP_KEY=.{50}' .env; then
  _msg "Creating encryption key..."
  php artisan key:generate
  php artisan cache:clear
else
  _msg "Encryption key already generated, skipping."
fi

# Install laravel backpack
if [[ ! -f vendor/.backpack_installed ]];
then
  _msg "Installing backpack..."
  php artisan backpack:base:install
  touch vendor/.backpack_installed
else
  _msg "Backpack already installed, skipping."
fi

_msg "Setting up webhook..."
php artisan telegram:webhook --setup --all

_msg "Registering bot commands..."
php artisan telegram:registerBotcommands

# Run php-fpm
_msg "Starting php-fpm..."
php-fpm
