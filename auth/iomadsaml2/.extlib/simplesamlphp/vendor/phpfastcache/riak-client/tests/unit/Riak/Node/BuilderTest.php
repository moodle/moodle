<?php

namespace Basho\Tests\Riak\Node;

use Basho\Riak;
use Basho\Riak\Node\Builder;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak nodes via the Node Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class BuilderTest extends TestCase
{
    /**
     * testConstruct
     *
     * Test node builder construct
     */
    public function testConstruct()
    {
        $builder = new Builder();

        $this->assertInstanceOf('Basho\Riak\Node\Builder', $builder);
    }

    /**
     * testWithHost
     */
    public function testWithHost()
    {
        $builder = (new Builder)
            ->atHost('localhost');

        $this->assertEquals($builder->getConfig()->getHost(), 'localhost');
    }

    /**
     * testWithPort
     */
    public function testWithPort()
    {
        $builder = (new Builder)
            ->onPort(10018);

        $this->assertEquals($builder->getConfig()->getPort(), 10018);
    }

    /**
     * testBuildLocalhost
     *
     * Test the localhost node builder
     */
    public function testBuildLocalhost()
    {
        $nodes = (new Builder)
            ->buildLocalhost([10018, 10028, 10038, 10048, 10058]);

        $this->assertTrue(count($nodes) == 5);
        $this->assertTrue($nodes[0]->getHost() == 'localhost');
        $this->assertTrue($nodes[0]->getPort() == 10018);
    }

    /**
     * testBuildCluster
     *
     * Test the cluster node builder
     */
    public function testBuildCluster()
    {
        $nodes = (new Builder)
            ->onPort(10018)
            ->buildCluster(['riak1.company.com', 'riak2.company.com', 'riak3.company.com',]);

        $this->assertTrue(count($nodes) == 3);
        $this->assertTrue($nodes[1]->getHost() == 'riak2.company.com');
        $this->assertTrue($nodes[0]->getPort() == 10018);
        $this->assertTrue($nodes[1]->getPort() == 10018);
    }

    public function testUsingAuth()
    {
        $node = (new Builder())
            ->atHost(static::getTestHost())
            ->onPort(static::getTestSecurePort())
            ->usingPasswordAuthentication('unauthorizeduser', 'hispassword')
            ->withCertificateAuthorityFile(getcwd() . '/tools/test-ca/certs/cacert.pem')
            ->build();

        $riak = new Riak([$node]);

        $this->assertEquals('unauthorizeduser', $node->getUserName());
        $this->assertEquals('hispassword', $node->getPassword());
        $this->assertEquals(getcwd() . '/tools/test-ca/certs/cacert.pem', $node->getCaFile());
        $this->assertInstanceOf('Basho\Riak', $riak);
    }
}
