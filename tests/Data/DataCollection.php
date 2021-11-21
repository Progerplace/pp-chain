<?php

namespace Data;

class DataCollection
{
    public array $base = [
        ['first', 'second', 'third'],
        ['first', 'second', 'third'],
        ['first', 'second', 'third'],
    ];

    public array $number = [
        [1, 2, 3],
        [1, 2, 3],
        [1, 2, 3]
    ];

    public array $withKeys = [
        'f' => ['f' => 'first', 's' => 'second', 't' => 'third'],
        's' => ['f' => 'first', 's' => 'second', 't' => 'third'],
        't' => ['f' => 'first', 's' => 'second', 't' => 'third'],
    ];

    public array $withElemsKeys = [
        ['f' => 'first', 's' => 'second', 't' => 'third'],
        ['f' => 'first', 's' => 'second', 't' => 'third'],
        ['f' => 'first', 's' => 'second', 't' => 'third'],
    ];

    public array $keysFromArrayWithKeys = [
        ['f', 's', 't'],
        ['f', 's', 't'],
        ['f', 's', 't'],
    ];
}