# What's this?

A few annotations on how to spin up a local dev environment for this application.
 
This document also includes a few key configurations needed in order to properly integrate with Telegram so that you don't have to spend as many time as I did trying to figure it out.

The procedures described below could probably be used not only to create a test enviroment, but also to deploy the app using Docker somewhere else (VPS, public instance, yadda yadda yadda) but as of now the current documentation will only cover a single use case: a testing app on _localhost_ that can be integrated with Telegram using CloudFlare as a proxy and SSL termination.

# Setting up the prerequisites

To be able to launch the app on local you'll need:

- docker
- docker-compose

If you also want to use the Telegram integration you'll also need:

1. A public A record that points to your application.
2. A valid SSL certificate.
3. A Telegram _Group_ (**DO NOT** mistake it with a Telegram _Channel_).
4. A Telegram bot and its API secret.

Note that for the target use case, this document will be using CloudFlare to cover points `1` and `2` using your router's own public IP address as the site where your app will be hosted. Reasoning is simple: it works without any extra infra requirement and at the moment of writing this I couldn't spend more time on this to add the Let's Encrypt container or Telegram's self-signed certificate integration needed to make it more platform agnostic.

Also, bear in mind that most this document won't cover you if you want to deploy it in another way (for example: on a public instance or VPS server), _but_ the proccess should still be pretty straightforward, as you'll only need to tweak your `./docker/.env` file to suit your needs.

# How does it work?

Start by cloning the repository and navigating into the `./docker` directory.

In there you'll find the `./docker/.env.example` file which contains every aspect of the application that can be configured. Copy the file to `./docker/.env` and edit it to suit your needs. The file itself is documented and straightforward. **DO NOT** mistake this _envfile_ with the application's own _envfile_; `./env` and `./docker/.env` are different files used for different things.

The defaults on the file are sane so most likely you won't need to change anything but the Telegram credentials, URLs and the PHP user ID (which might require you to create an user on your system with docker permissions).

Speaking of Telegram, if you need help to configure its integration read the section "_Setting up Telegram_" below. Do this **before** starting the application.
 
Once the file has been configured (**including** Telegram's parameters) simply do a `docker-compose up --build` to start the application.

# Setting up Telegram

The following sections explain how to set up the Telegram integration, which you'll need in order to make a testing bot capable of answering to your commands.

## Setting up an A record

The quickest (and _dirtiest_) way to make the application work without using any VPS or public instance revolves around using your own router's public IP address to host the application. Depending on your setup, you might need to forward the HTTP and HTTPS traffic to the machine where you host the application.

To make it work you'll need to [register at CloudFlare](https://www.cloudflare.com/), add a domain there and create a subdomain that points to your router's public IP address. It doesn't matter if it changes as you'll be able to modify the CloudFlare's A record whenever you need to.

Once you're registered, add a domain to CloudFlare's dashboard and create an A record pointing to wherever you need. Make sure that the entry is allowed to go through CloudFlare's proxy server (the "_orange cloud_" just by the entry on the dashboard).

As this is done, you'll need to add your subdomain's URL to your `./docker/.env` file under the `BOFHERS_TELEGRAM_WEBHOOK_URL` setting. Mind that you'll need the URL to start with `https://` and to end with a trailing slash (`/`).

Luckily enough, CloudFlare also offers the possibility to add an SSL termination to your subdomain. As we'll need one to work with Telegram, be sure to set it up as "_Flexible_" encryption type on CloudFlare.

## Creating a Telegram bot

If you don't have it already, you'll need to create a testing bot for Telegram.

Doing so is pretty simple and just requires you to use Telegram itself to send a message to the BotFather ([here's a link to it for your convenience](https://telegram.me/botfather)).

When you set it up make sure to **disable** the setting for _Group Privacy_ as it can make your bot not able to join a group or listen to commands. 

Once you are done, save your bot's API Token into your `./docker/.env` file under the `BOFHERS_TELEGRAM_BOT_TOKEN` entry.

You can find more information on Telegram's official documentation regarding bots:

- https://core.telegram.org/bots

## Creating a Telegram group

This process is pretty straightforward. Simply create a Telegram Group (**NOT** a Telegram Channel) by using the application itself. Once it has been created, invite your bot and grant it enough permissions to read all the incoming messages.

After this, you'll need to figure out the Group's `chat_id`. The easiest way to do this is to invite `@RawDataBot` to your channel. Upon its first entry to the channel, the bot will give you internal details about the Group. Once it has fulfilled its purpose you can remove it from the channel. Be sure to note down the `chat_id` for your Group, which you'll need later on.

## Setting up the application

At this point you are ready to spin up the application. Simply do a `docker-compose up --build` and wait for all the containers to finish starting. 

Please note that the first time that you start the application the `bofhers_php` container will need to set up all dependencies, which might take a while. The rest of the containers will wait to start until this is ready.
 
Once all the containers are done starting, you should be able to access the application's admin panel by logging into `http://localhost/admin` or `${BOFHERS_TELEGRAM_WEBHOOK_URL}/admin`. In there you'll need to create an admin account so that you can log in to the backend.
 
When you log in you'll need to set up your Telegram's Group in the admin interface. Click on "_Telegram, canales_" on the left menu and then "_Add telegram_canal_". In the form that will show up you'll need to add your Group's data, **make sure to mark it as Active**.

# How do I rebuild the dev environment?

If for any reason you think you messed up and want to start over, simply:

```shell script
# Delete the containers
cd docker
docker-compose rm -f

# Delete the applications .env file and the vendor directory
rm ../.env 
rm -rf ../vendor

# If needed tweak your docker-composer's .env file
vi .env

# Rebuild and launch the containers again
docker-compose up --build
```  

# Isn't there any test information?

As of now, the database has no seeds to create testing data.

# How's the stack like?

It's a simple LEMP stack composed of three containers:

- `bofhers_mysql`: A MySQL database _without_ persistence.
- `bofhers_nginx`: The web server that serves static content and acts as a proxy for php-fpm. 
- `bofhers_php`: A php-fpm container that holds the source code of the application plus a few tools.

The entrypoint of the `bofhers_php` container should set most of the dependencies and preconfigurations of the application.

# Where's the data stored?

There are no external volumes in this configuration. Data is ephemeral, that is: if the containers are destroyed, you'll lose it. This is not intended for production unless you set up the volumes for MySQL so that information is not lost.

