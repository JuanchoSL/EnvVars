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
        EnvVars::read(realpath(dirname(__DIR__, 1)) . DIRECTORY_SEPARATOR . '.env.local');
    }

    public function testReadFile()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)) . DIRECTORY_SEPARATOR . '.env');
        $this->assertEquals(getenv('IS_READED'), 'yes');
        $this->assertEquals($_ENV['IS_READED'], 'yes');

        $this->assertEquals(getenv('IS_BOOL'), true);
        $this->assertEquals(getenv('IS_BOOL'), 'true');
        $this->assertEquals($_ENV['IS_BOOL'], true);
        $this->assertEquals($_ENV['IS_BOOL'], 'true');
    }

    public function testReadDir()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(getenv('IS_READED'), 'yes');
        $this->assertEquals($_ENV['IS_READED'], 'yes');

        $this->assertEquals(getenv('IS_BOOL'), true);
        $this->assertEquals(getenv('IS_BOOL'), 'true');
        $this->assertEquals($_ENV['IS_BOOL'], true);
        $this->assertEquals($_ENV['IS_BOOL'], 'true');
    }

    public function testReadEmpty()
    {
        $this->expectException(DestinationUnreachableException::class);
        EnvVars::read('');
        $this->assertEquals(getenv('IS_READED'), 'yes');
        $this->assertEquals($_ENV['IS_READED'], 'yes');

        $this->assertEquals(getenv('IS_BOOL'), true);
        $this->assertEquals(getenv('IS_BOOL'), 'true');
        $this->assertEquals($_ENV['IS_BOOL'], true);
        $this->assertEquals($_ENV['IS_BOOL'], 'true');
    }

    public function testReadString()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $this->assertEquals('string', getenv('NOEVAL'));
        $this->assertEquals('string', $_ENV['NOEVAL']);
    }

    public function testReadExpression()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(dirname(__DIR__, 1), getenv('EVAL'));
        $this->assertEquals(dirname(__DIR__, 1), $_ENV['EVAL']);
    }

    public function testReadVariable()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(dirname(__DIR__, 1), getenv('VAR'));
        $this->assertEquals(dirname(__DIR__, 1), $_ENV['VAR']);
    }

    public function testReadComplex()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), getenv('COMPLEX'));
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), $_ENV['COMPLEX']);
    }
    
    public function testReadComplexExternalVar(){
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $this->assertEquals($_SERVER['TMP'], getenv('TMP'));
        $this->assertEquals($_SERVER['TMP'], $_ENV['TMP']);
    }

    public function testEvalString()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $response = EnvVars::eval('NOEVAL');
        $this->assertEquals('string', $response);
        $this->assertEquals('string', getenv('NOEVAL'));
        $this->assertEquals('string', $_ENV['NOEVAL']);
    }

    public function testEvalExpression()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $response = EnvVars::eval('EVAL');
        $this->assertEquals(dirname(__DIR__, 1), $response);
        $this->assertEquals(dirname(__DIR__, 1), getenv('EVAL'));
        $this->assertEquals(dirname(__DIR__, 1), $_ENV['EVAL']);
    }

    public function testEvalVariable()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $response = EnvVars::eval('VAR');
        $this->assertEquals(dirname(__DIR__, 1), $response);
        $this->assertEquals(dirname(__DIR__, 1), getenv('VAR'));
        $this->assertEquals(dirname(__DIR__, 1), $_ENV['VAR']);
    }

    public function testEvalComplex()
    {
        EnvVars::read(realpath(dirname(__DIR__, 1)));
        $response = EnvVars::eval('COMPLEX');
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), $response);
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), getenv('COMPLEX'));
        $this->assertEquals(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . getenv('DIR'), $_ENV['COMPLEX']);
    }
 
}