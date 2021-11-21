<?php

require_once __DIR__ . '/../vendor-local/phpunit.phar';
require_once __DIR__ . '/../build/pp-chain@0.1.phar';
require_once __DIR__ . '/Data/DataArray.php';
require_once __DIR__ . '/Data/DataCollection.php';

use Data\DataArray;
use Data\DataCollection;
use Ru\Progerplace\Chain\Chain;
use PHPUnit\Framework\TestCase;

class ChainElemsTest extends TestCase
{
    protected DataArray      $array;
    protected DataCollection $collection;

    protected function setUp(): void
    {
        $this->array = new DataArray();
        $this->collection = new DataCollection();
    }

    public function testKeys()
    {
        $res = Chain::fromArray($this->collection->withElemsKeys)
            ->elems->keys()
            ->array;
        $this->assertEquals($this->collection->keysFromArrayWithKeys, $res);
    }

    public function testJsonEncodeDecodeFields()
    {
        $chain = Chain::fromArray($this->collection->withElemsKeys);

        $array = $chain->elems->json->encodeFields(['s'])->array;
        $this->assertEquals('first', $array[0]['f']);
        $this->assertEquals('"second"', $array[0]['s']);
        $this->assertEquals('first', $array[1]['f']);
        $this->assertEquals('"second"', $array[1]['s']);

        $array = $chain->elems->json->decodeFields(['s'])->array;
        $this->assertEquals($this->collection->withElemsKeys, $array);
    }

    public function testJsonEncodeDecodeBy()
    {
        $chain1 = Chain::fromArray($this->collection->withElemsKeys);
        $chain2 = Chain::fromArray($this->collection->withElemsKeys);

        $array1 = $chain1->elems->json->encodeBy(fn($item, $key) => $item === 'second')->array;
        $array2 = $chain2->elems->json->encodeBy(fn($item, $key) => $key === 's')->array;
        $this->assertEquals('first', $array1[0]['f']);
        $this->assertEquals('"second"', $array1[0]['s']);
        $this->assertEquals('first', $array1[1]['f']);
        $this->assertEquals('"second"', $array1[1]['s']);

        $this->assertEquals('first', $array2[0]['f']);
        $this->assertEquals('"second"', $array2[0]['s']);
        $this->assertEquals('first', $array2[1]['f']);
        $this->assertEquals('"second"', $array2[1]['s']);


        $array1 = $chain1->elems->json->decodeBy(fn($item, $key) => $item === '"second"')->array;
        $array2 = $chain2->elems->json->decodeBy(fn($item, $key) => $key === 's')->array;
        $this->assertEquals($this->collection->withElemsKeys, $array1);
        $this->assertEquals($this->collection->withElemsKeys, $array2);
    }

    public function testMap()
    {
        $res = Chain::fromArray($this->collection->keysFromArrayWithKeys)
            ->elems->map(fn($item) => mb_strtoupper($item))
            ->array;

        $this->assertEquals([['F', 'S', 'T'], ['F', 'S', 'T'], ['F', 'S', 'T']], $res);
    }

    public function testValues()
    {
        $res = Chain::fromArray($this->collection->withElemsKeys)
            ->elems->values()
            ->array;
        $this->assertEquals([['first', 'second', 'third'], ['first', 'second', 'third'], ['first', 'second', 'third']], $res);
    }

    public function testFilter()
    {
        $res1 = Chain::fromArray($this->collection->number)->elems->filter(fn($item, $key) => $item > 1)->array;
        $res2 = Chain::fromArray($this->collection->withElemsKeys)->elems->filter(fn($item, $key) => $key === 's')->array;

        $this->assertEquals([[1 => 2, 2 => 3], [1 => 2, 2 => 3], [1 => 2, 2 => 3]], $res1);
        $this->assertEquals([['s' => 'second'], ['s' => 'second'], ['s' => 'second']], $res2);
    }

