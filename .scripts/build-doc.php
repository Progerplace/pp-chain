<?php

use phpowermove\docblock\Docblock;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainOuter;
use Ru\Progerplace\Chain\Func;

require_once __DIR__ . '/../vendor/autoload.php';

$targetFile = __DIR__ . '/../.data-project/build-doc/index.html';


$blocks = [
    'Append'    => [
        [Func::class, 'append'],
        'Append merge'             => [Func::class, 'appendMerge'],
        'Append merge from json'   => [Func::class, 'appendMergeFromJson'],
        'Append merge from string' => [Func::class, 'appendMergeFromString'],
    ],
    'Prepend'   => [
        [Func::class, 'prepend'],
        'Prepend merge'             => [Func::class, 'prependMerge'],
        'Prepend merge from json'   => [Func::class, 'prependMergeFromJson'],
        'Prepend merge from string' => [Func::class, 'prependMergeFromString'],
    ],
    'Map'       => [
        [Func::class, 'map']
    ],
    'Filter'    => [
        [Func::class, 'filter'],
        'Filter keys'   => [Func::class, 'filterKeys'],
        'Filter values' => [Func::class, 'filterValues'],
    ],
    'Reject'    => [
        [Func::class, 'reject'],
        'Reject null'   => [Func::class, 'rejectNull'],
        'Reject empty'  => [Func::class, 'rejectEmpty'],
        'Reject keys'   => [Func::class, 'rejectKeys'],
        'Reject values' => [Func::class, 'rejectValues'],
    ],
    'Group'     => [
        [Func::class, 'group'],
        'Group by field'  => [Func::class, 'groupByField'],
        'Group to struct' => [Func::class, 'groupToStruct'],
    ],
    'Values'    => [
        [Func::class, 'values'],
        'Values get list' => [Func::class, 'valuesGetList']
    ],
    'Reverse'   => [
        [Func::class, 'reverse']
    ],
    'Keys'      => [
        [Func::class, 'keys'],
        'Keys list'       => [Func::class, 'keysGetList'],
        'Keys get'        => [Func::class, 'keysGet'],
        'Keys get first'  => [Func::class, 'keysGetFirst'],
        'Keys get last'   => [Func::class, 'keysGetLast'],
        'Keys map'        => [Func::class, 'keysMap'],
        'Keys from field' => [Func::class, 'keysFromField'],
    ],
    'Keys case' => [
        'To camel case'        => [Func::class, 'keysCaseToCamel'],
        'To paskal case'       => [Func::class, 'keysCaseToPaskal'],
        'To snake case'        => [Func::class, 'keysCaseToSnake'],
        'To kebab case'        => [Func::class, 'keysCaseToKebab'],
        'To scream snake case' => [Func::class, 'keysCaseToScreamSnake'],
        'To scream kebab case' => [Func::class, 'keysCaseToScreamKebab'],
    ],
    'Unique'    => [
        [Func::class, 'unique'],
        'Unique by' => [Func::class, 'uniqueBy'],
    ],
    'Reduce'    => [
        [Func::class, 'reduce']
    ],
    'Json'      => [
        'Json encode fields' => [Func::class, 'jsonEncodeFields'],
        'Json encode by'     => [Func::class, 'jsonEncodeBy'],
        'Json decode fields' => [Func::class, 'jsonDecodeFields'],
        'Json decode by'     => [Func::class, 'jsonDecodeBy'],
    ],
    'Each'      => [
        [Func::class, 'each']
    ],
    'Count'     => [
        [Func::class, 'count']
    ],
    'Find'      => [
        [Func::class, 'find']
    ],
    'Sort'      => [
        [Func::class, 'sort']
    ],
    'Slice'     => [
        [Func::class, 'slice'],
        'Slice head' => [Func::class, 'sliceHead'],
        'Slice tail' => [Func::class, 'sliceTail'],
    ],
    'Splice'    => [
        [Func::class, 'splice'],
        'Splice head' => [Func::class, 'spliceHead'],
        'Splice tail' => [Func::class, 'spliceTail'],
    ],
    'Replace'   => [
        [Func::class, 'replace'],
        'Replace recursive' => [Func::class, 'replaceRecursive'],
    ],
    'Is'        => [
        'Is empty'           => [Func::class, 'isEmpty'],
        'Is not empty'       => [Func::class, 'isNotEmpty'],
        'Is every'           => [Func::class, 'isEvery'],
        'Is none'            => [Func::class, 'isNone'],
        'Is any'             => [Func::class, 'isAny'],
        'Is list'            => [Func::class, 'isList'],
        'Is has value'       => [Func::class, 'isHasValue'],
        'Is field has value' => [Func::class, 'isFieldHasValue'],
        'Is has key'         => [Func::class, 'isHasKey'],
    ],
    'Chunk'     => [
        'Chunk by size'  => [Func::class, 'chunkBySize'],
        'Chunk by count' => [Func::class, 'chunkByCount'],
    ],
    'Clear'     => [
        [Func::class, 'clear']
    ],
    'Flip'      => [
        [Func::class, 'flip']
    ],
    'Get'       => [
        [Func::class, 'get'],
        'Get or else'                => [Func::class, 'getOrElse'],
        'Get or exception'           => [Func::class, 'getOrException'],
        'Get by number'              => [Func::class, 'getByNumber'],
        'Get by number or else'      => [Func::class, 'getByNumberOrElse'],
        'Get by number or exception' => [Func::class, 'getByNumberOrException'],
        'Get first'                  => [Func::class, 'getFirst'],
        'Get first or else'          => [Func::class, 'getFirstOrElse'],
        'Get first or exception'     => [Func::class, 'getFirstOrException'],
        'Get last'                   => [Func::class, 'getLast'],
        'Get last or else'           => [Func::class, 'getLastOrElse'],
        'Get last or exception'      => [Func::class, 'getLastOrException'],
    ],
    'Shift'     => [
        [Func::class, 'shift']
    ],
    'Pop'       => [
        [Func::class, 'pop']
    ],
    'Flatten'   => [
        [Func::class, 'flatten'],
        'Flatten all' => [Func::class, 'flattenAll'],
    ],
    'Pad'       => [
        [Func::class, 'pad']
    ],
    'Outer'     => [
        'ReplaceWith' => [ChainOuter::class, 'replaceWith'],
        'Is'          => [ChainOuter::class, 'is'],
        'Change'      => [ChainOuter::class, 'change'],
        'Print'       => [ChainOuter::class, 'print'],
    ],
];


