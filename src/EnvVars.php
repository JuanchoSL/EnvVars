<?php

declare(strict_types=1);

namespace JuanchoSL\EnvVars;

use JuanchoSL\Exceptions\DestinationUnreachableException;
use JuanchoSL\Exceptions\NotFoundException;

class EnvVars
{

    protected static EnvVars $instance;

    protected string $filepath;

    /**
     * @var array<string,string> $temporal_array
     */
    protected array $temporal_array = [];

    protected function __construct(string $filepath)
    {
        $this->parseFile($filepath);
    }

    /**
     * Read the env file and put his values into $_ENV superglobal in order to use it.
     * If filename is not specified, .env filename is assumed
     * @param string $filepath The path where the .env file is placed.
     * @return EnvVars A new EnvVars instance
     */
    public static function init(string $filepath): EnvVars
    {
        return self::$instance = new EnvVars($filepath);
    }

    public static function read(string $filepath): void
    {
        trigger_error("The read starter method is deprecated, use init instead", E_USER_DEPRECATED);
        self::init($filepath);
    }

    /**
     * Method in order to add more config files to environment scope
     * @param string $filepath Full path to the file to parse
     */
    public function parseFile(string $filepath): void
    {
        $this->filepath = $filepath;
        $this->errorControl();
        $this->processContent();
    }

    protected function errorControl(): void
    {
        if (empty($this->filepath)) {
            throw new DestinationUnreachableException("Filepath for ENV file can not be empty");
        }

        if (!is_file($this->filepath)) {
            if (is_dir($this->filepath)) {
                if (substr($this->filepath, -1) != DIRECTORY_SEPARATOR) {
                    $this->filepath .= DIRECTORY_SEPARATOR;
                }
                $this->filepath .= '.env';
            }
        }

        $filepath = realpath($this->filepath);
        if (empty($filepath)) {
            throw new NotFoundException("File {$this->filepath} does not exists");
        }
        $this->filepath = $filepath;
    }

    protected function processContent(): void
    {
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

    protected function isWithVars(string $value): bool
    {
        $temporal_array = [];
        preg_match_all('/\$\{(\w+)\}/', $value, $temporal_array);
        return !empty($temporal_array);
    }

    protected function replaceWithVars(string $key): void
    {
        $temporal_array = [];
        preg_match_all('/\$\{(\w+)\}/', $this->temporal_array[$key], $temporal_array);
        if (!empty($temporal_array)) {
            foreach ($temporal_array[1] as $variable) {
                $old_var_content = getenv($variable) ? getenv($variable) : '';
                $this->temporal_array[$key] = str_replace('${' . $variable . '}', $old_var_content, $this->temporal_array[$key]);
            }
        }
    }

    protected function isEvaluable(string $value): bool
    {
        return (substr($value, -1) == ';');
    }

    protected function evaluate(string $key): void
    {
        $this->temporal_array[$key] = EnvVars::eval($this->temporal_array[$key]) ?? '';
    }

    protected function setVar(string $key, string $value): void
    {
        $key = trim($key);
        $value = trim($value);
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
    }

    /**
     * Evaluate if the content of a string is a php expresion and return the processed content
     * @param string $string The string to be evaluated
     * @return string|null The evaluated string or null if fail or not exists
     */
    public static function eval(string $string): ?string
    {
        if (substr($string, -1) != ';') {
            $string .= ";";
        }
        $return = null;
        eval("\$return={$string}");
        return $return;
    }

}