    public function testReject()
    {
        $res1 = Chain::fromArray($this->collection->number)->elems->reject(fn($item, $key) => $item > 2)->array;
        $res2 = Chain::fromArray($this->collection->withElemsKeys)->elems->reject(fn($item, $key) => $key === 'f' || $key === 't')->array;

        $this->assertEquals([[0 => 1, 1 => 2], [0 => 1, 1 => 2], [0 => 1, 1 => 2]], $res1);
        $this->assertEquals([['s' => 'second'], ['s' => 'second'], ['s' => 'second']], $res2);
    }

    public function testSort()
    {
        $res = Chain::fromArray($this->collection->number)->elems->sort(fn(int $a, int $b) => $b - $a)->array;
        $this->assertEquals([[3, 2, 1], [3, 2, 1], [3, 2, 1]], $res);
    }

    public function testFind()
    {
        $res1 = Chain::fromArray($this->collection->number)->elems->find(fn($item, $key) => $item > 1)->array;
        $res2 = Chain::fromArray($this->collection->withElemsKeys)->elems->find(fn($item, $key) => $key === 's')->array;

        $this->assertEquals([2, 2, 2], $res1);
        $this->assertEquals(['second', 'second', 'second'], $res2);
    }

    public function testReduce()
    {
        $res1 = Chain::fromArray($this->collection->number)->elems->reduce(fn($res, $item) => $res + $item, 0)->array;
        $res2 = Chain::fromArray($this->collection->number)->elems->reduce(fn($res, $item, $key) => [...$res, $item + $key]);
        $res3 = Chain::fromArray($this->collection->number)->elems->reduce(fn($res, $item, $key) => [...$res, $item + $key])->array;

        $this->assertEquals([6, 6, 6], $res1);
        $this->assertInstanceOf(Chain::class, $res2);
        $this->assertEquals([[1, 3, 5], [1, 3, 5], [1, 3, 5]], $res3);
    }

    public function testReverse()
    {
        $array = [$this->array->number, $this->array->number];
        $res1 = Chain::fromArray($array)->elems->reverse()->array;
        $res2 = Chain::fromArray($array)->elems->reverse(true)->array;

        $this->assertEquals([[3, 2, 1], [3, 2, 1]], $res1);
        $this->assertEquals([[2 => 3, 1 => 2, 0 => 1], [2 => 3, 1 => 2, 0 => 1]], $res2);
    }

    public function testFillKeys()
    {
        $res1 = Chain::fromArray($this->collection->number)->elems->fillKeys(fn($item, $key) => $item + 10)->array;
        $res2 = Chain::fromArray($this->collection->number)->elems->fillKeys(fn($item, $key) => 'k_' . $key)->array;

        $this->assertEquals([[11 => 1, 12 => 2, 13 => 3], [11 => 1, 12 => 2, 13 => 3], [11 => 1, 12 => 2, 13 => 3]], $res1);
        $this->assertEquals([['k_0' => 1, 'k_1' => 2, 'k_2' => 3], ['k_0' => 1, 'k_1' => 2, 'k_2' => 3], ['k_0' => 1, 'k_1' => 2, 'k_2' => 3]], $res2);
    }

    public function testCaseKey()
    {
        $snake = [$this->array->snakeCase, $this->array->snakeCase];
        $camel = [$this->array->camelCase, $this->array->camelCase];

        $res1 = Chain::fromArray($snake)->elems->caseKey->snakeToCamel()->array;
        $res2 = Chain::fromArray($snake)->elems->caseKey->camelToSnake()->array;

        $this->assertEquals($camel, $res1);
        $this->assertEquals($snake, $res2);
    }

    public function testCount()
    {
        $this->assertEquals([3, 3, 3], Chain::fromArray($this->collection->base)->elems->count()->array);
    }

    public function testIsEmpty()
    {
        $res = Chain::fromArray([[], [1, 2]])->elems->isEmpty()->array;
        $this->assertEquals([true, false], $res);
    }
}