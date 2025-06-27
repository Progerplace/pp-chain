Работа с массивами и коллекциями. Использование цепочек методов, единый список функций для разных ситуаций.

Php >= 7.4

## Использование {#ispolzovanie}

Для большинства функций доступно 3 способа использования:

```php
// 1. Вызов цепочкой:
Chain::from([1,2,3])
    ->filter(fn($item) => $item > 2)
    ->map(fn($item) => $item - 2)
    ->values()
    ->toArray()
    
// 2. Вызов одной функции:
ChainFunc::from([1,2,3])->values()
    
// 3. Вызов одной функции напрямую для массива:
Func::values([1,2,3]);
```

### Создание объекта {#sozdanie_obekta}

```php
// Объект может быть создан из итерируемого объекта (iterable). Во внутреннем представлении он будет преобразован в массив. Методы класса Func принимают только массивы.
Chain::from([1,2,3])
ChainFunc::from([1,2,3])

// Создание из json:
Chain::fromJson('[1,2,3]');
ChainFunc::fromJson('[1,2,3]');

// Создание из строки:
Chain::fromString('1,2,3', ',');
ChainFunc::fromString('1,2,3', ',');

// Создание из диапазона
Chain::fromRange(1,4)->toArray();
ChainFunc::fromRange(1,4)->toArray();
```

### Экспорт объекта Chain {#eksport_obekta_chain}

```php
// В массив
Chain::from([1,2,3])->toArray();

// В json
Chain::from([1,2,3])->toJson();

// В строку
Chain::from([1,2,3])->toString(',');
```

### Работа с дочерними элементами {#rabota_s_dochernimi_elementami}

Можно задавать глубину элементов для обработки обращением к полю `elems`

```php
$arrFirst = [1,2];
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
// ]
```

### Использование алиасов {#ispolzovanie_aliasov}

Для краткости можно использовать алиасы для основных классов:

```text
Ch -> Chain
Cf -> ChainFunc
F  -> Func
```

А также вспомогательные функции для замены метода `from`:
```text
Ch(...) -> Chain::from(...)
Cf(...) -> ChainFunc::from(...)
```

Пример для всех доступных вариантов:

```php
use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Ch;
use Ru\Progerplace\Chain\Cf;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\F;
use function Ru\Progerplace\Chain\Cf;
use function Ru\Progerplace\Chain\Ch;

Chain::from($arr)->map(fn(int $item) => $item + 1)->toArray();
Ch::from($arr)->map(fn(int $item) => $item + 1)->toArray();
Ch($arr)->map(fn(int $item) => $item + 1)->toArray();

ChainFunc::from($arr)->map(fn(int $item) => $item + 1);
Cf::from($arr)->map(fn(int $item) => $item + 1);
Cf($arr)->map(fn(int $item) => $item + 1);

Func::map($arr, fn(int $item) => $item + 1);
F::map($arr, fn(int $item) => $item + 1);
```