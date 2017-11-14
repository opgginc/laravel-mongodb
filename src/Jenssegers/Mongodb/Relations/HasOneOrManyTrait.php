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
}