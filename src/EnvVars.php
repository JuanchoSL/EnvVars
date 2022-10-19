<?php

namespace JuanchoSL\EnvVars;

class EnvVars
{

    public static function read($filepath)
    {
        if (!is_file($filepath) || !file_exists($filepath)) {
            throw new \Exception("File {$filepath} not exists", 404);
        }
        $configs = parse_ini_file($filepath, false);
        foreach ($configs as $key => $value) {
            $key = trim($key);
            $value = trim($value);
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }

}
