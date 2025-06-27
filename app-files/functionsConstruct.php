<?php

namespace Ru\Progerplace\Chain;

class_alias('Ru\Progerplace\Chain\Chain', 'Ru\Progerplace\Chain\Ch');
class_alias('Ru\Progerplace\Chain\ChainFunc', 'Ru\Progerplace\Chain\Cf');
class_alias('Ru\Progerplace\Chain\Func', 'Ru\Progerplace\Chain\F');

function Ch(?iterable $var, $default = []): Chain
{
    return Chain::from($var, $default);
}

function Cf(?iterable $var, $default = []): ChainFunc
{
    return ChainFunc::from($var, $default);
}

