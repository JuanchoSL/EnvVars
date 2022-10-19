# EnvVars

## Description
A small, lightweight utility to read ENV files and append his content to environment variables.

This is a test project in order to check how works the composer installation directly from GitHub

## Install
```
composer require juanchosl/envvars
```

## The use
Load composer autoload and use the JuanchoSL\EnvVars\EnvVars class, with abstract _read_ method you can pass it the absolute url of the .ENV file, the the content has been putted into $\_ENV superglobal or you can use getenv(ENV_VAR_NAME) instead
```
use Juanchosl\EnvVars\EnvVars;
EnvVars::read(__DIR__ . DIRECTORY_SEPARATOR . '.env');
```
```
$env_var = getenv('ENV_VAR_NAME');
```
