# EnvVars

## Description

A small, lightweight utility to read ENV files and append his content to environment variables.

This is a test project in order to check how works the composer installation directly from GitHub

## Install
```bash
composer require juanchosl/envvars
```

## How to use it

Load composer autoload and use the JuanchoSL\EnvVars\EnvVars class, with abstract _read_ method you can pass it the absolute file path or the dir path where the .ENV file are placed, the content has been putted into $\_ENV superglobal or you can use getenv(ENV_VAR_NAME) instead
```php
use Juanchosl\EnvVars\EnvVars;
```

### Initialize
```php
EnvVars::init(realpath(dirname(__DIR__, 1)) . DIRECTORY_SEPARATOR . '.env');
```

Or
```php
EnvVars::init(dirname(__DIR__, 1));
```

### Configure

We have some options for configure the use of the library, actually:

- OPTION_PROCESS_SECTIONS (default **false**)

```php
EnvVars::init(dirname(__DIR__, 1), [
    EnvVars::OPTION_PROCESS_SECTIONS => true
]);

```

### Add more files

Now, you can parse more files with new env vars in order to add to scope
```php
$envvar = EnvVars::init(dirname(__DIR__, 1).DIRECTORY_SEPARATOR.'.env-database');
$envvar->parseFile(dirname(__DIR__, 1).DIRECTORY_SEPARATOR.'.env-tokens')
```

### Call vars
```php
$env_var = getenv('ENV_VAR_NAME');
```

Or
```php
$env_var = $_ENV['ENV_VAR_NAME'];
```

### Type of vars into .env file

You can use as values for your variables:

- fixed values
- variable from other env settings
- strings and functions of php than can be evaluated

#### Literals

```php
VAR_KEY=var_value
```

#### Variables

Yo can use other env var name as content for set a nev environment variable. The order is not strict for reuse static values into env vars, the system parse and set first the literal variables, then the dynamic content, the var as value is then available.
Pay attention to put the variable in the correct format, start with a dollar and put the name of the key to search into brackets

```php
ORIGINAL_KEY=var_value
CLONED_KEY=${ORIGINAL_KEY}
```

#### Evaluations

The system can parse and evaluate native functions in order to solve complex values.
Pay attention to put the strign to evaluate into doble quotes and finish with a semicolon

```php
EVALUATED_KEY="dirname($_SERVER['DOCUMENT_ROOT']);"
```

OR

```php
EVALUATED_KEY="$_SERVER['HTTP_HOST'];"
```
