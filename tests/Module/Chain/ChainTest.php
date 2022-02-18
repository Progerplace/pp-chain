<?php

namespace Module\Chain;

require_once __DIR__ . '/../../Data.php';

use Data;
use PHPUnit\Framework\TestCase;
use Ru\Progerplace\Chain\ChainBase\Chain;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;

class ChainTest extends TestCase
{
    protected Data $data;

    protected function setUp(): void
    {
        $this->data = new Data();
    }

    public function testFromArrayToArray()
    {
        $this->assertEquals(
            $this->data->withKeys,
            Chain::from($this->data->withKeys)->array
        );

        $this->assertEquals(
            [],
            Chain::from(null)->array
        );

        $this->assertEquals(
            $this->data->number,
            Chain::from(null, $this->data->number)->array
        );
    }

    public function testFromJson()
    {
        $res = Chain::fromJson($this->data->jsonFromArrayWithKeys)->array;
        $this->assertEquals($this->data->withKeys, $res);
    }

    public function testFromString()
    {
        $res = Chain::fromString($this->data->strFromArrayDelimiter, $this->data->strDelimiter)->array;
        $this->assertEquals($this->data->base, $res);
    }

    public function testToJson()
    {
        $res = Chain::from($this->data->withKeys)->toJson();
        $this->assertEquals($this->data->jsonFromArrayWithKeys, $res);
    }

    public function testToString()
    {
        $res = Chain::from($this->data->withKeys)->toString();
        $this->assertEquals($this->data->strFromArray, $res);

        $res = Chain::from($this->data->withKeys)->toString($this->data->strDelimiter);
        $this->assertEquals($this->data->strFromArrayDelimiter, $res);
    }

    public function testMap()
    {
        $this->assertEquals(
            ChainFunc::map($this->data->keysFromArrayWithKeys, fn($item) => mb_strtoupper($item)),
            Chain::from($this->data->keysFromArrayWithKeys)->map(fn($item) => mb_strtoupper($item))->array
        );
    }

    public function testSort()
    {
        $ar = ["a12", "a10", "A2", "a1"];

        $this->assertEquals(
            ChainFunc::sort($this->data->number, fn(int $a, int $b) => $b - $a),
            Chain::from($this->data->number)->sort(fn(int $a, int $b) => $b - $a)->array
        );

        $this->assertEquals(
            ChainFunc::$sort::asc([3, 2, 1]),
            Chain::from([3, 2, 1])->sort->asc()->array
        );

        $this->assertEquals(
            ChainFunc::$sort::desc($this->data->number),
            Chain::from($this->data->number)->sort->desc()->array
        );

        $this->assertEquals(
            ChainFunc::$sort::natsort($ar),
            Chain::from($ar)->sort->natsort()->array
        );

        $this->assertEquals(
            ChainFunc::$sort::natsort($ar, true),
            Chain::from($ar)->sort->natsort(true)->array
        );
    }
}