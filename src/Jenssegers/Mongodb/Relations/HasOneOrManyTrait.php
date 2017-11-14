<?php
/**
 * Created by IntelliJ IDEA.
 * User: kars
 * Date: 2017-11-15
 * Time: 03:44
 */

namespace Jenssegers\Mongodb\Relations;


use MongoDB\BSON\ObjectID;

/**
 * Trait HasOneOrManyTrait
 * @package Jenssegers\Mongodb\Relations
 *
 * @property \Illuminate\Database\Eloquent\Builder $query
 * @property string $localKey
 */
trait HasOneOrManyTrait
{
    /**
     * 릴레이션을 가져올 땐 꼭 ObjectID 를 씌워서 가져옴
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where($this->foreignKey, '=', new ObjectID($this->getParentKey()));

            $this->query->whereNotNull($this->foreignKey);
        }
    }

    /**
     * @inheritdoc
     */
    public function addEagerConstraints(array $models)
    {
        // We'll grab the primary key name of the related models since it could be set to
        // a non-standard name and not "id". We will then construct the constraint for
        // our eagerly loading query so it returns the proper models from execution.
        $key = $this->foreignKey;

        $this->query->whereIn($key, $this->getEagerModelKeys($models));
    }

    /**
     * Gather the keys from an array of related models.
     *
     * @param  array  $models
     * @return array
     */
    protected function getEagerModelKeys(array $models)
    {
        $keys = [];

        // First we need to gather all of the keys from the parent models so we know what
        // to query for via the eager loading query. We will add them to an array then
        // execute a "where in" statement to gather up all of those related records.
        foreach ($models as $model) {
            if (! is_null($value = $model->{$this->localKey})) {
                $keys[] = new ObjectID($value);
            }
        }

        // If there are no keys that were not null we will just return an array with null
        // so this query wont fail plus returns zero results, which should be what the
        // developer expects to happen in this situation. Otherwise we'll sort them.
        if (count($keys) === 0) {
            return [null];
        }

        sort($keys);

        return array_values(array_unique($keys));
    }
}