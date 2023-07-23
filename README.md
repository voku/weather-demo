[![Build Status](https://github.com/voku/weather-demo/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/voku/weather-demo/actions)
[![codecov.io](http://codecov.io/github/voku/weather-demo/coverage.svg?branch=main)](http://codecov.io/github/voku/weather-demo?branch=main)

# Demo Weather App

### Intro

This demo project is running with middleware concept (PSR-15: HTTP Server Request Handlers):
- FastRoute (https://github.com/nikic/FastRoute) as router 
- PHP-DI (https://php-di.org/doc/) as dependency injection container
- Twig (https://twig.symfony.com/) as Template-Engine for the view layer
- PDO [sqlite] (https://www.php.net/manual/en/intro.pdo.php) as database layer
- Weather Wrapper (https://github.com/voku/weather) as API services

### Files

- `/bin/*` -> command line scripts
- `/database/*` -> the sqlite database itself
- `/public/*` -> public web server directory (css, images, ...)
- `/src/*` -> php code 
- `/src/framework/*` -> meta code
- `/src/framework/DependencyInjection.php` -> dependency injection definition
- `/src/framework/routing/HttpRouting.php` -> http routing definition
- `/src/modules/*` -> something like packages
- `/src/modules/*/commands/*` -> cli commands
- `/src/modules/*/entities/*` -> data objects
- `/src/modules/*/repositories/*` -> data sources
- `/src/modules/*/services/*` -> services that works with data

### Quick start

For development, you can just use the build in webserver from php:

`php8.2 -S localhost:8080 -t public/`

For production, you need to point the `root` path to the `public` directory (e.g. for nginx: `root /var/www/weather-demo.suckup.de/web/public;`)
and you need to pass all requests to this path (e.g. for nginx: `if (!-e $request_filename){ rewrite (.*) /index.php?$query_string; }`)
