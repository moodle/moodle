<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use SimpleSAML\Auth\SourceFactory;

class TestAuthSourceFactory implements SourceFactory
{
    /**
     * @return \SimpleSAML\Test\Utils\TestAuthSource
     */
    public function create(array $info, array $config)
    {
        return new TestAuthSource($info, $config);
    }
}
