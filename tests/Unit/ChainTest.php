<?php
/** @noinspection PhpParamsInspection */

namespace Unit;

use Operations;
use PHPUnit\Framework\TestCase;
use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Exception\NotFoundException;
use Ru\Progerplace\Chain\Func;
use stdClass;

final class ChainTest extends TestCase
{
    use Operations;

    protected array  $simple       = [1, 2];
    protected string $simpleJson   = '{1,2}';
    protected string $simpleString = '1,2';

    protected array  $flat       = ['a' => 1, 'b' => 2, 'c' => 3];
    protected string $flatJson   = '{"a":1,"b":2,"c":3}';
    protected string $flatString = '1,2,3';

    protected array $nested = [
        ['a' => 1, 'b' => 2],
        ['a' => 10, 'b' => 11],
        ['a' => 20, 'b' => 21],
    ];

    protected array $nestedDepthSingled = [
        'a' => [
            'a.a' => [
                'a.a.a' => 1,
                'a.a.b' => 2
            ]
        ]
    ];
    protected array $nestedDepth        = [
        'a' => [
            'a.a' => ['a.a.a' => 1, 'a.a.b' => 2],
            'a.b' => ['a.b.a' => 10, 'a.b.b' => 11],
            'a.c' => ['a.c.a' => 20, 'a.c.b' => 21],
        ],
        'b' => [
            'b.a' => ['b.a.a' => 0, 'b.a.b' => 2, 'b.a.c' => 3],
            'b.b' => ['b.b.a' => 10, 'b.b.b' => 11, 'b.b.c' => null],
        ]
    ];

    protected array $case = [
        'camel'       => ['varFirst' => 1, 'varSecond' => 2],
        'paskal'      => ['VarFirst' => 1, 'VarSecond' => 2],
        'snake'       => ['var_first' => 1, 'var_second' => 2],
        'kebab'       => ['var-first' => 1, 'var-second' => 2],
        'screamSnake' => ['VAR_FIRST' => 1, 'VAR_SECOND' => 2],
        'screamKebab' => ['VAR-FIRST' => 1, 'VAR-SECOND' => 2],
    ];

    public function testCreate(): void
    {
        $this->assertEqualsMultiple(
            $this->flat,
            Chain::from($this->flat)->toArray(),
            ChainFunc::from($this->flat)->toArray(),
        );

        $this->assertEqualsMultiple(
            [],
            Chain::from(null)->toArray(),
            ChainFunc::from(null)->toArray(),
        );

        $this->assertEqualsMultiple(
            $this->flat,
            Chain::from(null, $this->flat)->toArray(),
            ChainFunc::from(null, $this->flat)->toArray(),
        );

        $this->assertEquals(
            $this->flatJson,
            Chain::from($this->flat)->toJson()
        );

        $this->assertEqualsMultiple(
            $this->flat,
            Chain::fromJson($this->flatJson)->toArray(),
            ChainFunc::fromJson($this->flatJson)->toArray(),
        );

        $this->assertEqualsMultiple(
            $this->simple,
            Chain::fromString($this->simpleString, ',')->toArray(),
            ChainFunc::fromString($this->simpleString, ',')->toArray(),
        );

        $this->assertEquals(
            $this->flatJson,
            Chain::from($this->flat)->toJson()
        );

        $this->assertEquals(
            $this->simpleString,
            Chain::from($this->simple)->toString(',')
        );

        $this->assertEqualsMultiple(
            [1, 2, 3, 4],
            Chain::fromRange(1, 4)->toArray(),
            ChainFunc::fromRange(1, 4)->toArray(),
        );
    }

