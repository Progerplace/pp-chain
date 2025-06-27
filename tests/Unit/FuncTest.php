<?php
/** @noinspection PhpParamsInspection */

namespace Unit;

use Operations;
use PHPUnit\Framework\TestCase;
use Ru\Progerplace\Chain\Exception\NotFoundException;
use Ru\Progerplace\Chain\Func;
use stdClass;

class FuncTest extends TestCase
{
    use Operations;

    protected array  $simple       = [1, 2];
    protected string $simpleJson   = '{1,2}';
    protected string $simpleString = '1,2';

    protected array  $flat       = ['a' => 1, 'b' => 2];
    protected string $flatJson   = '{"a":1,"b":2}';
    protected string $flatString = '1,2';

    protected array $nested = [
        ['a' => 1, 'b' => 2],
        ['a' => 10, 'b' => 11],
        ['a' => 20, 'b' => 21],
    ];

    protected array $case = [
        'camel'       => ['varFirst' => 1, 'varSecond' => 2, 'entityId' => 3],
        'paskal'      => ['VarFirst' => 1, 'VarSecond' => 2, 'EntityId' => 3],
        'snake'       => ['var_first' => 1, 'var_second' => 2, 'entity_id' => 3],
        'kebab'       => ['var-first' => 1, 'var-second' => 2, 'entity-id' => 3],
        'screamSnake' => ['VAR_FIRST' => 1, 'VAR_SECOND' => 2, 'ENTITY_ID' => 3],
        'screamKebab' => ['VAR-FIRST' => 1, 'VAR-SECOND' => 2, 'ENTITY-ID' => 3],
    ];

    public function testMap(): void
    {
        $this->assertEquals(
            ['a' => 'a1', 'b' => 'b2'],
            Func::map($this->flat, fn(int $item, string $key) => $key . $item)
        );
    }

    public function testReject(): void
    {
        $this->assertEquals(
            [
                1 => ['a' => 10, 'b' => 11],
            ],
            Func::reject($this->nested, fn(array $item, int $key) => $item['a'] === 1 || $key === 2)
        );
    }

    public function testRejectNull(): void
    {
        $this->assertEquals(
            [1 => 1, 2 => ""],
            Func::rejectNull([null, 1, ""])
        );
    }

    public function testRejectEmpty(): void
    {
        $this->assertEquals(
            [1 => 1],
            Func::rejectEmpty([null, 1, ""])
        );
    }

    public function testRejectKeys(): void
    {
        $this->assertEquals(
            ['a' => 1],
            Func::rejectKeys(['a' => 1, 'b' => 2, 'c' => 3], 'b', 'c')
        );
    }

    public function testRejectValues(): void
    {
        $this->assertEquals(
            ['c' => 3],
            Func::rejectValues(['a' => 1, 'b' => 2, 'c' => 3], 1, 2)
        );
    }

    public function testValues(): void
    {
        $this->assertEquals(
            [1, 2],
            Func::values($this->flat)
        );
    }

    public function testReverse(): void
    {
        $this->assertEquals(
            [3, 2, 1],
            Func::reverse([1, 2, 3])
        );

        $this->assertEquals(
            [2 => 3, 1 => 2, 0 => 1],
            Func::reverse([1, 2, 3], true)
        );
    }

    public function testKeys(): void
    {
        $this->assertEquals(
            ['a', 'b'],
            Func::keys($this->flat)
        );
    }

    public function testKeysMap(): void
    {
        $this->assertEquals(
            ['a1' => 1, 'b2' => 2, 'c3' => 3],
            Func::keysMap(['a' => 1, 'b' => 2, 'c' => 3], fn(string $key, int $item) => $key . $item),
        );
    }

    public function testKeysFromField(): void
    {
        $arr = [
            ['id' => 10, 'val' => 'a'],
            ['id' => 20, 'val' => 'b'],
        ];

        $this->assertEquals(
            [
                10 => ['id' => 10, 'val' => 'a'],
                20 => ['id' => 20, 'val' => 'b'],
            ],
            Func::keysFromField($arr, 'id'),
        );
    }

    public function testKeysGet(): void
    {
        $this->assertEquals(
            'b',
            Func::keysGet($this->flat, 1)
        );
        $this->assertNull(Func::keysGet($this->flat, 10));
    }

