<?php

namespace ZnTool\Stress\Domain\Helpers;

use Illuminate\Support\Collection;
use ZnTool\Stress\Domain\Entities\ResultEntity;
use ZnTool\Stress\Domain\Libs\Runtime;

class RuntimeHelper
{

    public static function sunOfTree(Collection $all)
    {
        $localRuntime = 0;
        foreach ($all as $runtimeCollection) {
            $localRuntime += self::sumOfCollection($runtimeCollection);
        }
        return $localRuntime;
    }

    public static function sumOfCollection($runtimeCollection) {
        /** @var Runtime[] $runtimeCollection */
        $localRuntime = 0;
        foreach ($runtimeCollection as $rt) {
            $localRuntime = $localRuntime + $rt->getResult();
        }
        return $localRuntime;
    }
}
