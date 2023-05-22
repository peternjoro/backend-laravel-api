<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Container Description

This is a Dockerized Laravel API built using the following:

- **Laravel v10.11.0**

- **PHP**

    `PHP8.1-fpm, Composer, NPM, Node.js v14.x`

- **MySQL**

    The API is using [`db 4free`](https://db4free.net/) online MySQl database
### To use a local MySQl database:
    1. Install a local php server, e.g XAMPP, WAMP, MAMP & LAMP, with a minimum PHP v8.1
    2. Create a database named news_app
    3. Import the sql_dump file inside backup/ folder
    4. In the project folder, open the .env file and update the database credentials

- **Nginx**

    [`Nginx Official Image`](https://hub.docker.com/_/nginx/)

## Getting started

### Prerequisites:

- Docker Engine (v19.03.0+)
- Docker Compose

You can either: 
- Install [Docker Desktop](https://www.docker.com/products/docker-desktop) (includes both Docker Engine and Docker Compose)

OR

- Install [Docker Engine](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/) separately.

## Starting the containers

1. Clone the repository 

2. Open your terminal inside the project directory

3. Install all composer packages included in composer.json
    ```
    $ composer install
    ```
4. Install all npm packages included in package.json
    ```
    $ npm install
    ```
5. Generate a Laravel App Key.
    ```
    $ php artisan key:generate
    ```
6. With your terminal inside the root directory of the project, execute the following command to bring all the containers up.
   ```
    $ docker-compose up -d
    ```
7. To access the API, visit http://localhost:8000, this will however be used by the web client, the index page is the dfault page from Laravel.

## Stopping the containers

1. To bring all the containers down.

    ```
    $ docker-compose down
    ```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