    public function testKeysFirst(): void
    {
        $this->assertEquals(
            'a',
            Func::keysGetFirst($this->flat)
        );
        $this->assertNull(Func::keysGetFirst([]));
    }

    public function testKeysLast(): void
    {
        $this->assertEquals(
            'b',
            Func::keysGetLast($this->flat)
        );
        $this->assertNull(Func::keysGetLast([]));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue(
            Func::isEmpty([])
        );
        $this->assertFalse(
            Func::isEmpty([''])
        );
    }

    public function testIsNotEmpty(): void
    {
        $this->assertFalse(
            Func::isNotEmpty([])
        );
        $this->assertTrue(
            Func::isNotEmpty([''])
        );
    }

    public function testIsEvery(): void
    {
        $arr = [1, 2, 3];
        $this->assertTrue(Func::isEvery($arr, fn(int $item) => $item > 0));
        $this->assertFalse(Func::isEvery($arr, fn(int $item) => $item > 1));
    }

    public function testIsNone(): void
    {
        $arr = [1, 2, 3];
        $this->assertFalse(Func::isNone($arr, fn(int $item) => $item > 0));
        $this->assertFalse(Func::isNone($arr, fn(int $item) => $item > 1));
        $this->assertTrue(Func::isNone($arr, fn(int $item) => $item > 100));
    }

    public function testIsSome(): void
    {
        $arr = [1, 2, 3];
        $this->assertTrue(Func::isAny($arr, fn(int $item) => $item > 0));
        $this->assertTrue(Func::isAny($arr, fn(int $item) => $item >= 3));
        $this->assertFalse(Func::isAny($arr, fn(int $item) => $item > 3));
    }

    public function testIsList(): void
    {
        $arr = [0 => 1, 1 => 2, 2 => 3];
        $this->assertTrue(Func::isList($arr));

        $arr = [10 => 1, 11 => 2, 12 => 3];
        $this->assertFalse(Func::isList($arr));
    }

    public function testIsHasValue(): void
    {
        $arr = [1, 2, 3];
        $this->assertTrue(Func::isHasValue($arr, 3, 4));
        $this->assertFalse(Func::isHasValue($arr, 30, 40));
        $this->assertFalse(Func::isHasValue($arr, '3', '4'));
    }

    public function testIsFieldHasValue(): void
    {
        $arr = ['a' => 1, 'b' => 2];
        $this->assertTrue(Func::isFieldHasValue($arr, 'a', 1, 10));
        $this->assertFalse(Func::isFieldHasValue($arr, 'a', 2));
    }

    public function testIsHasKey(): void
    {
        $arr = ['a' => 1, 'b' => 2];
        $this->assertTrue(Func::isHasKey($arr, 'b'));
        $this->assertFalse(Func::isHasKey($arr, 'c'));
    }

    public function testUnique(): void
    {
        $this->assertEquals(
            [0 => 1, 2 => 2],
            Func::unique([1, 1, 2])
        );
    }

    public function testUniqueBy(): void
    {
        $first = new stdClass();
        $first->value = 1;

        $second = new stdClass();
        $second->value = 2;

        $third = new stdClass();
        $third->value = 1;

        $fourth = new stdClass();
        $fourth->value = 3;

        $arr = ['a' => $first, 'b' => $second, 'c' => $third, 'd' => $fourth];

        $this->assertEquals(
            ['a' => $first, 'b' => $second, 'd' => $fourth],
            Func::uniqueBy($arr, fn(stdClass $item, string $key) => $item->value)
        );
    }

    public function testReduce(): void
    {
        $this->assertEquals(
            3,
            Func::reduce($this->flat, fn(int $res, int $item) => $res + $item, 0)
        );

        $this->assertEquals(
            ['a', 1, 'b', 2],
            Func::reduce($this->flat, fn(array $res, int $val, string $key) => [...$res, $key, $val])
        );
    }

    public function testCount(): void
    {
        $this->assertEquals(
            3,
            Func::count([1, 2, 3])
        );
    }

