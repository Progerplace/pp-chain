<?php

require_once __DIR__ . '/../vendor-local/phpunit.phar';

use Ru\Progerplace\Chain\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testArgumentsAsArray()
    {
        $this->assertEquals(['a'], Utils::argumentsAsArray(['a']));
        $this->assertEquals(['a', 'b', 'c'], Utils::argumentsAsArray(['a', 'b', 'c']));
        $this->assertEquals(['a', 'b', 'c'], Utils::argumentsAsArray([['a', 'b'], 'c']));
    }
}