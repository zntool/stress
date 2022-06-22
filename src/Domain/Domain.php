<?php

namespace ZnTool\Stress\Domain;

use ZnCore\Domain\Domain\Interfaces\DomainInterface;

class Domain implements DomainInterface
{

    public function getName()
    {
        return 'dev';
    }

}