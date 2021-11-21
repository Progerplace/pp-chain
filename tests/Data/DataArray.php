<?php

namespace Data;

class DataArray
{
    public string $strDelimiter = ',';

    public array $base = ['first', 'second', 'third'];

    public array $withKeys = [
        'f' => 'first',
        's' => 'second',
        't' => 'third'
    ];

    public array $withKeysUpper = [
        'F' => 'first',
        'S' => 'second',
        'T' => 'third'
    ];

    public array $snakeCase = [
        'first_elem'  => 1,
        'second_elem' => 2,
        'third_elem'  => 3
    ];

    public array $camelCase = [
        'firstElem'  => 1,
        'secondElem' => 2,
        'thirdElem'  => 3
    ];

    public array $keysFromArrayWithKeys = ['f', 's', 't'];

    public array $number = [1, 2, 3];

    public string $strFromArray          = 'firstsecondthird';
    public string $strFromArrayDelimiter = 'first,second,third';

    public string $jsonFromArray         = '["first","second","third"]';
    public string $jsonFromArrayWithKeys = '{"f":"first","s":"second","t":"third"}';
}