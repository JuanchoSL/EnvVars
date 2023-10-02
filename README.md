# EnvVars

## Description
A small, lightweight utility to read ENV files and append his content to environment variables.

This is a test project in order to check how works the composer installation directly from GitHub

## Install
```
composer require juanchosl/envvars
```

## How use it
Load composer autoload and use the JuanchoSL\EnvVars\EnvVars class, with abstract _read_ method you can pass it the absolute file path or the dir path where the .ENV file are placed, the content has been putted into $\_ENV superglobal or you can use getenv(ENV_VAR_NAME) instead
```
use Juanchosl\EnvVars\EnvVars;
```
### Initialize
```
EnvVars::read(realpath(dirname(__DIR__, 1)) . DIRECTORY_SEPARATOR . '.env');
```
Or
```
EnvVars::read(dirname(__DIR__, 1));
```

### Call vars
```
$env_var = getenv('ENV_VAR_NAME');
```

### Type of vars into .env file
You can use as value for your variables, fixed values, variable from other env settings or strings and functions of php than can be evaluated

#### Strings
```
VAR_KEY=var_value
```
#### Variables
Special attention to put the variable in the correct format, start with a dollar and put the name of the key to search into brackets
```
ORIGINAL_KEY=var_value
CLONED_KEY=${ORIGINAL_KEY}
```

### Evaluations
special attention to put the strign to evaluate into doble quotes and finish with a semicolon
```
EVALUATED_KEY="dirname($_SERVER['DOCUMENT_ROOT']);"
```
OR
```
EVALUATED_KEY="$_SERVER['HTTP_HOST'];"
```