    public function testMap()
    {
        $this->assertEqualsMultiple(
            Func::map($this->flat, fn(int $item, string $key) => $key . $item),
            Chain::from($this->flat)->map(fn(int $item, string $key) => $key . $item)->toArray(),
            ChainFunc::from($this->flat)->map(fn(int $item, string $key) => $key . $item),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        'a.a.a' => 6,
                        'a.a.b' => 7
                    ]
                ]
            ],
            Chain::from($this->nestedDepthSingled)->elems->elems->map(fn(int $item) => $item + 5)->toArray(),
            ChainFunc::from($this->nestedDepthSingled)->elems->elems->map(fn(int $item) => $item + 5),
        );
    }

    public function testReject()
    {
        $arr = [null, 1, ""];

        $this->assertEqualsMultiple(
            Func::reject($this->nested, fn(array $item, int $key) => $item['a'] === 1 || $key === 2),
            Chain::from($this->nested)->reject(fn(array $item, int $key) => $item['a'] === 1 || $key === 2)->toArray(),
            ChainFunc::from($this->nested)->reject(fn(array $item, int $key) => $item['a'] === 1 || $key === 2),
        );
        $this->assertEqualsMultiple(
            Func::rejectNull($arr),
            Chain::from($arr)->reject->null()->toArray(),
            ChainFunc::from($arr)->reject->null(),
        );
        $this->assertEqualsMultiple(
            Func::rejectEmpty($arr),
            Chain::from($arr)->reject->empty()->toArray(),
            ChainFunc::from($arr)->reject->empty(),
        );
        $this->assertEqualsMultiple(
            Func::rejectKeys($this->flat, 'a', 'b'),
            Chain::from($this->flat)->reject->keys('a', 'b')->toArray(),
            ChainFunc::from($this->flat)->reject->keys('a', 'b'),
        );
        $this->assertEqualsMultiple(
            Func::rejectValues($this->flat, 1, '2'),
            Chain::from($this->flat)->reject->values(1, '2')->toArray(),
            ChainFunc::from($this->flat)->reject->values(1, '2'),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => ['c' => 3]
                ]
            ],
            Chain::from($arr)->elems->elems->reject(fn(int $item, string $key) => $item < 2 || $key == 'b')->toArray(),
            ChainFunc::from($arr)->elems->elems->reject(fn(int $item, string $key) => $item < 2 || $key == 'b'),
            Chain::from($arr)->elems->elems->reject->keys('a', 'b')->toArray(),
            ChainFunc::from($arr)->elems->elems->reject->keys('a', 'b'),
            Chain::from($arr)->elems->elems->reject->values(1, '2')->toArray(),
            ChainFunc::from($arr)->elems->elems->reject->values(1, '2'),
        );

        $arr2 = [
            'a' => [
                'a.a' => [null, 1, ""]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1 => 1]
                ]
            ],
            Chain::from($arr2)->elems->elems->reject->empty()->toArray(),
            ChainFunc::from($arr2)->elems->elems->reject->empty(),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1 => 1, 2 => ""]
                ]
            ],
            Chain::from($arr2)->elems->elems->reject->null()->toArray(),
            ChainFunc::from($arr2)->elems->elems->reject->null(),
        );
    }

    public function testValues(): void
    {
        $this->assertEqualsMultiple(
            Func::values($this->flat),
            Chain::from($this->flat)->values()->toArray(),
            ChainFunc::from($this->flat)->values(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2]
                ]
            ],
            Chain::from($this->nestedDepthSingled)->elems->elems->values()->toArray(),
            ChainFunc::from($this->nestedDepthSingled)->elems->elems->values(),
        );
    }

    public function testValuesGetList(): void
    {
        $this->assertEqualsMultiple(
            Func::values($this->flat),
            Chain::from($this->flat)->values->getList(),
            ChainFunc::from($this->flat)->values->getList(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2]
                ]
            ],
            Chain::from($this->nestedDepthSingled)->elems->elems->values->getList()->toArray(),
            ChainFunc::from($this->nestedDepthSingled)->elems->elems->values->getList(),
        );
    }

    public function testReverse(): void
    {
        $this->assertEqualsMultiple(
            Func::reverse($this->flat),
            Chain::from($this->flat)->reverse()->toArray(),
            ChainFunc::from($this->flat)->reverse(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        'a.a.b' => 2,
                        'a.a.a' => 1,
                    ]
                ]
            ],
            Chain::from($this->nestedDepthSingled)->elems->elems->reverse()->toArray(),
            ChainFunc::from($this->nestedDepthSingled)->elems->elems->reverse(),
        );
    }

    public function testKeys(): void
    {
        // keys и keysGetList
        $this->assertEqualsMultiple(
            Func::keysGetList($this->flat),
            Chain::from($this->flat)->keys->getList(),
            ChainFunc::from($this->flat)->keys->getList(),
            Chain::from($this->flat)->keys()->toArray(),
            ChainFunc::from($this->flat)->keys(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        'a.a.a',
                        'a.a.b',
                    ]
                ]
            ],
            Chain::from($this->nestedDepthSingled)->elems->elems->keys->getList()->toArray(),
            ChainFunc::from($this->nestedDepthSingled)->elems->elems->keys->getList(),
            Chain::from($this->nestedDepthSingled)->elems->elems->keys()->toArray(),
            ChainFunc::from($this->nestedDepthSingled)->elems->elems->keys(),
        );

        // map
        $this->assertEqualsMultiple(
            Func::keysMap($this->flat, fn(string $key, int $val) => $key . $val),
            Chain::from($this->flat)->keys->map(fn(string $key, int $val) => $key . $val)->toArray(),
            ChainFunc::from($this->flat)->keys->map(fn(string $key, int $val) => $key . $val),
        );

        $arr = [
            'a' => [
                'a.a' => [
                    'a.a.a' => 1,
                    'a.a.b' => 2
                ]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        'a.a.a1' => 1,
                        'a.a.b2' => 2
                    ]
                ]
            ],
            Chain::from($arr)->elems->elems->keys->map(fn(string $key, int $val) => $key . $val)->toArray(),
            ChainFunc::from($arr)->elems->elems->keys->map(fn(string $key, int $val) => $key . $val),
        );

        // fromField
        $arr = [
            ['id' => 10, 'val' => 'a'],
            ['id' => 20, 'val' => 'b'],
        ];

        $this->assertEqualsMultiple(
            Func::keysFromField($arr, 'id'),
            Chain::from($arr)->keys->fromField('id')->toArray(),
            ChainFunc::from($arr)->keys->fromField('id'),
        );


        $arr = [
            'a' => [
                'a.a' => [
                    ['id' => 10, 'val' => 'a'],
                    ['id' => 20, 'val' => 'b'],
                ]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        10 => ['id' => 10, 'val' => 'a'],
                        20 => ['id' => 20, 'val' => 'b'],
                    ]
                ]
            ],
            Chain::from($arr)->elems->elems->keys->fromField('id')->toArray(),
            ChainFunc::from($arr)->elems->elems->keys->fromField('id'),
        );


        // get
        $this->assertEqualsMultiple(
            Func::keysGet($this->flat, 1),
            Chain::from($this->flat)->keys->get(1),
            ChainFunc::from($this->flat)->keys->get(1),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 10, 'b' => 'a'],
                'a.b' => ['a' => 10, 'b' => 'a']
            ],
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 'b',
                    'a.b' => 'b',
                ]
            ],
            Chain::from($arr)->elems->elems->keys->get(1)->toArray(),
            ChainFunc::from($arr)->elems->elems->keys->get(1),
        );

        // getFirst
        $this->assertEqualsMultiple(
            Func::keysGetFirst($this->flat),
            Chain::from($this->flat)->keys->getFirst(),
            ChainFunc::from($this->flat)->keys->getFirst(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 'a',
                    'a.b' => 'a',
                ]
            ],
            Chain::from($arr)->elems->elems->keys->getFirst()->toArray(),
            ChainFunc::from($arr)->elems->elems->keys->getFirst(),
        );

        // getLast
        $this->assertEqualsMultiple(
            Func::keysGetLast($this->flat),
            Chain::from($this->flat)->keys->getLast(),
            ChainFunc::from($this->flat)->keys->getLast(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 'b',
                    'a.b' => 'b',
                ]
            ],
            Chain::from($arr)->elems->elems->keys->getLast()->toArray(),
            ChainFunc::from($arr)->elems->elems->keys->getLast(),
        );
    }

    public function testUnique(): void
    {
        $arrFlat = [1, 1, 2];

        $this->assertEqualsMultiple(
            Func::unique($arrFlat),
            Chain::from($arrFlat)->unique()->toArray(),
            ChainFunc::from($arrFlat)->unique(),
        );

        $arrDeep = [
            'a' => [
                'a.a' => [
                    'a.a.a' => 1,
                    'a.a.b' => 1
                ]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        'a.a.a' => 1,
                    ]
                ]
            ],
            Chain::from($arrDeep)->elems->elems->unique()->toArray(),
            ChainFunc::from($arrDeep)->elems->elems->unique(),
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

        $this->assertEqualsMultiple(
            Func::uniqueBy($arr, fn(stdClass $item, string $key) => $item->value),
            Chain::from($arr)->unique->by(fn(stdClass $item, string $key) => $item->value)->toArray(),
            ChainFunc::from($arr)->unique->by(fn(stdClass $item, string $key) => $item->value),
        );
    }

    public function testReduce(): void
    {
        $this->assertEqualsMultiple(
            Func::reduce($this->flat, fn(int $res, int $item) => $res + $item, 0),
            Chain::from($this->flat)->reduce(fn(int $res, int $item) => $res + $item, 0),
            ChainFunc::from($this->flat)->reduce(fn(int $res, int $item) => $res + $item, 0),
        );

        $this->assertEqualsMultiple(
            Func::reduce($this->flat, fn(array $res, int $val, string $key) => [...$res, $key, $val]),
            Chain::from($this->flat)->reduce(fn(array $res, int $val, string $key) => [...$res, $key, $val])->toArray(),
            ChainFunc::from($this->flat)->reduce(fn(array $res, int $val, string $key) => [...$res, $key, $val]),
        );
    }

    public function testCount(): void
    {
        $this->assertEqualsMultiple(
            Func::count($this->flat),
            Chain::from($this->flat)->count(),
            ChainFunc::from($this->flat)->count(),
        );

        $arr = [
            'a.a' => [
                'a.a.a' => [1, 2, 3],
                'a.a.b' => [1, 2]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a.a' => [
                    'a.a.a' => 3,
                    'a.a.b' => 2
                ],
            ],
            Chain::from($arr)->elems->elems->count()->toArray(),
            ChainFunc::from($arr)->elems->elems->count(),
        );

    }

    public function testJson(): void
    {
        $arr = ['a' => ['f' => 1], 'b' => ['f' => 2], 'c' => ['f' => 3]];
        $arrJson = ['a' => '{"f":1}', 'b' => '{"f":2}', 'c' => '{"f":3}'];

        $this->assertEqualsMultiple(
            Func::jsonEncodeFields($arr, 'a', 'b'),
            Chain::from($arr)->json->encodeFields('a', 'b')->toArray(),
            ChainFunc::from($arr)->json->encodeFields('a', 'b'),
        );

        $this->assertEqualsMultiple(
            Func::jsonEncodeBy($arr, fn(array $item, string $key) => $item === ['f' => 1] || $key === 'b'),
            Chain::from($arr)->json->encodeBy(fn(array $item, string $key) => $item === ['f' => 1] || $key === 'b')->toArray(),
            ChainFunc::from($arr)->json->encodeBy(fn(array $item, string $key) => $item === ['f' => 1] || $key === 'b'),
        );

        $this->assertEqualsMultiple(
            Func::jsonDecodeFields($arrJson, 'a', 'b'),
            Chain::from($arrJson)->json->decodeFields('a', 'b')->toArray(),
            ChainFunc::from($arrJson)->json->decodeFields('a', 'b'),
        );

        $this->assertEqualsMultiple(
            Func::jsonDecodeBy($arrJson, fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b'),
            Chain::from($arrJson)->json->decodeBy(fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b')->toArray(),
            ChainFunc::from($arrJson)->json->decodeBy(fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b'),
        );


        $arrDeep = [
            'a' => [
                'a.a' => [
                    'a.a.a' => ['f' => 1],
                    'a.a.b' => ['f' => 2]
                ]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        'a.a.a' => '{"f":1}',
                        'a.a.b' => ['f' => 2]
                    ]
                ]
            ],
            Chain::from($arrDeep)->elems->elems->json->encodeFields('a.a.a')->toArray(),
            Chain::from($arrDeep)->elems->elems->json->encodeBy(fn($item, $key) => $key === 'a.a.a')->toArray(),
            ChainFunc::from($arrDeep)->elems->elems->json->encodeFields('a.a.a'),
            ChainFunc::from($arrDeep)->elems->elems->json->encodeBy(fn($item, $key) => $key === 'a.a.a'),
        );
    }

    public function testAppend(): void
    {
        $arr = [1, 2];

        $this->assertEqualsMultiple(
            Func::append($arr, 3, 4, 5),
            Chain::from($arr)->append(3, 4, 5)->toArray(),
            ChainFunc::from($arr)->append(3, 4, 5),
        );
        $this->assertEqualsMultiple(
            Func::appendMerge($arr, 3, [4, [5]]),
            Chain::from($arr)->append->merge(3, [4, [5]])->toArray(),
            ChainFunc::from($arr)->append->merge(3, [4, [5]]),
        );
        $this->assertEqualsMultiple(
            Func::appendMergeFromJson($arr, '[3,4,5,[6,7]]'),
            Chain::from($arr)->append->mergeFromJson('[3,4,5,[6,7]]')->toArray(),
            ChainFunc::from($arr)->append->mergeFromJson('[3,4,5,[6,7]]'),
        );
        $this->assertEqualsMultiple(
            Func::appendMergeFromString($arr, '3,4,5', ','),
            Chain::from($arr)->append->mergeFromString('3,4,5', ',')->toArray(),
            ChainFunc::from($arr)->append->mergeFromString('3,4,5', ','),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 3, 4, 5]
                ]
            ],
            Chain::from($arr)->elems->elems->append(3, 4, 5)->toArray(),
            ChainFunc::from($arr)->elems->elems->append(3, 4, 5),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 3, 4, [5]]
                ]
            ],
            Chain::from($arr)->elems->elems->append->merge(3, [4, [5]])->toArray(),
            ChainFunc::from($arr)->elems->elems->append->merge(3, [4, [5]]),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 3, 4, 5, [6, 7]]
                ]
            ],
            Chain::from($arr)->elems->elems->append->mergeFromJson('[3,4,5,[6,7]]')->toArray(),
            ChainFunc::from($arr)->elems->elems->append->mergeFromJson('[3,4,5,[6,7]]'),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 3, 4, 5]
                ]
            ],
            Chain::from($arr)->elems->elems->append->mergeFromString('3,4,5', ',')->toArray(),
            ChainFunc::from($arr)->elems->elems->append->mergeFromString('3,4,5', ','),
        );
    }

    public function testPrepend(): void
    {
        $arr = [4, 5];

        $this->assertEqualsMultiple(
            Func::prepend($arr, 1, 2, 3),
            Chain::from($arr)->prepend(1, 2, 3)->toArray(),
            ChainFunc::from($arr)->prepend(1, 2, 3),
        );
        $this->assertEqualsMultiple(
            Func::prependMerge($arr, 1, [2, [3]]),
            Chain::from($arr)->prepend->merge(1, [2, [3]])->toArray(),
            ChainFunc::from($arr)->prepend->merge(1, [2, [3]]),
        );
        $this->assertEqualsMultiple(
            Func::prependMergeFromJson($arr, '[1,[2,[3]]]'),
            Chain::from($arr)->prepend->mergeFromJson('[1, [2, [3]]]')->toArray(),
            ChainFunc::from($arr)->prepend->mergeFromJson('[1, [2, [3]]]'),
        );
        $this->assertEqualsMultiple(
            Func::prependMergeFromString($arr, '1,2,3', ','),
            Chain::from($arr)->prepend->mergeFromString('1,2,3', ',')->toArray(),
            ChainFunc::from($arr)->prepend->mergeFromString('1,2,3', ','),
        );


        $arr = [
            'a' => [
                'a.a' => [4, 5]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 3, 4, 5]
                ]
            ],
            Chain::from($arr)->elems->elems->prepend(1, 2, 3)->toArray(),
            ChainFunc::from($arr)->elems->elems->prepend(1, 2, 3),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, [3], 4, 5]
                ]
            ],
            Chain::from($arr)->elems->elems->prepend->merge(1, [2, [3]])->toArray(),
            ChainFunc::from($arr)->elems->elems->prepend->merge(1, [2, [3]]),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, [3], 4, 5]
                ]
            ],
            Chain::from($arr)->elems->elems->prepend->mergeFromJson('[1,2,[3]]')->toArray(),
            ChainFunc::from($arr)->elems->elems->prepend->mergeFromJson('[1,2,[3]]'),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 3, 4, 5]
                ]
            ],
            Chain::from($arr)->elems->elems->prepend->mergeFromString('1,2,3', ',')->toArray(),
            ChainFunc::from($arr)->elems->elems->prepend->mergeFromString('1,2,3', ','),
        );
    }

    public function testFilter()
    {
        $this->assertEqualsMultiple(
            Func::filter(['a' => 1, 'b' => 2, 'c' => 3], fn(int $item, string $key) => $item > 1 && $key !== 'b'),
            Chain::from(['a' => 1, 'b' => 2, 'c' => 3])->filter(fn(int $item, string $key) => $item > 1 && $key !== 'b')->toArray(),
            ChainFunc::from(['a' => 1, 'b' => 2, 'c' => 3])->filter(fn(int $item, string $key) => $item > 1 && $key !== 'b'),
        );
        $this->assertEqualsMultiple(
            Func::filterKeys($this->flat, 'a', 'b'),
            Chain::from($this->flat)->filter->keys('a', 'b')->toArray(),
            ChainFunc::from($this->flat)->filter->keys('a', 'b'),
        );
        $this->assertEqualsMultiple(
            Func::filterValues($this->flat, 1, '2'),
            Chain::from($this->flat)->filter->values(1, '2')->toArray(),
            ChainFunc::from($this->flat)->filter->values(1, '2'),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => ['c' => 3]
                ]
            ],
            Chain::from($arr)->elems->elems->filter(fn(int $item, string $key) => $item > 1 && $key !== 'b')->toArray(),
            ChainFunc::from($arr)->elems->elems->filter(fn(int $item, string $key) => $item > 1 && $key !== 'b'),
            Chain::from($arr)->elems->elems->filter->keys('c')->toArray(),
            Chain::from($arr)->elems->elems->filter->values(3)->toArray(),
            ChainFunc::from($arr)->elems->elems->filter->values(3),
            ChainFunc::from($arr)->elems->elems->filter->keys('c'),
        );
    }

    public function testKeysCase(): void
    {
        foreach ($this->case as $item) {
            $this->assertEqualsMultiple(
                Func::keysCaseToCamel($item),
                Chain::from($item)->keys->case->toCamel()->toArray(),
                ChainFunc::from($item)->keys->case->toCamel(),
            );
        }

        foreach ($this->case as $item) {
            $this->assertEqualsMultiple(
                Func::keysCaseToPaskal($item),
                Chain::from($item)->keys->case->toPaskal()->toArray(),
                ChainFunc::from($item)->keys->case->toPaskal(),
            );
        }

        foreach ($this->case as $item) {
            $this->assertEqualsMultiple(
                Func::keysCaseToSnake($item),
                Chain::from($item)->keys->case->toSnake()->toArray(),
                ChainFunc::from($item)->keys->case->toSnake(),
            );
        }

        foreach ($this->case as $item) {
            $this->assertEqualsMultiple(
                Func::keysCaseToKebab($item),
                Chain::from($item)->keys->case->toKebab()->toArray(),
                ChainFunc::from($item)->keys->case->toKebab(),
            );
        }

        foreach ($this->case as $item) {
            $this->assertEqualsMultiple(
                Func::keysCaseToScreamSnake($item),
                Chain::from($item)->keys->case->toScreamSnake()->toArray(),
                ChainFunc::from($item)->keys->case->toScreamSnake(),
            );
        }

        foreach ($this->case as $item) {
            $this->assertEqualsMultiple(
                Func::keysCaseToScreamKebab($item),
                Chain::from($item)->keys->case->toScreamKebab()->toArray(),
                ChainFunc::from($item)->keys->case->toScreamKebab(),
            );
        }


        foreach ($this->case as $item) {
            $arr = [
                'a' => [
                    'a.a' => $item
                ]
            ];

            $this->assertEqualsMultiple(
                [
                    'a' => [
                        'a.a' => $this->case['camel']
                    ]
                ],
                Chain::from($arr)->elems->elems->keys->case->toCamel()->toArray(),
                ChainFunc::from($arr)->elems->elems->keys->case->toCamel(),
            );
        }

        foreach ($this->case as $item) {
            $arr = [
                'a' => [
                    'a.a' => $item
                ]
            ];

            $this->assertEqualsMultiple(
                [
                    'a' => [
                        'a.a' => $this->case['paskal']
                    ]
                ],
                Chain::from($arr)->elems->elems->keys->case->toPaskal()->toArray(),
                ChainFunc::from($arr)->elems->elems->keys->case->toPaskal(),
            );
        }

        foreach ($this->case as $item) {
            $arr = [
                'a' => [
                    'a.a' => $item
                ]
            ];

            $this->assertEqualsMultiple(
                [
                    'a' => [
                        'a.a' => $this->case['snake']
                    ]
                ],
                Chain::from($arr)->elems->elems->keys->case->toSnake()->toArray(),
                ChainFunc::from($arr)->elems->elems->keys->case->toSnake(),
            );
        }

        foreach ($this->case as $item) {
            $arr = [
                'a' => [
                    'a.a' => $item
                ]
            ];

            $this->assertEqualsMultiple(
                [
                    'a' => [
                        'a.a' => $this->case['kebab']
                    ]
                ],
                Chain::from($arr)->elems->elems->keys->case->toKebab()->toArray(),
                ChainFunc::from($arr)->elems->elems->keys->case->toKebab(),
            );
        }

        foreach ($this->case as $item) {
            $arr = [
                'a' => [
                    'a.a' => $item
                ]
            ];

            $this->assertEqualsMultiple(
                [
                    'a' => [
                        'a.a' => $this->case['screamSnake']
                    ]
                ],
                Chain::from($arr)->elems->elems->keys->case->toScreamSnake()->toArray(),
                ChainFunc::from($arr)->elems->elems->keys->case->toSCreamSnake(),
            );
        }

        foreach ($this->case as $item) {
            $arr = [
                'a' => [
                    'a.a' => $item
                ]
            ];

            $this->assertEqualsMultiple(
                [
                    'a' => [
                        'a.a' => $this->case['screamKebab']
                    ]
                ],
                Chain::from($arr)->elems->elems->keys->case->toScreamKebab()->toArray(),
                ChainFunc::from($arr)->elems->elems->keys->case->toScreamKebab(),
            );
        }
    }

    public function testFind(): void
    {
        $this->assertEqualsMultiple(
            Func::find($this->flat, fn(int $item) => $item == 1),
            Chain::from($this->flat)->find(fn(int $item) => $item == 1),
            Chain::from($this->flat)->find(fn(int $item, string $key) => $key == 'a'),
            ChainFunc::from($this->flat)->find(fn(int $item) => $item == 1),
            ChainFunc::from($this->flat)->find(fn(int $item, string $key) => $key == 'a'),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 1
                ]
            ],
            Chain::from($this->nestedDepthSingled)->elems->elems->find(fn(int $item) => $item == 1),
            ChainFunc::from($this->nestedDepthSingled)->elems->elems->find(fn(int $item) => $item == 1),
        );
    }

    public function testGroup(): void
    {
        $arr = [1, 2, 3, 4, 5];

        $this->assertEqualsMultiple(
            Func::group($arr, fn(int $item) => $item > 3 ? 'more' : 'less'),
            Chain::from($arr)->group(fn(int $item) => $item > 3 ? 'more' : 'less')->toArray(),
            ChainFunc::from($arr)->group(fn(int $item) => $item > 3 ? 'more' : 'less'),
        );

        $arr = [
            ['a' => 1],
            ['a' => 1],
            ['b' => 3],
        ];

        $this->assertEqualsMultiple(
            Func::groupByField($arr, 'a'),
            Chain::from($arr)->group->byField('a')->toArray(),
            ChainFunc::from($arr)->group->byField('a'),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2, 3, 4, 5]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        'less' => [1, 2, 3],
                        'more' => [4, 5]
                    ]
                ]
            ],
            Chain::from($arr)->elems->elems->group(fn(int $item) => $item > 3 ? 'more' : 'less')->toArray(),
            ChainFunc::from($arr)->elems->elems->group(fn(int $item) => $item > 3 ? 'more' : 'less'),
        );


        $arr = [
            'a' => [
                'a.a' => [
                    ['a' => 1],
                    ['a' => 1],
                    ['b' => 2],
                ]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        1  => [
                            0 => ['a' => 1],
                            1 => ['a' => 1],
                        ],
                        '' => [
                            0 => ['b' => 2],
                        ],
                    ]
                ]
            ],
            Chain::from($arr)->elems->elems->group->byField('a')->toArray(),
            ChainFunc::from($arr)->elems->elems->group->byField('a'),
        );


        $first = new stdClass();
        $first->value = 1;

        $second = new stdClass();
        $second->value = 2;

        $third = new stdClass();
        $third->value = 1;

        $fourth = new stdClass();
        $fourth->value = 3;

        $arr = [$first, $second, $third, $fourth];

        $this->assertEqualsMultiple(
            Func::groupToStruct($arr, fn($item) => $item),
            Chain::from($arr)->group->toStruct(fn($item) => $item)->toArray(),
            ChainFunc::from($arr)->group->toStruct(fn($item) => $item),
        );


        $arr = [
            'a' => [
                'a.a' => [$first, $second, $third, $fourth]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
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
                    ]
                ]
            ],
            Chain::from($arr)->elems->elems->group->toStruct(fn($item) => $item)->toArray(),
            ChainFunc::from($arr)->elems->elems->group->toStruct(fn($item) => $item),
        );
    }

    public function testClear(): void
    {
        $this->assertEqualsMultiple(
            [],
            Chain::from($this->flat)->clear()->toArray(),
            ChainFunc::from($this->flat)->clear(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => []
                ]
            ],
            Chain::from($this->nestedDepthSingled)->elems->elems->clear()->toArray(),
            ChainFunc::from($this->nestedDepthSingled)->elems->elems->clear(),
        );
    }

    public function testIs(): void
    {
        // empty
        $this->assertEqualsMultiple(
            Func::isEmpty([]),
            Chain::from([])->is->empty(),
            ChainFunc::from([])->is->empty(),
        );

        $arr = [
            'a' => [
                'a.a' => []
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => true
                ]
            ],
            Chain::from($arr)->elems->elems->is->empty()->toArray(),
            ChainFunc::from($arr)->elems->elems->is->empty(),
        );


        // notEmpty
        $this->assertEqualsMultiple(
            Func::isNotEmpty([]),
            Chain::from([])->is->notEmpty(),
            ChainFunc::from([])->is->notEmpty(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => false
                ]
            ],
            Chain::from($arr)->elems->elems->is->notEmpty()->toArray(),
            ChainFunc::from($arr)->elems->elems->is->notEmpty(),
        );


        // isEvery
        $arr = [1, 2, 3];
        $this->assertEqualsMultiple(
            Func::isEvery($arr, fn(int $item) => $item > 0),
            Chain::from($arr)->is->every(fn(int $item) => $item > 0),
            ChainFunc::from($arr)->is->every(fn(int $item) => $item > 0),
        );

        $arr = [
            'a' => [
                'a.a' => [1, 2, 3]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => true
                ]
            ],
            Chain::from($arr)->elems->elems->is->every(fn(int $item) => $item > 0)->toArray(),
            ChainFunc::from($arr)->elems->elems->is->every(fn(int $item) => $item > 0),
        );


        // isNone
        $arr = [1, 2, 3];
        $this->assertEqualsMultiple(
            Func::isNone($arr, fn(int $item) => $item > 2),
            Chain::from($arr)->is->none(fn(int $item) => $item > 2),
            ChainFunc::from($arr)->is->none(fn(int $item) => $item > 2),
        );

        $arr = [
            'a' => [
                'a.a' => [1, 2, 3]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => true
                ]
            ],
            Chain::from($arr)->elems->elems->is->none(fn(int $item) => $item > 100)->toArray(),
            ChainFunc::from($arr)->elems->elems->is->none(fn(int $item) => $item > 100),
        );


        // isSome
        $arr = [1, 2, 3];
        $this->assertEqualsMultiple(
            Func::isAny($arr, fn(int $item) => $item > 2),
            Chain::from($arr)->is->any(fn(int $item) => $item > 2),
            ChainFunc::from($arr)->is->any(fn(int $item) => $item > 2),
        );

        $arr = [
            'a' => [
                'a.a' => [1, 2, 3]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => true
                ]
            ],
            Chain::from($arr)->elems->elems->is->any(fn(int $item) => $item > 2)->toArray(),
            ChainFunc::from($arr)->elems->elems->is->any(fn(int $item) => $item > 2),
        );


        // isList
        $arr = [0 => 1, 1 => 2, 2 => 3];
        $this->assertEqualsMultiple(
            Func::isList($arr),
            Chain::from($arr)->is->list(),
            ChainFunc::from($arr)->is->list(),
        );

        $arr = [
            'a' => [
                'a.a' => [0 => 1, 1 => 2, 2 => 3]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => true
                ]
            ],
            Chain::from($arr)->elems->elems->is->list()->toArray(),
            ChainFunc::from($arr)->elems->elems->is->list(),
        );


        // isHasValue
        $arr = [1, 2, 3];
        $this->assertEqualsMultiple(
            Func::isHasValue($arr, 1, 10),
            Chain::from($arr)->is->hasValue(1, 10),
            ChainFunc::from($arr)->is->hasValue(1, 10),
        );

        $arr = [
            'a' => [
                'a.a' => [1, 2, 3]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => true
                ]
            ],
            Chain::from($arr)->elems->elems->is->hasValue(1, 10)->toArray(),
            ChainFunc::from($arr)->elems->elems->is->hasValue(1, 10),
        );


        // isFieldHasValue
        $arr = ['a' => 1, 'b' => 2];
        $this->assertEqualsMultiple(
            Func::isFieldHasValue($arr, 'a', 1, 10),
            Chain::from($arr)->is->fieldHasValue('a', 1, 10),
            ChainFunc::from($arr)->is->fieldHasValue('a', 1, 10),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => true
                ]
            ],
            Chain::from($arr)->elems->elems->is->fieldHasValue('a', 1, 10)->toArray(),
            ChainFunc::from($arr)->elems->elems->is->fieldHasValue('a', 1, 10),
        );


        // isHasKey
        $arr = ['a' => 1, 'b' => 2];
        $this->assertEqualsMultiple(
            Func::isHasKey($arr, 'a', 'd'),
            Chain::from($arr)->is->hasKey('a', 'd'),
            ChainFunc::from($arr)->is->hasKey('a', 'd'),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => true
                ]
            ],
            Chain::from($arr)->elems->elems->is->hasKey('a', 'd')->toArray(),
            ChainFunc::from($arr)->elems->elems->is->hasKey('a', 'd'),
        );
    }

    public function testChunk(): void
    {
        $arr = [1, 2, 3, 4, 5];

        $this->assertEqualsMultiple(
            Func::chunkBySize($arr, 2),
            Chain::from($arr)->chunk->bySize(2)->toArray(),
            ChainFunc::from($arr)->chunk->bySize(2),
        );
        $this->assertEqualsMultiple(
            Func::chunkBySize($arr, 2, true),
            Chain::from($arr)->chunk->bySize(2, true)->toArray(),
            ChainFunc::from($arr)->chunk->bySize(2, true),
        );
        $this->assertEqualsMultiple(
            Func::chunkByCount($arr, 3),
            Chain::from($arr)->chunk->byCount(3)->toArray(),
            ChainFunc::from($arr)->chunk->byCount(3),
        );
        $this->assertEqualsMultiple(
            Func::chunkByCount($arr, 3, true),
            Chain::from($arr)->chunk->byCount(3, true)->toArray(),
            ChainFunc::from($arr)->chunk->byCount(3, true),
        );

        $arr = [
            'a' => [
                'a.a' => [1, 2, 3, 4, 5]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [[1, 2], [3, 4], [5]]
                ]
            ],
            Chain::from($arr)->elems->elems->chunk->bySize(2)->toArray(),
            ChainFunc::from($arr)->elems->elems->chunk->bySize(2),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [[1, 2], [3, 4], [5]]
                ]
            ],
            Chain::from($arr)->elems->elems->chunk->byCount(3)->toArray(),
            ChainFunc::from($arr)->elems->elems->chunk->byCount(3),
        );
    }

    public function testFlip(): void
    {
        $arr = ['a' => 10, 'b' => 20, 'c' => 30];
        $this->assertEqualsMultiple(
            Func::flip($arr),
            Chain::from($arr)->flip()->toArray(),
            ChainFunc::from($arr)->flip(),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 10, 'b' => 20, 'c' => 30]
            ]
        ];


        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => ['10' => 'a', '20' => 'b', '30' => 'c']
                ]
            ],
            Chain::from($arr)->elems->elems->flip()->toArray(),
            ChainFunc::from($arr)->elems->elems->flip(),
        );
    }

    public function testShift(): void
    {
        $arrFunc = [1, 2, 3];

        $arr = [1, 2, 3];
        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            Func::shift($arrFunc),
            $ch->shift(),
            $cf->shift(),
        );

        $this->assertEqualsMultiple(
            $arrFunc,
            $ch->toArray(),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2, 3],
                'a.b' => [4, 5, 6],
            ]
        ];


        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            [1, 4],
            $ch->elems->elems->shift(),
            $cf->elems->elems->shift(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [2, 3],
                    'a.b' => [5, 6],
                ]
            ],
            $ch->toArray(),
        );
    }

    public function testPop(): void
    {
        $arrFunc = [1, 2, 3];

        $arr = [1, 2, 3];
        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            Func::pop($arrFunc),
            $ch->pop(),
            $cf->pop(),
        );

        $this->assertEqualsMultiple(
            $arrFunc,
            $ch->toArray(),
            $cf->toArray(),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2, 3],
                'a.b' => [4, 5, 6],
            ]
        ];


        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            [3, 6],
            $ch->elems->elems->pop(),
            $cf->elems->elems->pop(),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2],
                    'a.b' => [4, 5],
                ]
            ],
            $ch->toArray(),
        );
    }

    public function testSplice(): void
    {
        $arrFunc = [1, 2, 3, 4, 5];

        $arr = [1, 2, 3, 4, 5];
        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            Func::splice($arrFunc, 2, 1, 'item'),
            $ch->splice(2, 1, 'item'),
            $cf->splice(2, 1, 'item'),
        );

        $this->assertEqualsMultiple(
            $arrFunc,
            $ch->toArray(),
            $cf->toArray(),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2, 3, 4],
                'a.b' => [5, 6, 7, 8],
            ]
        ];
        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            [
                [3],
                [7]
            ],
            $ch->elems->elems->splice(2, 1, 'item'),
            $cf->elems->elems->splice(2, 1, 'item'),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 'item', 4],
                    'a.b' => [5, 6, 'item', 8],
                ]
            ],
            $ch->toArray(),
            $cf->toArray(),
        );
    }

    public function testSpliceHead(): void
    {
        $arrFunc = [1, 2, 3, 4, 5];

        $arr = [1, 2, 3, 4, 5];
        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            Func::spliceHead($arrFunc, 2, 'item'),
            $ch->splice->head(2, 'item'),
            $cf->splice->head(2, 'item'),
        );

        $this->assertEqualsMultiple(
            $arrFunc,
            $ch->toArray(),
            $cf->toArray(),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2, 3, 4],
                'a.b' => [5, 6, 7, 8],
            ]
        ];
        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            [
                [1, 2],
                [5, 6]
            ],
            $ch->elems->elems->splice->head(2, 'item'),
            $cf->elems->elems->splice->head(2, 'item'),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => ['item', 3, 4],
                    'a.b' => ['item', 7, 8],
                ]
            ],
            $ch->toArray(),
            $cf->toArray(),
        );
    }

    public function testSpliceTail(): void
    {
        $arrFunc = [1, 2, 3, 4, 5];

        $arr = [1, 2, 3, 4, 5];
        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            Func::spliceTail($arrFunc, 2, 'item'),
            $ch->splice->tail(2, 'item'),
            $cf->splice->tail(2, 'item'),
        );

        $this->assertEqualsMultiple(
            $arrFunc,
            $ch->toArray(),
            $cf->toArray(),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2, 3, 4],
                'a.b' => [5, 6, 7, 8],
            ]
        ];
        $ch = Chain::from($arr);
        $cf = ChainFunc::from($arr);

        $this->assertEqualsMultiple(
            [
                [3, 4],
                [7, 8]
            ],
            $ch->elems->elems->splice->tail(2, 'item'),
            $cf->elems->elems->splice->tail(2, 'item'),
        );

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 'item'],
                    'a.b' => [5, 6, 'item'],
                ]
            ],
            $ch->toArray(),
            $cf->toArray(),
        );
    }

    public function testSlice(): void
    {
        $arr = [10 => 1, 2, 3];

        $this->assertEqualsMultiple(
            Func::slice($arr, 1, 2, true),
            Chain::from($arr)->slice(1, 2, true)->toArray(),
            ChainFunc::from($arr)->slice(1, 2, true),
        );
        $this->assertEqualsMultiple(
            Func::sliceHead($arr, 2, true),
            Chain::from($arr)->slice->head(2, true)->toArray(),
            ChainFunc::from($arr)->slice->head(2, true),
        );
        $this->assertEqualsMultiple(
            Func::sliceTail($arr, 2, true),
            Chain::from($arr)->slice->tail(2, true)->toArray(),
            ChainFunc::from($arr)->slice->tail(2, true),
        );


        $arr = [
            'a' => [
                'a.a' => [10 => 1, 2, 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [2, 3]
                ]
            ],
            Chain::from($arr)->elems->elems->slice(1, 2)->toArray(),
            ChainFunc::from($arr)->elems->elems->slice(1, 2),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2]
                ]
            ],
            Chain::from($arr)->elems->elems->slice->head(2)->toArray(),
            ChainFunc::from($arr)->elems->elems->slice->head(2),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [2, 3]
                ]
            ],
            Chain::from($arr)->elems->elems->slice->tail(2)->toArray(),
            ChainFunc::from($arr)->elems->elems->slice->tail(2),
        );
    }

    public function testReplace(): void
    {
        $arr = [1, 2, 3, 4, 5];
        $this->assertEqualsMultiple(
            Func::replace($arr, [6, 7], [4 => 8]),
            Chain::from($arr)->replace([6, 7], [4 => 8])->toArray(),
            ChainFunc::from($arr)->replace([6, 7], [4 => 8]),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2, 3, 4, 5]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [6, 7, 3, 4, 8]
                ]
            ],
            Chain::from($arr)->elems->elems->replace([6, 7], [4 => 8])->toArray(),
            ChainFunc::from($arr)->elems->elems->replace([6, 7], [4 => 8]),
        );


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
        $this->assertEqualsMultiple(
            Func::replaceRecursive($arr, $arrReplace1, $arrReplace2),
            Chain::from($arr)->replace->recursive($arrReplace1, $arrReplace2)->toArray(),
            ChainFunc::from($arr)->replace->recursive($arrReplace1, $arrReplace2),
        );


        $arr = [
            'a' => [
                'a.a' => [
                    [1, 2, 3],
                    [4, 5, 6],
                ]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [
                        [1, 2, 3],
                        [4, 7, 9],
                    ]
                ]
            ],
            Chain::from($arr)->elems->elems->replace->recursive($arrReplace1, $arrReplace2)->toArray(),
            ChainFunc::from($arr)->elems->elems->replace->recursive($arrReplace1, $arrReplace2),
        );
    }

    public function testFlatten(): void
    {
        $arr = [1, [2], [3, [4, [5]]]];

        $this->assertEqualsMultiple(
            Func::flatten($arr),
            Chain::from($arr)->flatten()->toArray(),
            ChainFunc::from($arr)->flatten(),
        );
        $this->assertEqualsMultiple(
            Func::flatten($arr, 2),
            Chain::from($arr)->flatten(2)->toArray(),
            ChainFunc::from($arr)->flatten(2),
        );
        $this->assertEqualsMultiple(
            Func::flatten($arr, 3),
            Chain::from($arr)->flatten(3)->toArray(),
            ChainFunc::from($arr)->flatten(3),
        );
        $this->assertEqualsMultiple(
            Func::flattenAll($arr),
            Chain::from($arr)->flatten->all()->toArray(),
            ChainFunc::from($arr)->flatten->all(),
        );


        $arr = [
            'a' => [
                'a.a' => [1, [2], [3, [4, [5]]]]
            ]
        ];
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 3, 4, [5]]
                ]
            ],
            Chain::from($arr)->elems->elems->flatten(2)->toArray(),
            ChainFunc::from($arr)->elems->elems->flatten(2),
        );
        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 3, 4, 5]
                ]
            ],
            Chain::from($arr)->elems->elems->flatten->all()->toArray(),
            ChainFunc::from($arr)->elems->elems->flatten->all(),
        );
    }

    public function testPad(): void
    {
        $arr = [1, 2];

        $this->assertEqualsMultiple(
            Func::pad($arr, 5, 0),
            Chain::from($arr)->pad(5, 0)->toArray(),
            ChainFunc::from($arr)->pad(5, 0),
        );
        $this->assertEqualsMultiple(
            Func::pad($arr, -5, 0),
            Chain::from($arr)->pad(-5, 0)->toArray(),
            ChainFunc::from($arr)->pad(-5, 0),
        );


        $arr = [
            'a' => [
                'a.a' => [1, 2]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => [1, 2, 0, 0, 0]
                ]
            ],
            Chain::from($arr)->elems->elems->pad(5, 0)->toArray(),
            ChainFunc::from($arr)->elems->elems->pad(5, 0),
        );
    }

    public function testGet(): void
    {
        $arr = [1, 2, 3];

        $this->assertEqualsMultiple(
            Func::get($arr, 1),
            Chain::from($arr)->get(1),
            ChainFunc::from($arr)->get(1),
        );
        $this->assertEqualsMultiple(
            Func::get($arr, 10),
            Chain::from($arr)->get(10),
            ChainFunc::from($arr)->get(10),
        );

        $arr = [
            'a' => [
                'a.a' => [1, 2, 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 2
                ]
            ],
            Chain::from($arr)->elems->elems->get(1)->toArray(),
            ChainFunc::from($arr)->elems->elems->get(1),
        );

        // getOrElse

        $arr = [1, 2, 3];

        $this->assertEqualsMultiple(
            Func::getOrElse($arr, 1, 'else'),
            Chain::from($arr)->get->orElse(1, 'else'),
            ChainFunc::from($arr)->get->orElse(1, 'else'),
        );
        $this->assertEqualsMultiple(
            Func::getOrElse($arr, 10, 'else'),
            Chain::from($arr)->get->orElse(10, 'else'),
            ChainFunc::from($arr)->get->orElse(10, 'else'),
        );

        $arr = [
            'a' => [
                'a.a' => [1, 2, 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 2
                ]
            ],
            Chain::from($arr)->elems->elems->get->orElse(1, 'else')->toArray(),
            ChainFunc::from($arr)->elems->elems->get->orElse(1, 'else'),
        );

        // getOrException

        $arr = [1, 2, 3];

        $this->assertEqualsMultiple(
            Func::getOrException($arr, 1),
            Chain::from($arr)->get->orException(1),
            ChainFunc::from($arr)->get->orException(1),
        );

//        $this->expectException(NotFoundException::class);
//        Chain::from($arr)->get->orException(10);
//        ChainFunc::from($arr)->get->orException(10);

        // getByNumber

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEqualsMultiple(
            Func::getByNumber($arr, 1),
            Chain::from($arr)->get->byNumber(1),
            ChainFunc::from($arr)->get->byNumber(1),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 2
                ]
            ],
            Chain::from($arr)->elems->elems->get->byNumber(1)->toArray(),
            ChainFunc::from($arr)->elems->elems->get->byNumber(1),
        );

        // byNumberOrElse

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEqualsMultiple(
            Func::getByNumberOrElse($arr, 1, 'else'),
            Chain::from($arr)->get->byNumberOrElse(1, 'else'),
            ChainFunc::from($arr)->get->byNumberOrElse(1, 'else'),
        );
        $this->assertEqualsMultiple(
            Func::getOrElse($arr, 10, 'else'),
            Chain::from($arr)->get->byNumberOrElse(10, 'else'),
            ChainFunc::from($arr)->get->byNumberOrElse(10, 'else'),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 2
                ]
            ],
            Chain::from($arr)->elems->elems->get->byNumberOrElse(1, 'else')->toArray(),
            ChainFunc::from($arr)->elems->elems->get->byNumberOrElse(1, 'else'),
        );

        // byNumberOrException

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertEqualsMultiple(
            Func::getByNumberOrException($arr, 1),
            Chain::from($arr)->get->byNumberOrException(1),
            ChainFunc::from($arr)->get->byNumberOrException(1),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 2
                ]
            ],
            Chain::from($arr)->elems->elems->get->byNumberOrException(1)->toArray(),
            ChainFunc::from($arr)->elems->elems->get->byNumberOrException(1),
        );

        // first

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEqualsMultiple(
            Func::getFirst($arr),
            Chain::from($arr)->get->first(),
            ChainFunc::from($arr)->get->first(),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 1
                ]
            ],
            Chain::from($arr)->elems->elems->get->first()->toArray(),
            ChainFunc::from($arr)->elems->elems->get->first(),
        );

        // firstOrElse

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEqualsMultiple(
            Func::getFirstOrElse($arr, 'else'),
            Chain::from($arr)->get->firstOrElse('else'),
            ChainFunc::from($arr)->get->firstOrElse('else'),
        );
        $this->assertEqualsMultiple(
            Func::getFirstOrElse([], 'else'),
            Chain::from([])->get->firstOrElse('else'),
            ChainFunc::from([])->get->firstOrElse('else'),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 1
                ]
            ],
            Chain::from($arr)->elems->elems->get->firstOrElse('else')->toArray(),
            ChainFunc::from($arr)->elems->elems->get->firstOrElse('else'),
        );

        // first or exception

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEqualsMultiple(
            Func::getFirstOrException($arr),
            Chain::from($arr)->get->firstOrException(),
            ChainFunc::from($arr)->get->firstOrException(),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 1
                ]
            ],
            Chain::from($arr)->elems->elems->get->firstOrException()->toArray(),
            ChainFunc::from($arr)->elems->elems->get->firstOrException(),
        );

        // last

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEqualsMultiple(
            Func::getLast($arr),
            Chain::from($arr)->get->last(),
            ChainFunc::from($arr)->get->last(),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 3
                ]
            ],
            Chain::from($arr)->elems->elems->get->last()->toArray(),
            ChainFunc::from($arr)->elems->elems->get->last(),
        );

        // lastOrElse

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEqualsMultiple(
            Func::getLastOrElse($arr, 'else'),
            Chain::from($arr)->get->lastOrElse('else'),
            ChainFunc::from($arr)->get->lastOrElse('else'),
        );
        $this->assertEqualsMultiple(
            Func::getLastOrElse([], 'else'),
            Chain::from([])->get->lastOrElse('else'),
            ChainFunc::from([])->get->lastOrElse('else'),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 3
                ]
            ],
            Chain::from($arr)->elems->elems->get->lastOrElse('else')->toArray(),
            ChainFunc::from($arr)->elems->elems->get->lastOrElse('else'),
        );

        // last or exception

        $arr = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEqualsMultiple(
            Func::getLastOrException($arr),
            Chain::from($arr)->get->lastOrException(),
            ChainFunc::from($arr)->get->lastOrException(),
        );

        $arr = [
            'a' => [
                'a.a' => ['a' => 1, 'b' => 2, 'c' => 3]
            ]
        ];

        $this->assertEqualsMultiple(
            [
                'a' => [
                    'a.a' => 3
                ]
            ],
            Chain::from($arr)->elems->elems->get->lastOrException()->toArray(),
            ChainFunc::from($arr)->elems->elems->get->lastOrException(),
        );

    }
}