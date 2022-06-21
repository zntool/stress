<?php

namespace ZnTool\Stress\Domain\Repositories\Conf;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Libs\Entity\Helpers\CollectionHelper;
use ZnCore\Base\Libs\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Base\Libs\Entity\Helpers\EntityHelper;
use ZnCore\Base\Libs\Entity\Interfaces\EntityIdInterface;
use ZnCore\Base\Libs\Repository\Interfaces\CrudRepositoryInterface;
use ZnCore\Base\Libs\Query\Entities\Query;
use ZnTool\Dev\Runtime\Domain\Helpers\Benchmark;
use ZnTool\Stress\Domain\Entities\ProfileEntity;
use ZnTool\Stress\Domain\Entities\ResultEntity;
use ZnTool\Stress\Domain\Libs\Runtime;
use function GuzzleHttp\Promise\settle;

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

    public function all(Query $query = null)
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
        if(empty($item)) {
            throw new NotFoundException('Profile not found');
        }
        return EntityHelper::createEntity($this->getEntityClass(), $item);
    }

    /*public function _relations()
    {
        // TODO: Implement relations() method.
    }*/

}
