<?php

namespace Kevinrob\GuzzleCache\Strategy\Delegate;

use Psr\Http\Message\RequestInterface;

interface RequestMatcherInterface
{

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function matches(RequestInterface $request);
}
