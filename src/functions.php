<?php

namespace Jsl\Models;

use DateTime;
use DateTimeZone;
use Jsl\Database\ConnectionInterface;


/**
 * Get a model's database connection
 *
 * @param string|object $model
 *
 * @return ConnectionInterface
 */
function connection(string|object $model): ConnectionInterface
{
    $model = is_string($model) ? $model : $model::class;
    return Models::getConnection($model);
}


/**
 * Get a models public class or object properties
 *
 * @param string|object $classOrObject
 *
 * @return array
 */
function getPublicProperties(string|object $classOrObject): array
{
    return is_object($classOrObject)
        ? get_object_vars($classOrObject)
        : get_class_vars($classOrObject);
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
