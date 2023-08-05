<?php

namespace Jsl\Models\Components;

use DateTime;
use DateTimeZone;
use Jsl\Database\ConnectionInterface;
use Jsl\Models\Models;
use Jsl\Models\Schema\Model;


/**
 * Get a model's database connection
 *
 * @param string|object $model
 *
 * @return ConnectionInterface
 */
function db(): ConnectionInterface
{
    return models()->getConnection();
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
