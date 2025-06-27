<?php

use Ru\Progerplace\Chain\Cf;
use Ru\Progerplace\Chain\Ch;
use Ru\Progerplace\Chain\F;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/DocBuilder.php';

$dirBuild = realpath(__DIR__ . '/../.build');

$ch = Ch::from([]);
$cf = Cf::from([]);

$sections = [
    'Append'    => [
        [$ch->append(...), $cf->append(...), F::append(...)],
        'Append merge'             => [$ch->append->merge(...), $cf->append->merge(...), F::appendMerge(...)],
        'Append merge from json'   => [$ch->append->mergeFromJson(...), $cf->append->mergeFromJson(...), F::appendMergeFromJson(...)],
        'Append merge from string' => [$ch->append->mergeFromString(...), $cf->append->mergeFromString(...), F::appendMergeFromString(...)],
    ],
    'Chunk'     => [
        'Chunk by count' => [$ch->chunk->byCount(...), $cf->chunk->byCount(...), F::chunkByCount(...)],
        'Chunk by size'  => [$ch->chunk->bySize(...), $cf->chunk->bySize(...), F::chunkBySize(...)],
    ],
    'Clear'     => [
        [$ch->clear(...), $cf->clear(...)],
    ],
    'Count'     => [
        [$ch->count(...), $cf->count(...), F::count(...)],
    ],
    'Each'      => [
        [$ch->each(...), $cf->each(...), F::each(...)],
    ],
    'Filter'    => [
        [$ch->filter(...), $cf->filter(...), F::filter(...)],
        'Filter keys'   => [$ch->filter->keys(...), $cf->filter->keys(...), F::filterKeys(...)],
        'Filter values' => [$ch->filter->values(...), $cf->filter->values(...), F::filterValues(...)],
    ],
    'Find'      => [
        [$ch->find(...), $cf->find(...), F::find(...)],
    ],
    'Flip'      => [
        [$ch->flip(...), $cf->flip(...), F::flip(...)],
    ],
    'Flatten'   => [
        [$ch->flatten(...), $cf->flatten(...), F::flatten(...)],
        'Flatten all' => [$ch->flatten->all(...), $cf->flatten->all(...), F::flattenAll(...)],
    ],
    'Get'       => [
        [$ch->get(...), $cf->get(...), F::get(...)],
        'Get by number'              => [$ch->get->byNumber(...), $cf->get->byNumber(...), F::getByNumber(...)],
        'Get by number or else'      => [$ch->get->byNumberOrElse(...), $cf->get->byNumberOrElse(...), F::getByNumberOrElse(...)],
        'Get by number or exception' => [$ch->get->byNumberOrException(...), $cf->get->byNumberOrException(...), F::getByNumberOrException(...)],
        'Get first'                  => [$ch->get->first(...), $cf->get->first(...), F::getFirst(...)],
        'Get first or else'          => [$ch->get->firstOrElse(...), $cf->get->firstOrElse(...), F::getFirstOrElse(...)],
        'Get first or exception'     => [$ch->get->firstOrException(...), $cf->get->firstOrException(...), F::getFirstOrException(...)],
        'Get last'                   => [$ch->get->last(...), $cf->get->last(...), F::getLast(...)],
        'Get last or else'           => [$ch->get->lastOrElse(...), $cf->get->lastOrElse(...), F::getLastOrElse(...)],
        'Get last or exception'      => [$ch->get->lastOrException(...), $cf->get->lastOrException(...), F::getLastOrException(...)],
        'Get or else'                => [$ch->get->orElse(...), $cf->get->orElse(...), F::getOrElse(...)],
        'Get or exception'           => [$ch->get->orException(...), $cf->get->orException(...), F::getOrException(...)],
    ],
    'Is'        => [
        'Is empty'           => [$ch->is->empty(...), $cf->is->empty(...), F::isEmpty(...)],
        'Is every'           => [$ch->is->every(...), $cf->is->every(...), F::isEvery(...)],
        'Is field has value' => [$ch->is->fieldHasValue(...), $cf->is->fieldHasValue(...), F::isFieldHasValue(...)],
        'Is has key'         => [$ch->is->hasKey(...), $cf->is->hasKey(...), F::isHasKey(...)],
        'Is has value'       => [$ch->is->hasValue(...), $cf->is->hasValue(...), F::isHasValue(...)],
        'Is list'            => [$ch->is->list(...), $cf->is->list(...), F::isList(...)],
        'Is none'            => [$ch->is->none(...), $cf->is->none(...), F::isNone(...)],
        'Is not empty'       => [$ch->is->notEmpty(...), $cf->is->notEmpty(...), F::isNotEmpty(...)],
        'Is any'             => [$ch->is->any(...), $cf->is->any(...), F::isAny(...)],
    ],
    'Json'      => [
        'Json decode by'     => [$ch->json->decodeBy(...), $cf->json->decodeBy(...), F::jsonDecodeBy(...)],
        'Json decode fields' => [$ch->json->decodeFields(...), $cf->json->decodeFields(...), F::jsonDecodeFields(...)],
        'Json encode by'     => [$ch->json->encodeBy(...), $cf->json->encodeBy(...), F::jsonEncodeBy(...)],
        'Json encode fields' => [$ch->json->encodeFields(...), $cf->json->encodeFields(...), F::jsonEncodeFields(...)],
    ],
    'Keys'      => [
        [$ch->keys(...), $cf->keys(...), F::keys(...)],
        'Keys from field' => [$ch->keys->fromField(...), $cf->keys->fromField(...), F::keysFromField(...)],
        'Keys get'        => [$ch->keys->get(...), $cf->keys->get(...), F::keysGet(...)],
        'Keys get first'  => [$ch->keys->getFirst(...), $cf->keys->getFirst(...), F::keysGetFirst(...)],
        'Keys get last'   => [$ch->keys->getLast(...), $cf->keys->getLast(...), F::keysGetLast(...)],
        'Keys map'        => [$ch->keys->map(...), $cf->keys->map(...), F::keysMap(...)],
    ],
    'Keys case' => [
        'To camel case'        => [$ch->keys->case->toCamel(...), $cf->keys->case->toCamel(...), F::keysCaseToCamel(...)],
        'To kebab case'        => [$ch->keys->case->toKebab(...), $cf->keys->case->toKebab(...), F::keysCaseToKebab(...)],
        'To paskal case'       => [$ch->keys->case->toPaskal(...), $cf->keys->case->toPaskal(...), F::keysCaseToPaskal(...)],
        'To scream kebab case' => [$ch->keys->case->toScreamKebab(...), $cf->keys->case->toScreamKebab(...), F::keysCaseToScreamKebab(...)],
        'To scream snake case' => [$ch->keys->case->toScreamSnake(...), $cf->keys->case->toScreamSnake(...), F::keysCaseToScreamSnake(...)],
        'To snake case'        => [$ch->keys->case->toSnake(...), $cf->keys->case->toSnake(...), F::keysCaseToSnake(...)],
    ],
    'Map'       => [
        [$ch->map(...), $cf->map(...), F::map(...)],
    ],
    'Outer'     => [
        'Outer check'        => [$ch->outer->check(...)],
        'Outer change'       => [$ch->outer->change(...)],
        'Outer is'           => [$ch->outer->is(...)],
        'Outer print'        => [$ch->outer->print(...)],
        'Outer replace with' => [$ch->outer->replaceWith(...)],
    ],
    'Pad'       => [
        [$ch->pad(...), $cf->pad(...), F::pad(...)],
    ],
    'Pop'       => [
        [$ch->pop(...), $cf->pop(...), F::pop(...)],
    ],
    'Prepend'   => [
        [$ch->prepend(...), $cf->prepend(...), F::prepend(...)],
        'Prepend merge'             => [$ch->prepend->merge(...), $cf->prepend->merge(...), F::prependMerge(...)],
        'Prepend merge from json'   => [$ch->prepend->mergeFromJson(...), $cf->prepend->mergeFromJson(...), F::prependMergeFromJson(...)],
        'Prepend merge from string' => [$ch->prepend->mergeFromString(...), $cf->prepend->mergeFromString(...), F::prependMergeFromString(...)],
    ],
    'Reject'    => [
        [$ch->reject(...), $cf->reject(...), F::reject(...)],
        'Reject empty'  => [$ch->reject->empty(...), $cf->reject->empty(...), F::rejectEmpty(...)],
        'Reject keys'   => [$ch->reject->keys(...), $cf->reject->keys(...), F::rejectKeys(...)],
        'Reject null'   => [$ch->reject->null(...), $cf->reject->null(...), F::rejectNull(...)],
        'Reject values' => [$ch->reject->values(...), $cf->reject->values(...), F::rejectValues(...)],
    ],
    'Reverse'   => [
        [$ch->reverse(...), $cf->reverse(...), F::reverse(...)],
    ],
    'Replace'   => [
        [$ch->replace(...), $cf->replace(...), F::replace(...)],
        'Replace recursive' => [$ch->replace->recursive(...), $cf->replace->recursive(...), F::replaceRecursive(...)],
    ],
    'Shift'     => [
        [$ch->shift(...), $cf->shift(...), F::shift(...)],
    ],
    'Slice'     => [
        [$ch->slice(...), $cf->slice(...), F::slice(...)],
        'Slice head' => [$ch->slice->head(...), $cf->slice->head(...), F::sliceHead(...)],
        'Slice tail' => [$ch->slice->tail(...), $cf->slice->tail(...), F::sliceTail(...)],
    ],
    'Splice'    => [
        [$ch->splice(...), $cf->splice(...), F::splice(...)],
        'Splice head' => [$ch->splice->head(...), $cf->splice->head(...), F::spliceHead(...)],
        'Splice tail' => [$ch->splice->tail(...), $cf->splice->tail(...), F::spliceTail(...)],
    ],
    'Sort'      => [
        [$ch->sort(...), $cf->sort(...), F::sort(...)],
    ],
    'Unique'    => [
        [$ch->unique(...), $cf->unique(...), F::unique(...)],
        'Unique by' => [$ch->unique->by(...), $cf->unique->by(...), F::uniqueBy(...)],
    ],
    'Values'    => [
        [$ch->values(...), $cf->values(...), F::values(...)],
    ],
];


(new DocBuilder($sections, $dirBuild))->execute();