foreach ($blocks as $title => &$items) {
    $items = blockToHtml($title, $items);
}

$htmlSidebar = buildSidebarHtml($blocks);
$htmlMain = buildMainHtml($blocks);
$tpl = getTpl();
$html = sprintf($tpl, $htmlSidebar, $htmlMain);
file_put_contents($targetFile, $html);

function buildSidebarHtml(array $items): string
{
    $res = '<ul class="sidebar__nav">';

    foreach ($items as $title => $val) {
        $id = strtolower($title);
        $res .= '<li>';
        $res .= sprintf('<a href="#%s">%s</a>', $id, $title);
        $res .= '</li>';
    }

    $res .= '</ul>';

    return $res;
}

function buildMainHtml(array $items): string
{
    return implode('', $items);
}

function blockToHtml(string $title, array $items): string
{
    $id = strtolower($title);

    $res = '';
    $res .= '<section id="' . $id . '" class="section">';
    $res .= '<h2 class="section__title">' . $title . '</h2>';

    $res .= '<div class="methods">';

    foreach ($items as $itemTitle => $item) {
        $doc = (new ReflectionMethod($item[0], $item[1]))->getDocComment();
        $html = docBlockToHtml($doc);

        $html = str_replace('<pre>', '<pre class="language-php">', $html);

        $res .= '<div class="method">';
        if (!is_int($itemTitle)) {
            $res .= '<h3 class="method__title">' . $itemTitle . '</h3>';
        }
        $res .= '<div class="method__cnt">';
        $res .= $html;
        $res .= '</div>';
        $res .= '</div>';
    }

    $res .= '</div>';
    $res .= '</section>';

    return $res;
}

