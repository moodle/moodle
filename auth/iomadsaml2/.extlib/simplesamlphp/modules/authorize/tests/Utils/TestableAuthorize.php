<?php
/**
 * Subclass authorize filter to make it unit testable.
 */

namespace SimpleSAML\Module\Authorize\Tests\Utils;

use SimpleSAML\Module\authorize\Auth\Process\Authorize;

class TestableAuthorize extends Authorize
{
    /**
     * Override the redirect behavior since its difficult to test
     * @param array $request the state
     */
    protected function unauthorized(array &$request)
    {
        $request['NOT_AUTHORIZED'] = true;
    }
}
