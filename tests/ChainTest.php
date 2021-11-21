<?php

require_once __DIR__ . '/../vendor-local/phpunit.phar';
require_once __DIR__ . '/../build/pp-chain@0.1.phar';
require_once __DIR__ . '/Data/DataArray.php';
require_once __DIR__ . '/Data/DataCollection.php';


use Data\DataArray;
use Data\DataCollection;
use Ru\Progerplace\Chain\Chain;
use PHPUnit\Framework\TestCase;

class ChainTest extends TestCase
{
    protected DataArray      $array;
    protected DataCollection $collection;

    protected function setUp(): void
    {
        $this->array = new DataArray();
        $this->collection = new DataCollection();
    }

    public function testFromArrayToArray()
    {
        $res = Chain::fromArray($this->array->withKeys)->array;
        $this->assertEquals($this->array->withKeys, $res);
    }

    public function testFromJson()
    {
        $res = Chain::fromJson($this->array->jsonFromArrayWithKeys)->array;
        $this->assertEquals($this->array->withKeys, $res);
    }

    public function testFromString()
    {
        $res = Chain::fromString($this->array->strFromArrayDelimiter, $this->array->strDelimiter)->array;
        $this->assertEquals($this->array->base, $res);
    }

    public function testToJson()
    {
        $res = Chain::fromArray($this->array->withKeys)->toJson();
        $this->assertEquals($this->array->jsonFromArrayWithKeys, $res);
    }

    public function testToString()
    {
        $res = Chain::fromArray($this->array->withKeys)->toString();
        $this->assertEquals($this->array->strFromArray, $res);

        $res = Chain::fromArray($this->array->withKeys)->toString($this->array->strDelimiter);
        $this->assertEquals($this->array->strFromArrayDelimiter, $res);
    }

    public function testKeys()
    {
        $res = Chain::fromArray($this->array->withKeys)->keys()->array;
        $this->assertEquals($this->array->keysFromArrayWithKeys, $res);
    }

    public function testJsonEncodeDecodeFields()
    {
        $chain = Chain::fromArray($this->array->withKeys);

        $array = $chain->json->encodeFields(['s'])->array;

        $this->assertEquals('first', $array['f']);
        $this->assertEquals('"second"', $array['s']);

        $array = $chain->json->decodeFields(['s'])->array;
        $this->assertEquals($this->array->withKeys, $array);
    }

    public function testJsonEncodeDecodeBy()
    {
        $chain1 = Chain::fromArray($this->array->withKeys);
        $chain2 = Chain::fromArray($this->array->withKeys);

        $array1 = $chain1->json->encodeBy(fn($item, $key) => $item === 'second')->array;
        $array2 = $chain2->json->encodeBy(fn($item, $key) => $key === 's')->array;

        $this->assertEquals('first', $array1['f']);
        $this->assertEquals('first', $array2['f']);
        $this->assertEquals('"second"', $array1['s']);
        $this->assertEquals('"second"', $array2['s']);

        $array1 = $chain1->json->decodeBy(fn($item, $key) => $item === '"second"')->array;
        $array2 = $chain2->json->decodeBy(fn($item, $key) => $key === 's')->array;
        $this->assertEquals($this->array->withKeys, $array1);
        $this->assertEquals($this->array->withKeys, $array2);
    }

    public function testMap()
    {
        $res = Chain::fromArray($this->array->keysFromArrayWithKeys)
            ->map(fn($item) => mb_strtoupper($item))
            ->array;
        $this->assertEquals(['F', 'S', 'T'], $res);
    }

    public function testValues()
    {
        $res = Chain::fromArray($this->array->withKeys)->values()->array;
        $this->assertEquals(['first', 'second', 'third'], $res);
    }

    public function testFilter()
    {
        $res1 = Chain::fromArray($this->array->number)->filter(fn($item, $key) => $item > 1)->array;
        $res2 = Chain::fromArray($this->array->number)->filter(fn($item, $key) => $key > 0)->array;
        $this->assertEquals([1 => 2, 2 => 3], $res1);
        $this->assertEquals([1 => 2, 2 => 3], $res2);
    }

