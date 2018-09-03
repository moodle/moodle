<?php

declare(strict_types=1);

namespace Phpml\Preprocessing\Imputer;

interface Strategy
{
    /**
     * @param array $currentAxis
     *
     * @return mixed
     */
    public function replaceValue(array $currentAxis);
}
