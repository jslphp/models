<?php

namespace Jsl\Models;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Jsl\Database\Collections\Paginate;
use Jsl\Database\Query\Builder;
use JsonSerializable;

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
     * Get the model's table name
     *
     * @return string
     */
    abstract public static function table(): string;


    /**
     * The default ORDER BY for queries
     *
     * @return array
     */
    public static function defaultOrderBy(): array
    {
        return [];
    }


    /**
     * Get the primary key
     *
     * @return string
     */
    public static function primaryKey(): string
    {
        return 'id';
    }


    /**
     * Get a query builder
     *
     * @return Builder
     */
    public static function query(): Builder
    {
        $query = connection(static::class)
            ->table(static::table())
            ->model(static::class);

        foreach (static::defaultOrderBy() as $column => $diraction) {
            $query->orderBy($column, $diraction);
        }

        $props = getPublicProperties(static::class);

        if (key_exists('deletedAt', $props)) {
            $query->whereNull('deletedAt');
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
        return static::query()->find(id: $identifier, column: $column);
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
        $remove = [static::primaryKey(), 'createdAt', 'updatedAt', 'deletedAt'];

        foreach ($remove as $key) {
            if (key_exists($key, $data)) {
                unset($data[$key]);
            }
        }

        $model = new static($data);

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
        $props = getPublicProperties(static::class);

        foreach ($data as $key => $value) {
            if (key_exists($key, $props) && $this->primaryKey() !== $key) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }


    /**
     * Save the current model
     *
     * @return bool
     */
    public function save(): bool
    {
        $primaryKey = $this->primaryKey();
        $primaryValue = $primaryKey ? $this->{$primaryKey} : null;
        $data = getPublicProperties($this);

        if (key_exists($primaryKey, $data)) {
            unset($data[$primaryKey]);
        }

        if (key_exists('deletedAt', $data)) {
            unset($data['deletedAt']);
        }

        $dates = ['createdAt', 'updatedAt'];
        foreach ($dates as $key) {
            if (property_exists($this, $key) && $this->{$key} === null) {
                $data[$key] = tzDate();
            }
        }

        if ($primaryValue === null) {
            $id = static::query()->insertGetId($data);

            if ($id) {
                $this->replaceAll(static::find($id, $primaryKey)->toArray());
            }

            return $this->{$primaryKey} !== null;
        }


        $stmt = static::query()
            ->where($primaryKey, $primaryValue)
            ->update($data);

        return $stmt->rowCount() > 0;
    }


    /**
     * Delete the record
     *
     * @return bool
     */
    public function delete(): bool
    {
        $primaryKey = $this->primaryKey();
        $primaryValue = $primaryKey ? $this->{$primaryKey} : null;

        if ($primaryValue === null) {
            return true;
        }

        $query = static::query()->where($primaryKey, $primaryValue)
            ->limit(1);

        property_exists($this, 'deletedAt')
            ? $query->update(['deletedAt' => tzDate()])
            : $query->delete();


        if (static::find($primaryValue, $primaryKey)) {
            return false;
        }

        $this->replaceAll(getPublicProperties(static::class));

        return true;
    }


    /**
     * Get the model as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return getPublicProperties($this);
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
        $primaryKey = $this->primaryKey();

        if (key_exists($primaryKey, $data)) {
            $this->{$primaryKey} = $data[$primaryKey];
        }

        $dates = ['createdAt', 'updatedAt'];

        foreach ($dates as $key) {
            if (key_exists($key, $data) === false) {
                continue;
            }

            if (property_exists($this, $key) && $data[$key]) {
                $this->{$key} = new DateTime($data[$key], new DateTimeZone('UTC'));
            }

            unset($data[$key]);
        }

        $this->replace($data);

        return $this;
    }
}
