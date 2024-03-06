<?php

namespace Packback\Lti1p3\Concerns;

use Packback\Lti1p3\Helpers\Helpers;

trait Arrayable
{
    abstract public function getArray(): array;

    public function toArray(): array
    {
        return Helpers::filterOutNulls($this->getArray());
    }
}
