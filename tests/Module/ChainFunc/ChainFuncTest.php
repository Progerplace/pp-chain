<?php

namespace Module\ChainFunc;

require_once __DIR__ . '/../../Data.php';

use Data;
use PHPUnit\Framework\TestCase;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;

class ChainFuncTest extends TestCase
{
    protected Data $data;

    protected function setUp(): void
    {
        $this->data = new Data();
    }

    public function testMap()
    {
        $this->assertEquals(
            ['F', 'S', 'T'],
            ChainFunc::map($this->data->keysFromArrayWithKeys, fn($item) => mb_strtoupper($item))
        );
    }

    public function testValues()
    {
        $this->assertEquals(
            ['first', 'second', 'third'],
            ChainFunc::values($this->data->withKeys)
        );
    }

    public function testColumn()
    {
        $c = [$this->data->withKeys, $this->data->withKeys];

        $this->assertEquals(
            ['second', 'second'],
            ChainFunc::column($c, 's')
        );

        $this->assertEquals(
            ['first' => 'second'],
            ChainFunc::column([$this->data->withKeys], 's', 'f')
        );
    }

    public function testUnique()
    {
        $this->assertEquals(
            [0 => 1, 1 => 2, 3 => 3],
            ChainFunc::unique([1, 2, 2, 3])
        );
    }

    public function testReverse()
    {
        $this->assertEquals(
            [2 => 3, 1 => 2, 0 => 1],
            ChainFunc::unique([1, 2, 3])
        );
    }

    public function testCount()
    {
        $this->assertEquals(
            3,
            ChainFunc::count($this->data->base));
    }

    public function testIsEmpty()
    {
        $this->assertTrue(ChainFunc::isEmpty([]));
        $this->assertFalse(ChainFunc::isEmpty([1, 2]));
    }

    public function testFilter()
    {
        $this->assertEquals(
            [1 => 2, 2 => 3],
            ChainFunc::filter($this->data->number, fn($item, $key) => $item > 1),
        );

        $this->assertEquals(
            [1 => 2, 2 => 3],
            ChainFunc::filter($this->data->number, fn($item, $key) => $key > 0)
        );
    }

    public function testReject()
    {
        $this->assertEquals(
            [0 => 1, 1 => 2],
            ChainFunc::reject($this->data->number, fn($item, $key) => $item > 2)
        );

        $this->assertEquals(
            [0 => 1, 1 => 2],
            ChainFunc::reject($this->data->number, fn($item, $key) => $key > 1)
        );
    }

    public function testSort()
    {
        $ar = ["a12", "a10", "A2", "a1"];

        $this->assertEquals(
            [3, 2, 1],
            ChainFunc::sort($this->data->number, fn(int $a, int $b) => $b - $a)
        );

        $this->assertEquals(
            [1, 2, 3],
            ChainFunc::$sort::asc([3, 2, 1])
        );

        $this->assertEquals(
            [3, 2, 1],
            ChainFunc::$sort::desc($this->data->number)
        );

        $this->assertEquals(
            [3 => 'a1', 2 => 'A2', 1 => 'a10', 0 => 'a12'],
            ChainFunc::$sort::natsort($ar)
        );

        $this->assertEquals(
            [2 => 'A2', 3 => 'a1', 1 => 'a10', 0 => 'a12'],
            ChainFunc::$sort::natsort($ar, true)
        );

    }

    public function testFind()
    {
        $this->assertEquals(
            2,
            ChainFunc::find($this->data->number, fn($item, $key) => $item > 1)
        );

        $this->assertEquals(
            2,
            ChainFunc::find($this->data->number, fn($item, $key) => $key > 0)
        );
    }

    public function testReduce()
    {
        $this->assertEquals(
            6,
            ChainFunc::reduce($this->data->number, fn($res, $item) => $res + $item, 0)
        );

        $this->assertEquals(
            9,
            ChainFunc::reduce($this->data->number, fn($res, $item, $key) => $res + $item + $key, 0)
        );
    }

    public function testFillKeys()
    {
        $this->assertEquals(
            [11 => 1, 12 => 2, 13 => 3],
            ChainFunc::fillKeys($this->data->number, fn($item, $key) => $item + 10)
        );

        $this->assertEquals(
            ['k_0' => 1, 'k_1' => 2, 'k_2' => 3],
            ChainFunc::fillKeys($this->data->number, fn($item, $key) => 'k_' . $key)
        );
    }

    public function testFillKeysFromField()
    {
        $c = [$this->data->withKeys];

        $this->assertEquals(
            ['first' => ['f' => 'first', 's' => 'second', 't' => 'third']],
            ChainFunc::$fillKeys::fromField($c, 'f')
        );
    }

    public function testGroup()
    {
        $c = [$this->data->withKeys, $this->data->withKeys, $this->data->withKeys];

        $this->assertEquals(
            [
                'second' => [$this->data->withKeys, $this->data->withKeys, $this->data->withKeys]
            ],
            ChainFunc::group($c, fn($item, $key) => $item['s'])
        );
    }

    public function testCaseKey()
    {
        $this->assertEquals(
            $this->data->camelCase,
            ChainFunc::$caseKey::snakeToCamel($this->data->snakeCase)
        );

        $this->assertEquals(
            $this->data->snakeCase,
            ChainFunc::$caseKey::camelToSnake($this->data->snakeCase)
        );

        $this->assertEquals(
            $this->data->withKeysUpper,
            ChainFunc::$caseKey::toUpper($this->data->withKeys)
        );

        $this->assertEquals(
            $this->data->withKeys,
            ChainFunc::$caseKey::toLower($this->data->withKeysUpper)
        );
    }

    public function testJsonEncodeDecodeFields()
    {
        $encoded = ChainFunc::$json::encodeFields($this->data->withKeys, 's');

        $this->assertEquals(
            ['f' => 'first', 's' => '"second"', 't' => 'third'],
            $encoded
        );

        $this->assertEquals(
            $this->data->withKeys,
            ChainFunc::$json::decodeFields($encoded, 's')
        );
    }

    public function testJsonEncodeDecodeBy()
    {
        $encoded1 = ChainFunc::$json::encodeBy($this->data->withKeys, fn($item, $key) => $item === 'second');
        $encoded2 = ChainFunc::$json::encodeBy($this->data->withKeys, fn($item, $key) => $key === 's');

        $this->assertEquals(
            ['f' => 'first', 's' => '"second"', 't' => 'third'],
            $encoded1
        );

        $this->assertEquals(
            ['f' => 'first', 's' => '"second"', 't' => 'third'],
            $encoded2
        );


        $this->assertEquals(
            $this->data->withKeys,
            ChainFunc::$json::decodeBy($encoded1, fn($item, $key) => $item === '"second"')
        );

        $this->assertEquals(
            $this->data->withKeys,
            ChainFunc::$json::decodeBy($encoded2, fn($item, $key) => $key === 's')
        );
    }
}