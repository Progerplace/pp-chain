<?php

namespace Module\ChainElems;

require_once __DIR__ . '/../../Data.php';

use Data;
use PHPUnit\Framework\TestCase;
use Ru\Progerplace\Chain\ChainBase\Chain;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;

class ChainElemsTest extends TestCase
{
    protected Data $data;

    protected function setUp(): void
    {
        $this->data = new Data();
    }

    public function testMap()
    {
        $c = [$this->data->keysFromArrayWithKeys, $this->data->keysFromArrayWithKeys];

        $this->assertEquals(
            $this->handleCollection(
                $c,
                fn($item) => ChainFunc::map($item, fn($item) => mb_strtoupper($item))
            ),
            Chain::from($c)->elems->map(fn($item) => mb_strtoupper($item))->array
        );
    }

    public function testSort()
    {
        $ar = ["a12", "a10", "A2", "a1"];
        $c1 = [$this->data->number, $this->data->number];
        $c2 = [[3, 2, 1], [3, 2, 1]];
        $c3 = [$ar, $ar];


        $this->assertEquals(
            $this->handleCollection(
                $c1,
                fn($item) => ChainFunc::sort($item, fn(int $a, int $b) => $b - $a)
            ),
            Chain::from($c1)->elems->sort(fn(int $a, int $b) => $b - $a)->array
        );

        $this->assertEquals(
            $this->handleCollection(
                $c2,
                fn($item) => ChainFunc::$sort::asc($item)
            ),
            Chain::from($c2)->elems->sort->asc()->array
        );

        $this->assertEquals(
            $this->handleCollection(
                $c1,
                fn($item) => ChainFunc::$sort::desc($item)
            ),
            Chain::from($c1)->elems->sort->desc()->array
        );

        $this->assertEquals(
            $this->handleCollection(
                $c3,
                fn($item) => ChainFunc::$sort::natsort($item)
            ),
            Chain::from($c3)->elems->sort->natsort()->array
        );

        $this->assertEquals(
            $this->handleCollection(
                $c3,
                fn($item) => ChainFunc::$sort::natsort($item, true)
            ),
            Chain::from($c3)->elems->sort->natsort(true)->array
        );
    }

    protected function handleCollection(array $c, callable $callback): array
    {
        $res = [];

        foreach ($c as $item) {
            $res[] = $callback($item);
        }

        return $res;
    }
}