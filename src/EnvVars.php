<?php
declare(strict_types=1);

namespace JuanchoSL\EnvVars;

use JuanchoSL\Exceptions\DestinationUnreachableException;
use JuanchoSL\Exceptions\NotFoundException;

class EnvVars
{
    const OPTION_PROCESS_SECTIONS = 'process_section';

    protected static EnvVars $instance;

    protected string $filepath;
    protected iterable $options = [
        self::OPTION_PROCESS_SECTIONS => false
    ];

    /**
     * @var array<string,string> $temporal_array
     */
    protected array $temporal_array = [];

    protected function __construct(string $filepath, iterable $options = [])
    {
        foreach ($options as $option => $value) {
            if (array_key_exists($option, $this->options)) {
                $this->options[$option] = $value;
            }
        }
        $this->parseFile($filepath);
    }

    /**
     * Read the env file and put his values into $_ENV superglobal in order to use it.
     * If filename is not specified, .env filename is assumed
     * @param string $filepath The path where the .env file is placed.
     * @param iterable $options Define configurations for parse ini file, use EnvVars::OPTION_* constants
     * @return EnvVars A new EnvVars instance
     */
    public static function init(string $filepath, iterable $options = []): EnvVars
    {
        return self::$instance = new EnvVars($filepath, $options);
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
        $configs = parse_ini_file($this->filepath, $this->options[static::OPTION_PROCESS_SECTIONS], INI_SCANNER_RAW);
        if (is_array($configs)) {
            $configs = $this->mergeSections($configs);
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

    protected function mergeSections(iterable $parts, string $preppend = '')
    {
        foreach ($parts as $key => $part) {
            unset($parts[$key]);
            if (is_array($part)) {
                $parts = array_merge($parts, $this->mergeSections($part, $key));
            } else {
                if (!empty($preppend)) {
                    $key = $preppend . '_' . $key;
                }
                $parts[$key] = $part;
            }
        }
        return $parts;
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
        eval ("\$return={$string}");
        return $return;
    }

}