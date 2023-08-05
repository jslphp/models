<?php

namespace Jsl\Models;

use DateTime;
use DateTimeZone;
use Jsl\Database\ConnectionInterface;
use Jsl\Database\Query\Builder;
use Jsl\Models\Models;
use Jsl\Models\Schema\Model;


/**
 * Get a model's database connection
 *
 * @param string|object $model
 *
 * @return ?ConnectionInterface
 */
function db(): ?ConnectionInterface
{
    return models()->getConnection();
}


/**
 * Get a base query for a model
 *
 * @param string|object $model
 *
 * @return Builder
 */
function query(string|object $model): Builder
{
    $model = model(className($model));

    $query = db()->table($model->table)->model($model->model);

    if ($prop = $model->softDelete) {
        $query->whereNull($prop->column);
    }

    return $query;
}


/**
 * Get the fully quallified class name
 *
 * @param string|object $classOrObject
 *
 * @return string
 */
function className(string|object $classOrObject): string
{
    return is_object($classOrObject)
        ? $classOrObject::class
        : $classOrObject;
}


/**
 * Get the models instance
 *
 * @return Models
 */
function models(): Models
{
    static $models;
    return $models ??= new Models;
}


/**
 * Get a model
 *
 * @param string|object $model
 *
 * @return Model|null
 */
function model(string|object $model): ?Model
{
    $model = is_object($model) ? $model::class : $model;
    return models()->get($model);
}


/**
 * Get a date as a DateTime object with a specifc timezone
 *
 * @param string|DateTime|null|null $date
 * @param string $tz
 *
 * @return DateTime
 */
function tzDate(string|DateTime|null $date = null, string|DateTimeZone $tz = 'UTC'): DateTime
{
    $date = is_object($date) ? $date : new DateTime($date ?? 'now');
    $date = $date->setTimezone(is_object($tz) ? $tz : new DateTimeZone($tz));

    return $date;
}
