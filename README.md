# Demo Weather App

### Intro

This demo project is running with middleware concept (PSR-15: HTTP Server Request Handlers):
- FastRoute (https://github.com/nikic/FastRoute) as router 
- Twig (https://twig.symfony.com/) as Template-Engine for the view layer
- PDO [sqlite] (https://www.php.net/manual/en/intro.pdo.php) as database layer
- Weather Wrapper (https://github.com/voku/weather) as API services

### Files

- `/bin/*` -> command line scripts
- `/database/*` -> the sqlite database itself
- `/public/*` -> public web server directory (css, images, ...)
- `/src/*` -> php code 
- `/src/framework/*` -> meta code
- `/src/modules/*` -> something like packages
- `/src/modules/*/commands/*` -> cli commands
- `/src/modules/*/entities/*` -> data objects
- `/src/modules/*/repositories/*` -> data sources
- `/src/modules/*/services/*` -> services that works with data
