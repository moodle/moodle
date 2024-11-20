<?php

namespace Basho\Tests;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Functional tests verifying TSL features
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SecurityFeaturesTest extends TestCase
{
    public function testUnauthorized()
    {
        $nodes = [
            (new Riak\Node\Builder())
                ->atHost(static::getTestHost())
                ->onPort(static::getTestSecurePort())
                ->usingPasswordAuthentication('unauthorizeduser', 'hispassword')
                ->withCertificateAuthorityFile(getcwd() . '/tools/test-ca/certs/cacert.pem')
                ->build()
        ];

        $riak = new Riak($nodes);

        // build an object
        $command = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_data')
            ->buildBucket('users')
            ->build();

        $response = $command->execute();

        // expects 401 - Unauthorized
        $this->assertEquals('401', $response->getCode());
    }

    public function testPasswordAuth()
    {
        $nodes = [
            (new Riak\Node\Builder())
                ->atHost(static::getTestHost())
                ->onPort(static::getTestSecurePort())
                ->usingPasswordAuthentication('riakpass', 'Test1234')
                ->withCertificateAuthorityFile(getcwd() . '/tools/test-ca/certs/cacert.pem')
                ->build()
        ];

        $riak = new Riak($nodes);

        // build an object
        $command = (new Command\Builder\StoreObject($riak))
            ->buildObject('some_data')
            ->buildBucket('users')
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getCode(), $response->getMessage());
        $this->assertNotEmpty($response->getLocation());
        $this->assertInstanceOf('\Basho\Riak\Location', $response->getLocation());
    }
}
