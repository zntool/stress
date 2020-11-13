<?php

namespace ZnTool\Stress\Domain\Repositories\Conf;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Repository\CrudRepositoryInterface;
use ZnCore\Domain\Libs\Query;
use ZnTool\Dev\Runtime\Domain\Helpers\Benchmark;
use ZnTool\Stress\Domain\Entities\ProfileEntity;
use ZnTool\Stress\Domain\Entities\ResultEntity;
use ZnTool\Stress\Domain\Libs\Runtime;
use function GuzzleHttp\Promise\settle;

class ProfileRepository implements CrudRepositoryInterface
{

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
        $profileArray = include __DIR__ . '/../../../Domain/example/scenario.php';
        $profileCollection = EntityHelper::createEntityCollection($this->getEntityClass(), $profileArray);
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
        $profileArray = include __DIR__ . '/../../../Domain/example/scenario.php';
        //$collection = new Collection($profileArray);
        $callback = function ($item) use ($name) {
            return $item['name'] == $name;
        };
        $item = Arr::first($profileArray, $callback);
        if(empty($item)) {
            throw new NotFoundException('Profile not found');
        }
        return EntityHelper::createEntity($this->getEntityClass(), $item);
    }

    public function relations()
    {
        // TODO: Implement relations() method.
    }


}
