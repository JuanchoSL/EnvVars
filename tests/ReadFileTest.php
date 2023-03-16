<?php

namespace JuanchoSL\EnvVars\Tests;

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
        $this->assertEquals($_ENV['IS_BOOL'], true);
        $this->assertEquals($_ENV['IS_BOOL'], 'true');
        $this->assertEquals(getenv('IS_BOOL'), true);
        $this->assertEquals(getenv('IS_BOOL'), 'true');
    }
    
    public function testReadEmpty()
    {
        EnvVars::read(null);
        $this->assertEquals(getenv('IS_READED'), 'yes');
        $this->assertEquals($_ENV['IS_READED'], 'yes');
        $this->assertEquals($_ENV['IS_BOOL'], true);
        $this->assertEquals($_ENV['IS_BOOL'], 'true');
        $this->assertEquals(getenv('IS_BOOL'), true);
        $this->assertEquals(getenv('IS_BOOL'), 'true');
    }
}