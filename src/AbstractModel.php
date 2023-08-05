<?php

namespace Jsl\Models;

use DateTimeInterface;
use Exception;
use Jsl\Database\Collections\Paginate;
use Jsl\Database\Query\Builder;
use JsonSerializable;

use function Jsl\Models\Components\db;
use function Jsl\Models\Components\model;
use function Jsl\Models\Components\tzDate;

abstract class AbstractModel implements JsonSerializable
{
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->replaceAll($data);
    }


    /**
     * Get a query builder
     *
     * @return Builder
     */
    public static function query(): Builder
    {
        $model = model(static::class);

        $query = db()->table($model->table)->model($model->model);

        if ($prop = $model->softDelete) {
            $query->whereNull($prop->column);
        }

        return $query;
    }


    /**
     * Find a record
     *
     * @param string|int $identifier
     * @param string|null $column If null, the default primary key will be used
     *
     * @return static|null
     */
    public static function find(string|int $identifier, ?string $column = null): ?static
    {
        $model = model(static::class);

        if ($model->primaryKey === null) {
            throw new Exception("No column set and no primary key defined for the model");
        }

        return static::query()->find(
            id: $identifier,
            column: $column ?? $model->primaryKey->column
        );
    }


    /**
     * Get records using pagination
     *
     * @param int $page
     * @param int $perPage
     *
     * @return array
     */
    public static function paginate(int $page = 1, int $perPage = 25): Paginate
    {
        return static::query()->paginate($page, $perPage);
    }


    /**
     * Get all records
     *
     * @return array
     */
    public static function get(): array
    {
        return static::query()->get();
    }


    /**
     * Create a new record
     *
     * @param array $data
     *
     * @return ?AbstractModel
     */
    public static function create(array $data): ?AbstractModel
    {
        $model = model(static::class);
        $parsedData = [];

        foreach ($data as $key => $value) {
            $property = $model->property($key);

            if ($property?->isSystemProperty() === false) {
                $parsedData[$property->column] = $value;
            }
        }

        $model = new static($parsedData);

        return $model->save() ? $model : null;
    }


    /**
     * Replace the model data
     *
     * @param array $data
     *
     * @return self
     */
    public function replace(array $data): self
    {
        $model = model($this);

        foreach ($data as $key => $value) {
            $property = $model->property($key);

            if ($property?->isSystemProperty() === false) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }


    /**
     * Save the current model
     *
     * @return bool It will return false on updates if no data was changed (and no updatedAt field is defined)
     */
    public function save(): bool
    {
        $model = model($this);
        $key = $model->primaryKey;
        $data = [];
        $isNew = $key === null || empty($this->{$key->name});

        // If primary key is missing or empty, do an insert
        if ($isNew === true) {
            foreach ($model->properties as $property) {
                if ($property->isSystemProperty() === false) {
                    $data[$property->column] = $this->{$property->name};
                }
            }

            if ($model->createdAt) {
                $data[$model->createdAt->column] = tzDate();
            }

            if ($model->updatedAt) {
                $data[$model->updatedAt->column] = tzDate();
            }

            $stmt = static::query()->insert($data);

            if ($stmt->rowCount() === 0) {
                return false;
            }

            // Update the current model with any auto generated data from the database
            if ($key && $id = db()->lastInsertId()) {
                $this->replaceAll(static::find($id, $key->column)->toArray());
            } else {
                $this->replaceAll($data);
            }

            return true;
        }

        // We have a primary key defined and set, so let's do an update
        if ($isNew === false) {
            foreach ($model->properties as $property) {
                if ($property->isSystemProperty() === false) {
                    $data[$property->column] = $this->{$property->name};
                }
            }

            if ($model->updatedAt) {
                $data[$model->updatedAt->column] = $this->{$model->updatedAt->name} = tzDate();
            }

            $stmt = static::query()->where($key->column, $this->{$key->name})->update($data);

            return $stmt->rowCount() > 0;
        }
    }


    /**
     * Delete the record
     *
     * @return bool
     */
    public function delete(): bool
    {
        $model = model($this);
        $key = $model->primaryKey;

        if ($key === null || empty($this->{$key->name})) {
            throw new Exception("Only models with defined and set primary keys can be deleted");
        }

        $query = static::query()->where($key->column, $this->{$key->name})
            ->limit(1);

        $model->softDelete
            ? $query->update(['deletedAt' => tzDate()])
            : $query->delete();

        if (static::find($this->{$key->name}, $key->column)) {
            return false;
        }

        return true;
    }


    /**
     * Get the model as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $model = model($this);

        $data = [];
        foreach ($model->properties as $property => $_) {
            $data[$property] = $this->{$property};
        }

        return $data;
    }


    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        $data = $this->toArray();

        foreach ($data as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $data[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        return $data;
    }


    /**
     * Replace all, including readonly
     *
     * @param array $data
     *
     * @return self
     */
    protected function replaceAll(array $data): self
    {
        $model = model($this);
        $key = $model->primaryKey;

        if ($key && key_exists($key->name, $data)) {
            $this->{$key->name} = $data[$key->name];
        }

        $newData = [];
        foreach ($data as $prop => $value) {
            $property = $model->property($prop);
            $property = $property ?: $model->column($prop);

            if ($property === null) {
                continue;
            }

            if ($property->isDate() && $value) {
                $this->{$property->name} = tzDate($value, 'UTC');
                continue;
            }

            $newData[$property->name] = $value;
        }

        $this->replace($newData);

        return $this;
    }
}
