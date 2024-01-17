<?php

namespace Packback\Lti1p3\Concerns;

trait JsonStringable
{
    use Arrayable;

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}
