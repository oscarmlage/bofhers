# What's this?

A few annotations on how to spin up a quick LEMP dev environment for this application.

# What do I need?

Just the following:

- docker
- docker-compose

# How does it work?

Start by cloning the repository and navigating into the  ./docker  directory.

In there you'll find the `.env` file which contains configuration aspects -that do not require to be changed- and credentials. The file is commented and you'll most likely only need to edit the credentials.
 
Once the file has been configured simply do a `docker-compose up` to bring up the application. 

By default it listens on the local machine port.

# Isn't there any test information?

As of now, the database has no seeds to create testing data.

You can register a new admin user by accesing `http://localhost/admin`.

# How's the stack like?

It's a simple LEMP stack composed of three containers:

- `bofhers_mysql`: A MySQL database _without_ persistence.
- `bofhers_nginx`: The web server that serves static content and acts as a proxy for php-fpm. 
- `bofhers_php`: A php-fpm container that holds the source code of the application plus a few tools.

The entrypoint of the `bofhers_php` container should set most of the dependencies and preconfigurations of the application.

# Legacy steps

These are here in case I need them, but _suposedly_ they aren't needed.

Empieza por clonar el repositorio y navegar al directorio   ./docker 
- docker-compose build
- docker-compose up
- docker-compose exec app php artisan migrate
- docker-compose exec app php artisan backpack:base:install
