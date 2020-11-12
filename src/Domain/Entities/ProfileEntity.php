<?php

namespace ZnTool\Stress\Domain\Entities;

class ProfileEntity
{

    public $synchQueryCount = 1; // кол-во параллельных запросов
    public $ageCount = 1; // кол-во эпох теста
    public $queryCollection;

    public function __construct(int $synchQueryCount = 1, int $ageCount = 1)
    {
        $this->synchQueryCount = $synchQueryCount;
        $this->ageCount = $ageCount;
    }

    public function getSynchQueryCount(): int
    {
        return $this->synchQueryCount;
    }

    public function setSynchQueryCount(int $synchQueryCount): void
    {
        $this->synchQueryCount = $synchQueryCount;
    }

    public function getAgeCount(): int
    {
        return $this->ageCount;
    }

    public function setAgeCount(int $ageCount): void
    {
        $this->ageCount = $ageCount;
    }

    public function getQueryCollection()
    {
        return $this->queryCollection;
    }

    public function getQueryByIndex(int $index)
    {
        return $this->queryCollection[$index];
    }

    public function getQueryCollectionCount(): int
    {
        return count($this->queryCollection);
    }

    public function setQueryCollection($queryCollection): void
    {
        $this->queryCollection = $queryCollection;
    }
}