    public function testReject()
    {
        $res1 = Chain::fromArray($this->array->number)->reject(fn($item, $key) => $item > 2)->array;
        $res2 = Chain::fromArray($this->array->number)->reject(fn($item, $key) => $key > 1)->array;

        $this->assertEquals([0 => 1, 1 => 2], $res1);
        $this->assertEquals([0 => 1, 1 => 2], $res2);
    }

    public function testSort()
    {
        $res = Chain::fromArray($this->array->number)->sort(fn(int $a, int $b) => $b - $a)->array;
        $this->assertEquals([3, 2, 1], $res);
    }

    public function testFind()
    {
        $res1 = Chain::fromArray($this->array->number)->find(fn($item, $key) => $item > 1);
        $res2 = Chain::fromArray($this->array->number)->find(fn($item, $key) => $key > 0);
        $this->assertEquals(2, $res1);
        $this->assertEquals(2, $res2);
    }

    public function testReduce()
    {
        $res1 = Chain::fromArray($this->array->number)->reduce(fn($res, $item) => $res + $item, 0, false);
        $res2 = Chain::fromArray($this->array->number)->reduce(fn($res, $item, $key) => [...$res, $item + $key]);
        $res3 = Chain::fromArray($this->array->number)->reduce(fn($res, $item, $key) => [...$res, $item + $key], [], false);
        $res4 = Chain::fromArray($this->array->number)->reduce(fn($res, $item, $key) => [...$res, $item + $key])->array;

        $this->assertEquals(6, $res1);
        $this->assertInstanceOf(Chain::class, $res2);
        $this->assertEquals([1, 3, 5], $res3);
        $this->assertEquals([1, 3, 5], $res4);
    }

    public function testReverse()
    {
        $res1 = Chain::fromArray($this->array->number)->reverse()->array;
        $res2 = Chain::fromArray($this->array->number)->reverse(true)->array;

        $this->assertEquals([3, 2, 1], $res1);
        $this->assertEquals([2 => 3, 1 => 2, 0 => 1], $res2);
    }

    public function testFillKeys()
    {
        $res1 = Chain::fromArray($this->array->number)->fillKeys(fn($item, $key) => $item + 10)->array;
        $res2 = Chain::fromArray($this->array->number)->fillKeys(fn($item, $key) => 'k_' . $key)->array;
        $res3 = Chain::fromArray($this->collection->base)->fillKeys(fn($item) => $item[0])->array;

        $this->assertEquals([11 => 1, 12 => 2, 13 => 3], $res1);
        $this->assertEquals(['k_0' => 1, 'k_1' => 2, 'k_2' => 3], $res2);
        $this->assertEquals(['first' => ['first', 'second', 'third']], $res3);
    }

    public function testFillKeysFromField()
    {
        $res1 = Chain::fromArray($this->collection->withElemsKeys)->fillKeys->fromField('f')->array;
        $this->assertEquals(['first' => ['f' => 'first', 's' => 'second', 't' => 'third']], $res1);
    }

    public function testGroup()
    {
        $res1 = Chain::fromArray($this->collection->withElemsKeys)->group(fn($item, $key) => $item['s'])->array;
        $this->assertEquals(['second' => [['f' => 'first', 's' => 'second', 't' => 'third'], ['f' => 'first', 's' => 'second', 't' => 'third'], ['f' => 'first', 's' => 'second', 't' => 'third']]], $res1);
    }

    public function testCaseKey()
    {
        $res1 = Chain::fromArray($this->array->snakeCase)->caseKey->snakeToCamel()->array;
        $res2 = Chain::fromArray($this->array->snakeCase)->caseKey->camelToSnake()->array;
        $res3 = Chain::fromArray($this->array->withKeys)->caseKey->toUpper()->array;
        $res4 = Chain::fromArray($this->array->withKeysUpper)->caseKey->toLower()->array;

        $this->assertEquals($this->array->camelCase, $res1);
        $this->assertEquals($this->array->snakeCase, $res2);
        $this->assertEquals($this->array->withKeysUpper, $res3);
        $this->assertEquals($this->array->withKeys, $res4);
    }

    public function testCount()
    {
        $this->assertEquals(3, Chain::fromArray($this->array->base)->count());
    }

    public function testIsEmpty()
    {
        $this->assertTrue(Chain::fromArray([])->isEmpty());
        $this->assertFalse(Chain::fromArray([1, 2])->isEmpty());
    }
}