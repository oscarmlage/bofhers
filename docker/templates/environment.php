<?php
/**
 * This file generates an application .env file by using environment variables
 * and echoes it to stdout.
 *
 * This allows to abstract credentials and configuration in a way that can
 * be shared through all the microservices and the application itself.
 *
 * This file should not be modified. If you wish to do changes here:
 *
 * - Delete the application .env file
 * - Change docker/.env to suit your needs
 * - Redeploy the application
 */
echo <<<CONFIG
APP_NAME={$_ENV['BOFHERS_APP_NAME']}
APP_ENV={$_ENV['BOFHERS_APP_ENV']}
APP_KEY=
APP_DEBUG=true
APP_URL={$_ENV['BOFHERS_APP_URL']}

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST={$_ENV['BOFHERS_DB_HOST']}
DB_PORT={$_ENV['BOFHERS_DB_PORT']}
DB_DATABASE={$_ENV['BOFHERS_DATABASE']}
DB_USERNAME={$_ENV['BOFHERS_DB_USERNAME']}
DB_PASSWORD={$_ENV['BOFHERS_DB_PASSWORD']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY=
MIX_PUSHER_APP_CLUSTER=

TELEGRAM_WEBHOOK_ROUTE={$_ENV['BOFHERS_TELEGRAM_WEBHOOK_ROUTE']}
TELEGRAM_WEBHOOK_URL={$_ENV['BOFHERS_TELEGRAM_WEBHOOK_URL']}
TELEGRAM_BOT_TOKEN={$_ENV['BOFHERS_TELEGRAM_BOT_TOKEN']}

CONFIG;
