<?php

namespace Basho\Riak;

use Basho\Riak\Node\Config;
use Basho\Riak\Node\Response;

/**
 * Contains the connection configuration to connect to a Riak node.
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Node
{
    /**
     * Configuration
     *
     * Contains configuration needed to connect to a Riak node.
     *
     * @var Config|null
     */
    protected $config = null;

    /**
     * Inactive node
     *
     * This is only set to true if the node has been marked as unreachable.
     *
     * @var bool
     */
    protected $inactive = false;

    /**
     * Node signature
     *
     * This property is used to store a stateless unique identifier for this node.
     *
     * @var string
     */
    protected $signature = '';

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->setSignature();
    }

    /**
     * This should NEVER be invoked outside of this object.
     */
    private function setSignature()
    {
        $this->signature = md5(json_encode($this->config));
    }

    /**
     * @return boolean
     */
    public function isInactive()
    {
        return $this->inactive;
    }

    /**
     * @param boolean $inactive
     */
    public function setInactive($inactive)
    {
        $this->inactive = $inactive;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->config->getHost();
    }

    /**
     * @return Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->config->getPort();
    }

    /**
     * Returns host:port for Node
     *
     * @return string
     */
    public function getUri()
    {
        return sprintf('%s:%s', $this->config->getHost(), $this->config->getPort());
    }

    /**
     * useTls
     *
     * @return bool
     */
    public function useTls()
    {
        return $this->config->isAuth();
    }

    /**
     * getUserName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->config->getUser();
    }

    /**
     * getPassword
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->config->getPass();
    }

    public function getCaFile()
    {
        return $this->config->getCaFile();
    }

    public function getCaDirectory()
    {
        return $this->config->getCaDirectory();
    }

    /**
     * getCertificate
     *
     * @return string
     */
    public function getCertificate()
    {
        return $this->config->getCertificate();
    }

    public function getCertificatePassword()
    {
        return $this->config->getCertificatePassword();
    }

    /**
     * getPrivateKey
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->config->getPrivateKey();
    }

    /**
     * getPrivateKeyPassword
     *
     * @return string
     */
    public function getPrivateKeyPassword()
    {
        return $this->config->getPrivateKeyPassword();
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->config->getConnectionTimeout();
    }

    /**
     * @return int
     */
    public function getStreamTimeout()
    {
        // NB: value represents seconds
        return $this->config->getStreamTimeout();
    }

    /**
     * @param Command $command
     * @param Api $api
     *
     * @return Command\Response
     * @throws Exception
     */
    public function execute(Command $command, Api $api)
    {
        $success = $api->prepare($command, $this)->send();
        if ($success === FALSE) {
            return false;
        }

        return $api->getResponse();
    }
}
