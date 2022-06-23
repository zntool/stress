<?php

namespace ZnTool\Stress\Domain\Repositories\Conf;

use Illuminate\Support\Arr;
use Illuminate\Support\Enumerable;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Domain\Entity\Helpers\CollectionHelper;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;
use ZnTool\Stress\Domain\Entities\ProfileEntity;

class ProfileRepository implements CrudRepositoryInterface
{

    private $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getEntityClass(): string
    {
        return ProfileEntity::class;
    }

    public function create(EntityIdInterface $entity)
    {
        // TODO: Implement create() method.
    }

    public function update(EntityIdInterface $entity)
    {
        // TODO: Implement update() method.
    }

    public function deleteById($id)
    {
        // TODO: Implement deleteById() method.
    }

    public function deleteByCondition(array $condition)
    {
        // TODO: Implement deleteByCondition() method.
    }

    public function all(Query $query = null): Enumerable
    {
        $profileCollection = CollectionHelper::create($this->getEntityClass(), $this->config);
        return $profileCollection;
    }

    public function count(Query $query = null): int
    {
        // TODO: Implement count() method.
    }

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        // TODO: Implement oneById() method.
    }

    public function oneByName(string $name, Query $query = null): ProfileEntity
    {
        $callback = function ($item) use ($name) {
            return $item['name'] == $name;
        };
        $item = Arr::first($this->config, $callback);
        if (empty($item)) {
            throw new NotFoundException('Profile not found');
        }
        return EntityHelper::createEntity($this->getEntityClass(), $item);
    }

    /*public function _relations()
    {
        // TODO: Implement relations() method.
    }*/

}
