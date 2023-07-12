<?php

namespace Basho\Riak\Node;

use Basho\Riak\Node;

/**
 * This class follows the Builder design pattern and is the preferred method for creating Basho\Riak\Node objects for
 * connecting to your Riak node cluster.
 *
 * <code>
 *  // simple local development / testing cluster
 *  use Basho\Riak\Node;
 *
 *  $nodes = (new Node\Builder)
 *      ->buildLocalhost([10018, 10028, 10038, 10048, 10058]);
 * </code>
 *
 * <code>
 *  // password authentication to production cluster
 *  use Basho\Riak\Node;
 *
 *  $nodes = (new Node\Builder)
 *      ->onPort(8098)
 *      ->usingPasswordAuthentication('riakuser', 'riakpassword')
 *      ->withCertificateAuthorityFile(getcwd() . '/path/to/cacert.pem')
 *      ->buildCluster(['riak1.company.int','riak2.company.int','riak3.company.int']);
 * </code>
 *
 * <code>
 *  // certificate authentication to production load balanced cluster
 *  use Basho\Riak\Node;
 *
 *  $node = (new Node\Builder)
 *      ->atHost('riak.company.int')
 *      ->onPort(8098)
 *      ->usingCertificateAuthentication(getcwd() . '/path/to/client.crt')
 *      ->withCertificateAuthorityFile(getcwd() . '/path/to/cacert.pem')
 *      ->build();
 * </code>
 *
 * <code>
 *  // pam authentication to production load balanced cluster
 *  use Basho\Riak\Node;
 *
 *  $node = (new Node\Builder)
 *      ->atHost('riak.company.int')
 *      ->onPort(8098)
 *      ->usingPamAuthentication('riakuser')
 *      ->withCertificateAuthorityFile(getcwd() . '/path/to/cacert.pem')
 *      ->build();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Builder
{
    /**
     * Internal storage
     *
     * @var Config|null
     */
    protected $config = null;

    public function __construct()
    {
        $this->config = new Config();
    }

    /**
     * usingTrustAuthentication
     *
     * Build nodes with trust authentication
     *
     * User authentication and access rules are only available in Riak versions 2 and above. To use this feature, TSL
     * is required to communicate with your Riak nodes.
     *
     * @param string $user
     *
     * @return $this
     */
    public function usingTrustAuthentication($user = '')
    {
        $this->config->setUser($user);
        $this->config->setAuth(true);

        return $this;
    }

    /**
     * usingPasswordAuthentication
     *
     * Build nodes with password authentication
     *
     * User authentication and access rules are only available in Riak versions 2 and above. To use this feature, TSL
     * is required to communicate with your Riak nodes.
     *
     * @param $user
     * @param $pass
     * @return $this
     */
    public function usingPasswordAuthentication($user, $pass = '')
    {
        $this->config->setUser($user);
        $this->config->setPass($pass);
        $this->config->setAuth(true);

        return $this;
    }

    /**
     * usingCertificateAuthentication
     *
     * Build nodes with certificate authentication
     *
     * User authentication and access rules are only available in Riak versions 2 and above. To use this feature, TSL
     * is required to communicate with your Riak nodes.
     *
     * CURRENTLY NOT SUPPORTED OVER THE RIAK HTTP API
     *
     * @param $certificate
     * @param string $password
     *
     * @return $this
     * @throws Builder\Exception
     */
    public function usingCertificateAuthentication($certificate, $password = '')
    {
        $this->config->setCertificate($certificate);
        $this->config->setCertificatePassword($password);
        $this->config->setAuth(true);

        throw new Node\Builder\Exception('Riak over HTTP does not support Certificate Authentication.');

        //return $this;
    }

    /**
     * usingPamAuthentication
     *
     * Build nodes with PAM authentication
     *
     * User authentication and access rules are only available in Riak versions 2 and above. To use this feature, TSL
     * is required to communicate with your Riak nodes.
     *
     * @param $user
     *
     * @return $this
     */
    public function usingPamAuthentication($user)
    {
        $this->config->setUser($user);
        $this->config->setAuth(true);

        return $this;
    }

    /**
     * withCertificateAuthorityDirectory
     *
     * Path to CA file. A Certificate Authority file is required for any secure connections to Riak
     *
     * @param $ca_file
     *
     * @return $this
     *
     */
    public function withCertificateAuthorityFile($ca_file)
    {
        $this->config->setCaFile($ca_file);

        return $this;
    }

    /**
     * withCertificateAuthorityDirectory
     *
     * Directory where the CA file can be found. A Certificate Authority file is required for any secure connections to
     * Riak
     *
     * @param $ca_directory
     *
     * @return $this
     *
     */
    public function withCertificateAuthorityDirectory($ca_directory)
    {
        $this->config->setCaDirectory($ca_directory);

        return $this;
    }

    public function withPrivateKey($private_key, $password = '')
    {
        $this->config->setPrivateKey($private_key);
        $this->config->setPrivateKeyPassword($password);

        return $this;
    }

    /**
     * Client side connection timeout for requests
     *
     * @param $timeout
     */
    public function withConnectionTimeout($timeout) {
        $this->config->setConnectionTimeout($timeout);
    }

    /**
     * Client side socket read/write timeout for requests
     *
     * @param $timeout
     */
    public function withStreamTimeout($timeout) {
        $this->config->setStreamTimeout($timeout);
    }

    /**
     * @return Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Build distributed cluster
     *
     * Build node objects configured to listen on the same port but different hosts. Commonly used in
     * staging and production environments where you have multiple Riak nodes on multiple machines / vms.
     *
     * @param array $hosts
     * @return Node[]
     */
    public function buildCluster(array $hosts = ['localhost'])
    {
        $nodes = [];
        foreach ($hosts as $host) {
            $nodes[] = $this->atHost($host)->build();
        }

        return $nodes;
    }

    /**
     * Build node
     *
     * Validate configuration for a single node object, then build it
     *
     * @return Node
     */
    public function build()
    {
        $this->validate();

        return new Node(clone $this->config);
    }

    /**
     * Builder configuration validation
     *
     * Checks the current configuration of the Node Builder for errors. This method should be executed before each Node
     * is built.
     *
     * @throws Builder\Exception
     */
    protected function validate()
    {
        // verify we have a host address and port
        if (!$this->config->getHost() || !$this->config->getPort()) {
            throw new Node\Builder\Exception('Node host address and port number are required.');
        }

        if ($this->config->getUser() && $this->config->getCertificate()) {
            throw new Node\Builder\Exception('Connect with password OR certificate authentication, not both.');
        }

        if ($this->config->isAuth() && !$this->config->getCaDirectory() && !$this->config->getCaFile()) {
            throw new Node\Builder\Exception('Certificate authority file is required for authentication.');
        }
    }

    /**
     * Build with host address
     *
     * Build node objects with configuration to use a specific host address
     *
     * @param $host
     * @return $this
     */
    public function atHost($host)
    {
        $this->config->setHost($host);

        return $this;
    }

    /**
     * Build local node cluster
     *
     * Build multiple node objects configured with the same host address but different ports. Commonly used in
     * development environments where you have multiple Riak nodes on a single machine / vm.
     *
     * @param array $ports
     * @return Node[]
     */
    public function buildLocalhost(array $ports = [8087])
    {
        $nodes = [];
        $this->atHost('localhost');
        foreach ($ports as $port) {
            $nodes[] = $this->onPort($port)->build();
        }

        return $nodes;
    }

    /**
     * Build node objects with configuration to use a specific port number
     *
     * @param $port
     * @return $this
     */
    public function onPort($port)
    {
        $this->config->setPort($port);

        return $this;
    }
}
