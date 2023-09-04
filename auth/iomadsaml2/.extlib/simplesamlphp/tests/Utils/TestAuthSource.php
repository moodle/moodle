<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use SimpleSAML\Auth\Source;

class TestAuthSource extends Source
{
    /**
     * @return void
     */
    public function authenticate(&$state)
    {
    }
}
