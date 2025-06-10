<?php

require_once __DIR__ . '/../vendor/autoload.php';

class_alias('Ru\Progerplace\Chain\Chain', 'Ru\Progerplace\Chain\Ch');
class_alias('Ru\Progerplace\Chain\ChainFunc', 'Ru\Progerplace\Chain\Cf');
class_alias('Ru\Progerplace\Chain\Func', 'Ru\Progerplace\Chain\F');

function ChFrom(?iterable $var, $default = []): Ru\Progerplace\Chain\Chain
{
    return Ru\Progerplace\Chain\Chain::from($var, $default);
}