function docBlockToHtml(string $doc): string
{
    $docblock = new Docblock($doc);
    $parsedown = new Parsedown();
    $descShort = $parsedown->text($docblock->getShortDescription());
    $descLong = $parsedown->text($docblock->getLongDescription());

    return $descShort . $descLong;
}

function getTpl(): string
{
    return '
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Progerplace Chain</title>
    
    <link rel="stylesheet" href="prism.css">
    <link rel="stylesheet" href="style.css">
    <script src="prism.js" defer></script>
    <script src="js.js" defer></script>
</head>
<body>
<div id="about"></div>

<div class="main-grid">
    <div class="main-grid__sidebar">
        <a href="#about" class="sidebar__title-main">Progerplace chain</a>
        <div class="sidebar__methods">%s</div>        
    </div>
    <div class="main-grid__cnt">
    
        <section  class="section">
            <h1 class="section__title">Progerplace chain</h1>
            <div class="about__desc">
                <p>Работа с массивами и коллекциями. Использование цепочек методов, единый список функций для разных ситуаций.</p>
                <br>
                <p>Php >= 7.4</p>
                <h3>Использование</h3>
                <p>
                Есть 3 способа использования:
                <br>
                <pre class="language-php"><code>// 1. Вызов одной функции:
ChainFunc::from([1,2,3])->values()

// 2. Вызов цепочкой:
Chain::from(1,2,3)
    ->filter(fn($item) => $item > 2)
    ->map(fn($item) => $item - 2)
    ->values()
    ->toArray()
    
// 3. Вызов одной функции напрямую для массива:
Func::values([1,2,3]);</code></pre>                
                </p>
                
                <h4>Создание объекта</h4>
                <pre class="language-php"><code>// Объект может быть создан из итерируемого объекта (iterable). Во внутреннем представлении он будет преобразован в массив. Методы класса Func принимают только массивы.
Chain::from([1,2,3])
ChainFunc::from([1,2,3])

// Создание из json:
Chain::fromJson(\'[1,2,3]\');
ChainFunc::fromJson(\'[1,2,3]\');

// Создание из строки:
Chain::fromString(\'1,2,3\', \',\');
ChainFunc::fromString(\'1,2,3\', \',\');

// Создание из диапазона
Chain::fromRange(1,4)->toArray();
ChainFunc::fromRange(1,4)->toArray();</code></pre>

<h4>Экспорт объекта Chain</h4>
<pre class="language-php"><code>// В массив
Chain::from([1,2,3])->toArray();

// В json
Chain::from([1,2,3])->toJson();

// В строку
Chain::from([1,2,3])->toString(\',\');</code></pre>

<h4>Работа с дочерними элементами</h4>
Можно задавать глубину элементов для обработки обращением к полю <code>elems</code>
<pre class="language-php"><code>$arrFirst = [1,2];
$arrSecond = [
    [1,2],
    [3,4]
]
$arrThird = [
    [
        [1,2],
        [3,4]
    ],
    [
        [1,2],
        [3,4]
    ]
]

Chain::from($arrSecond)->map(fn($item) => $item + 5)->toArray()
ChainFunc::from($arrSecond)->map(fn($item) => $item + 5)
// [6,7]

Chain::from($arrSecond)->elems->map(fn($item) => $item + 5)->toArray()
ChainFunc::from($arrSecond)->elems->map(fn($item) => $item + 5)
// $arrSecond = [
//     [6,7],
//     [8,9]
// ]

Chain::from($arrThird)->elems->elems->map(fn($item) => $item + 5)->toArray()
ChainFunc::from($arrThird)->elems->elems->map(fn($item) => $item + 5)
// $arrThird = [
//     [
//         [1,2],
//         [3,4]
//     ],
//     [
//         [1,2],
//         [3,4]
//     ]
// ]</code></pre>

                <h4>Использование алиасов</h4>
                Для краткости можно использовать алиасы для основных классов:
<pre class="language-"><code>Ch -> Chain
Cf -> ChainFunc
F  -> Func</code></pre>                
          
            </div>
        </section>
        <div class="methods-title">Методы</div>
        <div id="methods"></div>
        %s
    </div>
</div>
</body>
</html>
';
}
