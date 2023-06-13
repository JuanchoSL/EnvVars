<?php

namespace JuanchoSL\EnvVars;

use JuanchoSL\Exceptions\NotFoundException;

class EnvVars
{

    /**
     * Read the env file and put hit values into $_ENV superglobal in order to use it.
     * If filename is not specified, .env filename is assumed
     * @param string $filepath The path where the .env file is placed.
     * @throws \Exception
     */
    public static function read($filepath): void
    {
        if (empty($filepath)) {
            $filepath = dirname(__DIR__, 1);
        }
        if (!is_file($filepath)) {
            if (is_dir($filepath)) {
                $filepath .= DIRECTORY_SEPARATOR . '.env';
            }
        }

        if (!file_exists($filepath)) {
            throw new NotFoundException("File {$filepath} not exists");
        }
        $configs = parse_ini_file($filepath, false, INI_SCANNER_RAW);
        if (is_array($configs)) {
            foreach ($configs as $key => $value) {
                $key = trim($key);
                $value = trim($value);
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }

}