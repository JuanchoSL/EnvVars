<?php

namespace JuanchoSL\EnvVars\Tests;

use JuanchoSL\Exceptions\DestinationUnreachableException;
use PHPUnit\Framework\TestCase;
use JuanchoSL\EnvVars\EnvVars;
use JuanchoSL\Exceptions\NotFoundException;

class ReadFileTest extends TestCase
{

    public function testNoReadFile()
    {
        $this->expectException(NotFoundException::class);
        EnvVars::init(realpath(dirname(__DIR__, 1)) . DIRECTORY_SEPARATOR . '.env.local');
    }

    public function testNoReadDir()
    {
        $this->expectException(DestinationUnreachableException::class);
        EnvVars::init(realpath(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'invalid_directory'));
    }

    public function testReadEmpty()
    {
        $this->expectException(DestinationUnreachableException::class);
        EnvVars::init('');
    }

    public function testReadFile()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)) . DIRECTORY_SEPARATOR . '.env');
        $this->assertEquals(getenv('IS_READED'), 'yes');
        $this->assertEquals($_ENV['IS_READED'], 'yes');

        $this->assertEquals(getenv('IS_BOOL'), true);
        $this->assertEquals(getenv('IS_BOOL'), 'true');
        $this->assertEquals($_ENV['IS_BOOL'], true);
        $this->assertEquals($_ENV['IS_BOOL'], 'true');
    }

    public function testReadDir()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(getenv('IS_READED'), 'yes');
        $this->assertEquals($_ENV['IS_READED'], 'yes');

        $this->assertEquals(getenv('IS_BOOL'), true);
        $this->assertEquals(getenv('IS_BOOL'), 'true');
        $this->assertEquals($_ENV['IS_BOOL'], true);
        $this->assertEquals($_ENV['IS_BOOL'], 'true');
    }

    public function testReadString()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $this->assertEquals('string', getenv('NOEVAL'));
        $this->assertEquals('string', $_ENV['NOEVAL']);
    }

    public function testReadExpression()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(dirname(__DIR__, 1), getenv('EVAL'));
        $this->assertEquals(dirname(__DIR__, 1), $_ENV['EVAL']);
    }

    public function testReadVariable()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(dirname(__DIR__, 1), getenv('VAR'));
        $this->assertEquals(dirname(__DIR__, 1), $_ENV['VAR']);
    }

    public function testReadComplex()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), getenv('COMPLEX'));
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), $_ENV['COMPLEX']);
    }

    public function testReadComplexExternalVar()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(sys_get_temp_dir(), getenv('TMP'));
        $this->assertEquals(sys_get_temp_dir(), $_ENV['TMP']);
    }

    public function testEvalString()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $response = EnvVars::eval('sys_get_temp_dir()');
        $this->assertEquals(sys_get_temp_dir(), $response);
        $this->assertEquals(sys_get_temp_dir(), getenv('TMP'));
        $this->assertEquals(sys_get_temp_dir(), $_ENV['TMP']);
    }

    public function testEvalExpression()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $response = EnvVars::eval('dirname(__DIR__, 1)');
        $this->assertEquals(dirname(__DIR__, 1), $response);
        $this->assertEquals(dirname(__DIR__, 1), getenv('EVAL'));
        $this->assertEquals(dirname(__DIR__, 1), $_ENV['EVAL']);
    }

    public function testEvalVariable()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $response = EnvVars::eval('dirname(__DIR__, 1)');
        $this->assertEquals(dirname(__DIR__, 1), $response);
        $this->assertEquals(dirname(__DIR__, 1), getenv('VAR'));
        $this->assertEquals(dirname(__DIR__, 1), $_ENV['VAR']);
    }

    public function testEvalComplex()
    {
        EnvVars::init(realpath(dirname(__DIR__, 1)));
        $response = EnvVars::eval("dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR')");
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), $response);
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), getenv('COMPLEX'));
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), $_ENV['COMPLEX']);
    }

}