    public function testJsonEncodeFields(): void
    {
        $this->assertEquals(
            ['a' => '{"f":1}', 'b' => '{"f":2}', 'c' => ['f' => 3]],
            Func::jsonEncodeFields(['a' => ['f' => 1], 'b' => ['f' => 2], 'c' => ['f' => 3]], 'a', 'b')
        );
    }

    public function testJsonEncodeBy(): void
    {
        $this->assertEquals(
            ['a' => '{"f":1}', 'b' => '{"f":2}', 'c' => ['f' => 3]],
            Func::jsonEncodeBy(['a' => ['f' => 1], 'b' => ['f' => 2], 'c' => ['f' => 3]], fn(array $item, string $key) => $item === ['f' => 1] || $key === 'b')
        );
    }

    public function testJsonDecodeFields(): void
    {
        $this->assertEquals(
            ['a' => ['f' => 1], 'b' => ['f' => 2], 'c' => '{"f":3}'],
            Func::jsonDecodeFields(['a' => '{"f":1}', 'b' => '{"f":2}', 'c' => '{"f":3}'], 'a', 'b')
        );
    }

    public function testJsonDecodeBy(): void
    {
        $this->assertEquals(
            ['a' => ['f' => 1], 'b' => ['f' => 2], 'c' => '{"f":3}'],
            Func::jsonDecodeBy(['a' => '{"f":1}', 'b' => '{"f":2}', 'c' => '{"f":3}'], fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b')
        );
    }

    public function testAppend(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4],
            Func::append([1, 2], 3, 4)
        );
    }

    public function testAppendMerge(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, [6, 7]],
            Func::appendMerge([1, 2], 3, [4, 5, [6, 7]])
        );
    }

    public function testAppendMergeFromJson(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, [6, 7]],
            Func::appendMergeFromJson([1, 2], '[3,4,5,[6,7]]')
        );
    }

    public function testAppendMergeFromString(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5],
            Func::appendMergeFromString([1, 2], '3,4,5', ',')
        );
    }

    public function testPrepend(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4],
            Func::prepend([3, 4], 1, 2)
        );
    }

    public function testPrependMerge(): void
    {
        $this->assertEquals(
            [1, 2, 3, [4, 5], 6, 7],
            Func::prependMerge([6, 7], 1, [2, 3, [4, 5]])
        );
    }

    public function testPrependMergeFromJson(): void
    {
        $this->assertEquals(
            [1, 2, 3, [4, 5], 6, 7],
            Func::prependMergeFromJson([6, 7], '[1,2,3,[4,5]]')
        );
    }

    public function testPrependMergeFromString(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5],
            Func::prependMergeFromString([4, 5], '1,2,3', ',')
        );
    }

    public function testFilter(): void
    {
        $this->assertEquals(
            ['c' => 3],
            Func::filter(['a' => 1, 'b' => 2, 'c' => 3], fn(int $item, string $key) => $item > 1 && $key !== 'b')
        );
    }

    public function testFilterKeys(): void
    {
        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            Func::filterValues(['a' => 1, 'b' => 2, 'c' => 3], 1, 2)
        );
    }

    public function testKeysCaseToCamel(): void
    {
        foreach ($this->case as $item) {
            $this->assertEquals(
                $this->case['camel'],
                Func::keysCaseToCamel($item)
            );
        }
    }

    public function testKeysCaseToPaskal(): void
    {
        foreach ($this->case as $item) {
            $this->assertEquals(
                $this->case['paskal'],
                Func::keysCaseToPaskal($item)
            );
        }
    }

    public function testKeysCaseToSnake(): void
    {
        foreach ($this->case as $item) {
            $this->assertEquals(
                $this->case['snake'],
                Func::keysCaseToSnake($item)
            );
        }
    }

    public function testCaseKeyToKebab(): void
    {
        foreach ($this->case as $item) {
            $this->assertEquals(
                $this->case['kebab'],
                Func::keysCaseToKebab($item)
            );
        }
    }

    public function testKeysCaseToScreamSnake(): void
    {
        foreach ($this->case as $item) {
            $this->assertEquals(
                $this->case['screamSnake'],
                Func::keysCaseToScreamSnake($item)
            );
        }
    }

    public function testKeysCaseToScreamKebab(): void
    {
        foreach ($this->case as $item) {
            $this->assertEquals(
                $this->case['screamKebab'],
                Func::keysCaseToScreamKebab($item)
            );
        }
    }

    public function testFind(): void
    {
        $this->assertEqualsMultiple(
            1,
            Func::find($this->flat, fn(int $item) => $item == 1),
            Func::find($this->flat, fn(int $item, string $key) => $key == 'a'),
        );
    }

    public function testGroup(): void
    {
        $arr = [1, 2, 3, 4, 5];

        $this->assertEquals(
            [
                'less' => [1, 2, 3],
                'more' => [4, 5]
            ],
            Func::group($arr, fn(int $item) => $item > 3 ? 'more' : 'less'),
        );
    }

    public function testGroupByField(): void
    {
        $arr = [
            ['a' => 1],
            ['a' => 1],
            ['a' => 3],
        ];

        $this->assertEquals(
            [
                1 => [
                    0 => ['a' => 1],
                    1 => ['a' => 1],
                ],
                3 => [
                    0 => ['a' => 3],
                ],
            ],
            Func::groupByField($arr, 'a')
        );

        $arr = [
            ['a' => 1],
            ['a' => 1],
            ['b' => 2],
        ];

        $this->assertEquals(
            [
                1  => [
                    0 => ['a' => 1],
                    1 => ['a' => 1],
                ],
                '' => [
                    0 => ['b' => 2],
                ],
            ],
            Func::groupByField($arr, 'a')
        );
    }

    public function testGroupToStruct(): void
    {
        $first = new stdClass();
        $first->value = 1;

        $second = new stdClass();
        $second->value = 2;

        $third = new stdClass();
        $third->value = 1;

        $fourth = new stdClass();
        $fourth->value = 3;

        $arr = [$first, $second, $third, $fourth];

        $this->assertEquals(
            [
                [
                    'key'   => $first,
                    'items' => [$first, $third]
                ],
                [
                    'key'   => $second,
                    'items' => [$second]
                ],
                [
                    'key'   => $fourth,
                    'items' => [$fourth]
                ],
            ],
            Func::groupToStruct($arr, fn($item) => $item)
        );

        $this->assertTrue(true);
    }

    public function testSort(): void
    {
        $this->assertEquals(
            [1, 11, 2, 3],
            Func::sort([1, 3, 11, 2], fn($a, $b) => strcmp($a, $b)),
        );
        $this->assertEquals(
            [1, 2, 3, 11],
            Func::sort([1, 3, 11, 2], fn($a, $b) => strnatcmp($a, $b)),
        );

        $this->assertEquals(
            [1, 2, 3],
            Func::sort([3, 1, 2], fn($a, $b) => $a - $b)
        );
    }

    public function testClear(): void
    {
        $this->assertEquals(
            [],
            Func::clear([1, 2, 3])
        );
    }

    public function testChunkBySize(): void
    {
        $arr = [1, 2, 3, 4, 5];

        $this->assertEquals(
            [[1, 2], [3, 4], [5]],
            Func::chunkBySize($arr, 2),
        );

        $this->assertEquals(
            [
                [0 => 1, 1 => 2],
                [2 => 3, 3 => 4],
                [4 => 5],
            ],
            Func::chunkBySize($arr, 2, true)
        );
    }

    public function testChunkByCount(): void
    {
        $arr = [1, 2, 3, 4, 5];

        $this->assertEquals(
            [
                [1, 2, 3, 4, 5]
            ],
            Func::chunkByCount($arr, 1)
        );
        $this->assertEquals(
            [
                [1, 2, 3], [4, 5]
            ],
            Func::chunkByCount($arr, 2)
        );
        $this->assertEquals(
            [
                [1, 2], [3, 4], [5]
            ],
            Func::chunkByCount($arr, 3)
        );
        $this->assertEquals(
            [
                [1, 2], [3], [4], [5]
            ],
            Func::chunkByCount($arr, 4)
        );
        $this->assertEquals(
            [
                [1], [2], [3], [4], [5]
            ],
            Func::chunkByCount($arr, 5)
        );
        $this->assertEquals(
            [
                [1], [2], [3], [4], [5], []
            ],
            Func::chunkByCount($arr, 6)
        );


        $this->assertEquals(
            [
                [0 => 1, 1 => 2],
                [2 => 3, 3 => 4],
                [4 => 5],
            ],
            Func::chunkByCount($arr, 3, true)
        );
    }

    public function testFlip(): void
    {
        $arr = ['a' => 10, 'b' => 20, 'c' => 30];
        $this->assertEquals(
            ['10' => 'a', '20' => 'b', '30' => 'c'],
            Func::flip($arr)
        );
    }

    public function testShift(): void
    {
        $arr = [1, 2, 3];
        $elem = Func::shift($arr);

        $this->assertEquals(1, $elem);
        $this->assertEquals(
            [2, 3],
            $arr
        );

        $arr = ['a' => 10, 'b' => 20, 'c' => 30];
        $elem = Func::shift($arr);
        $this->assertEquals(10, $elem);
        $this->assertEquals(
            ['b' => 20, 'c' => 30],
            $arr
        );
    }

    public function testPop(): void
    {
        $arr = [1, 2, 3];
        $elem = Func::pop($arr);

        $this->assertEquals(3, $elem);
        $this->assertEquals(
            [1, 2],
            $arr
        );

        $arr = ['a' => 10, 'b' => 20, 'c' => 30];
        $elem = Func::pop($arr);
        $this->assertEquals(30, $elem);
        $this->assertEquals(
            ['a' => 10, 'b' => 20],
            $arr
        );
    }

    public function testSplice(): void
    {
        $arr = [1, 2, 3, 4, 5];

        $this->assertEquals(
            [3],
            Func::splice($arr, 2, 1, 'item'),
        );
        $this->assertEquals(
            [1, 2, 'item', 4, 5],
            $arr
        );
    }

    public function testSpliceHead(): void
    {
        $arr = [1, 2, 3, 4, 5];

        $this->assertEquals(
            [1, 2],
            Func::spliceHead($arr, 2, 'item'),
        );
        $this->assertEquals(
            ['item', 3, 4, 5],
            $arr
        );
    }

    public function testSpliceTail(): void
    {
        $arr = [1, 2, 3, 4, 5];

        $this->assertEquals(
            [4, 5],
            Func::spliceTail($arr, 2, 'item'),
        );
        $this->assertEquals(
            [1, 2, 3, 'item'],
            $arr
        );
    }

    public function testSlice(): void
    {
        $arr = [10 => 1, 2, 3, 4, 5];
        $this->assertEquals(
            [2, 3],
            Func::slice($arr, 1, 2)
        );
        $this->assertEquals(
            [11 => 2, 12 => 3],
            Func::slice($arr, 1, 2, true)
        );
    }

    public function testSliceHead(): void
    {
        $arr = [10 => 1, 2, 3, 4, 5];
        $this->assertEquals(
            [1, 2],
            Func::sliceHead($arr, 2)
        );
        $this->assertEquals(
            [1, 2],
            Func::sliceHead($arr, -2)
        );
        $this->assertEquals(
            [10 => 1, 11 => 2],
            Func::sliceHead($arr, 2, true)
        );
    }

    public function testSliceTail(): void
    {
        $arr = [10 => 1, 2, 3, 4, 5];
        $this->assertEquals(
            [4, 5],
            Func::sliceTail($arr, 2)
        );
        $this->assertEquals(
            [4, 5],
            Func::sliceTail($arr, -2)
        );
        $this->assertEquals(
            [13 => 4, 14 => 5],
            Func::sliceTail($arr, 2, true)
        );
    }

    public function testReplace(): void
    {
        $arr = [1, 2, 3, 4, 5];
        $this->assertEquals(
            [6, 7, 3, 4, 8],
            Func::replace($arr, [6, 7], [4 => 8])
        );
    }

    public function testReplaceRecursive(): void
    {
        $arr = [
            [1, 2, 3],
            [4, 5, 6],
        ];
        $arrReplace1 = [
            1 => [
                1 => 7,
                2 => 8
            ]
        ];
        $arrReplace2 = [
            1 => [
                2 => 9
            ]
        ];

        $this->assertEquals(
            [
                [1, 2, 3],
                [4, 7, 9],
            ],
            Func::replaceRecursive($arr, $arrReplace1, $arrReplace2)
        );
    }

    public function testFlatten(): void
    {
        $arr = [1, [2], [3, [4, [5]]]];

        $this->assertEquals(
            [1, 2, 3, [4, [5]]],
            Func::flatten($arr)
        );
        $this->assertEquals(
            [1, 2, 3, 4, [5]],
            Func::flatten($arr, 2)
        );
        $this->assertEquals(
            [1, 2, 3, 4, 5],
            Func::flatten($arr, 3)
        );
    }

    public function testFlattenAll(): void
    {
        $arr = [1, [2], [3, [4, [5]]]];

        $this->assertEquals(
            [1, 2, 3, 4, 5],
            Func::flattenAll($arr)
        );
    }

    public function testPad(): void
    {
        $arr = [1, 2];

        $this->assertEquals(
            [1, 2, 0, 0, 0],
            Func::pad($arr, 5, 0)
        );
        $this->assertEquals(
            [0, 0, 0, 1, 2],
            Func::pad($arr, -5, 0)
        );
    }

    public function testGet(): void
    {
        $arr = [1, 2, 3];
        $this->assertEquals(
            2,
            Func::get($arr, 1)
        );
        $this->assertNull(
            Func::get($arr, 10)
        );
    }

    public function testGetOrElse(): void
    {
        $arr = [1, 2, 3];
        $this->assertEquals(
            2,
            Func::getOrElse($arr, 1, 'default')
        );
        $this->assertEquals(
            'default',
            Func::getOrElse($arr, 10, 'default')
        );
    }

    public function testGetOrException(): void
    {
        $arr = [1, 2, 3];
        $this->assertEquals(
            2,
            Func::getOrException($arr, 1)
        );

        $this->expectException(NotFoundException::class);
        Func::getOrException($arr, 10);
    }

    public function testGetByNumber(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            2,
            Func::getByNumber($arr, 1)
        );
        $this->assertNull(
            Func::getByNumber($arr, 10)
        );
    }

    public function testGetByNumberOrElse(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            2,
            Func::getByNumberOrElse($arr, 1, 'default')
        );
        $this->assertEquals(
            'default',
            Func::getByNumberOrElse($arr, 10, 'default')
        );
    }


    public function testGetByNumberOrException(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            2,
            Func::getByNumberOrException($arr, 1)
        );

        $this->expectException(NotFoundException::class);
        Func::getByNumberOrException($arr, 10);
    }


    public function testGetFirst(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            1,
            Func::getFirst($arr)
        );
        $this->assertNull(
            Func::getFirst([])
        );
    }

    public function testGetFirstOrElse(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            1,
            Func::getFirstOrElse($arr, 'default')
        );
        $this->assertEquals(
            'default',
            Func::getFirstOrElse([], 'default')
        );
    }

    public function testGetFirstOrException(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            1,
            Func::getFirstOrException($arr)
        );

        $this->expectException(NotFoundException::class);
        Func::getFirstOrException([]);
    }

    public function testGetLast(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            3,
            Func::getLast($arr)
        );
        $this->assertNull(
            Func::getLast([])
        );
    }

    public function testGetLastOrElse(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            3,
            Func::getLastOrElse($arr, 'default')
        );
        $this->assertEquals(
            'default',
            Func::getLastOrElse([], 'default')
        );
    }

    public function testGetLastOrException(): void
    {
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEquals(
            3,
            Func::getLastOrException($arr)
        );

        $this->expectException(NotFoundException::class);
        Func::getLastOrException([]);
    }


    public function testMathMin(): void
    {
        $arr = [1, 2, 3];

        $this->assertEquals(
            1,
            Func::mathMin($arr)
        );
    }

    public function testMathMinBy(): void
    {
        $arr = [1, 2, 3];

        $this->assertEquals(
            5,
            Func::mathMinBy($arr, fn(int $item, int $key) => 10 - $item - $key)
        );
    }

    public function testMathMinByField(): void
    {
        $arr = [
            ['a' => 1, 'b' => 2, 'c' => 3],
            ['a' => 4, 'b' => 5, 'c' => 6],
        ];

        $this->assertEquals(
            2,
            Func::mathMinByField($arr, 'b')
        );
    }
}