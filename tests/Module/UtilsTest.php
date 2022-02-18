<?php

namespace Module;

use PHPUnit\Framework\TestCase;
use Ru\Progerplace\Chain\Utils;

class UtilsTest extends TestCase
{
    public function testArgumentsAsArray()
    {
        $this->assertEquals(['a'], Utils::argumentsAsArray(['a']));
        $this->assertEquals(['a', 'b', 'c'], Utils::argumentsAsArray(['a', 'b', 'c']));
        $this->assertEquals(['a', 'b', 'c'], Utils::argumentsAsArray([['a', 'b'], 'c']));
    }
}