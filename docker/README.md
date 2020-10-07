# What's this?

A few annotations on how to spin up a local dev environment for this application and some important application configuration.

# What do I need?

Just the following:

- docker
- docker-compose

# How does it work?

Start by cloning the repository and navigating into the `./docker` directory.

In there you'll find the `.env` file which contains every aspect of the application that can be configured. The file itself is documented.

Most likey you won't need to change anything but any credential that you need to input, such as Telegram tokens.
 
Once the file has been configured simply do a `docker-compose up` to bring up the application.  

# Isn't there any test information?

As of now, the database has no seeds to create testing data.

# How's the stack like?

It's a simple LEMP stack composed of three containers:

- `bofhers_mysql`: A MySQL database _without_ persistence.
- `bofhers_nginx`: The web server that serves static content and acts as a proxy for php-fpm. 
- `bofhers_php`: A php-fpm container that holds the source code of the application plus a few tools.

The entrypoint of the `bofhers_php` container should set most of the dependencies and preconfigurations of the application.
