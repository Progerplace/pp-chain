<?php

use Ru\Progerplace\Chain\Cf;
use Ru\Progerplace\Chain\Ch;
use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\F;
use Ru\Progerplace\Chain\Func;
use function Ru\Progerplace\Chain\Cf;
use function Ru\Progerplace\Chain\Ch;

require_once __DIR__ . '/../vendor/autoload.php';
//require_once __DIR__ . '/../app-files/aliases.php';

$arr = [1, 2, 3];
//$arr = [
//    [1, 2, 3],
//    [4, 5, 6]
//];
//$arr = [
//    ['a' => 1, 'b' => 2, 'c' => 3],
//    ['a' => 4, 'b' => 5, 'c' => 6, 'd' => 7],
//];


Chain::from($arr)->map(fn(int $item) => $item + 1)->toArray();
Ch::from($arr)->map(fn(int $item) => $item + 1)->toArray();
Ch($arr)->map(fn(int $item) => $item + 1)->toArray();

ChainFunc::from($arr)->map(fn(int $item) => $item + 1);
Cf::from($arr)->map(fn(int $item) => $item + 1);
Cf($arr)->map(fn(int $item) => $item + 1);

Func::map($arr, fn(int $item) => $item + 1);
F::map($arr, fn(int $item) => $item + 1);



//$res = Ch($arr)->map(fn($item) => $item + 10)->toArray();
//$res = Cf($arr)->map(fn($item) => $item + 20);


//$res = Ch::from($arr)
//    ->map(fn($item) => $item)->toArray();
//$r = Func::map($arr, fn($item) => $item);

//$res = ChFrom([1, 2, 3])->toArray();

//$res = ChainFunc::from($arr)->math->min();
//$res = ChainFunc::from($arr)->elems->math->min();
//$res = ChainFunc::from($arr)->elems->math->min()->toArray();
//$res = Chain::from($arr)->math->min->by(fn($item) => $item['a']);
//$res = Chain::from($arr)->math->min->byField('d');

print_r($res);
echo PHP_EOL;

/**
 *
 * Chain::from([])->math->min()
 * Chain::from([])->math->min->by()
 * Chain::from([])->math->min->byField()
 */


//$arr = [1, 2, 3, 4, 5];
//$ch = Chain::from($arr);
//$cf = ChainFunc::from($arr);
//print_r($cf->splice->head(2, 'item'));
//echo PHP_EOL;

//$arr = [
//    'a' => [
//        'a.a' => [1, 2, 3],
//        'a.b' => [4, 5, 6],
//    ]
//];
//$arr = [1, 2, 3];
//
//$res = array_splice($arr, 1, 3);
//
//print_r($res);
//echo PHP_EOL;
//print_r('----------------------------------------------------------');
//echo PHP_EOL;
//print_r($arr);
//echo PHP_EOL;

