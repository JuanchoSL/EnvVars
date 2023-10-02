<?php declare(strict_types=1);

namespace JuanchoSL\EnvVars;

use JuanchoSL\Exceptions\DestinationUnreachableException;
use JuanchoSL\Exceptions\NotFoundException;

class EnvVars
{

    protected static EnvVars $instance;
    protected string $filepath;

    protected array $temporal_array = [];
    protected function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->errorControl();
        $configs = parse_ini_file($this->filepath, false, INI_SCANNER_RAW);
        if (is_array($configs)) {
            foreach ($configs as $key => $value) {
                if ($this->isWithVars($value) || $this->isEvaluable($value)) {
                    $this->temporal_array[$key] = $value;
                } else {
                    $this->setVar($key, $value);
                }
            }
        }
        if (!empty($this->temporal_array)) {
            foreach ($this->temporal_array as $key => $value) {
                if ($this->isWithVars($value)) {
                    $this->replaceWithVars($key);
                }
                if ($this->isEvaluable($value)) {
                    $this->evaluate($key);
                }
                $this->setVar($key, $this->temporal_array[$key]);
                unset($this->temporal_array[$key]);
            }
        }
    }

    protected function isEvaluable(string $value)
    {
        return (substr($value, -1) == ';');
    }

    protected function evaluate(string $key)
    {
        $this->temporal_array[$key] = eval("return {$this->temporal_array[$key]}");
    }

    protected function isWithVars(string $value)
    {
        $temporal_array = [];
        preg_match_all('/\$\{(\w+)\}/', $value, $temporal_array);
        return !empty($temporal_array);
    }

    protected function replaceWithVars(string $key)
    {
        $temporal_array = [];
        preg_match_all('/\$\{(\w+)\}/', $this->temporal_array[$key], $temporal_array);
        if (!empty($temporal_array)) {
            foreach ($temporal_array[1] as $variable) {
                $this->temporal_array[$key] = str_replace('${' . $variable . '}', getenv($variable), $this->temporal_array[$key]);
            }
        }
    }
    /**
     * Read the env file and put his values into $_ENV superglobal in order to use it.
     * If filename is not specified, .env filename is assumed
     * @param string $filepath The path where the .env file is placed.
     * @throws \Exception
     */
    public static function read(string $filepath): void
    {
        self::$instance = new EnvVars($filepath);
        return;

        if (empty($filepath)) {
            throw new DestinationUnreachableException("Filepath for ENV file can not be empty");
        }

        if (!is_file($filepath)) {
            if (is_dir($filepath)) {
                $filepath .= DIRECTORY_SEPARATOR . '.env';
            }
        }

        if (!file_exists($filepath)) {
            throw new NotFoundException("File {$filepath} does not exists");
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

    public static function init(string $filepath): void
    {
        if (empty(self::$instance)) {
        }
        self::$instance = new EnvVars($filepath);
    }

    /**
     * Evaluate if the content of a ENV var is a php expresion and return the processed content
     * @param string $env_key The ENV name to be evaluated
     * @return string|null The evaluated string or null if fail or not exists
     */
    public static function eval(string $env_key): ?string
    {
        $result = null;
        $var = getenv($env_key);
        if (substr($var, -1) == ';') {
            eval("\$result=$var");
        } else {
            $result = $var;
        }
        return $result;
    }

    protected function setVar($key, $value)
    {
        $key = trim($key);
        $value = trim($value);
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
    }

    protected function errorControl()
    {
        if (empty($this->filepath)) {
            throw new DestinationUnreachableException("Filepath for ENV file can not be empty");
        }

        if (!is_file($this->filepath)) {
            if (is_dir($this->filepath)) {
                $this->filepath .= DIRECTORY_SEPARATOR . '.env';
            }
        }

        if (!file_exists($this->filepath)) {
            throw new NotFoundException("File {$this->filepath} does not exists");
        }
    }
}