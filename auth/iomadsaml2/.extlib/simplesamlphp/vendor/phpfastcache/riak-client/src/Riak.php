<?php

namespace Basho;

use Basho\Riak\Api;
use Basho\Riak\Api\Http;
use Basho\Riak\Command;
use Basho\Riak\Exception;
use Basho\Riak\Node;

/**
 * This class maintains the list of nodes in the Riak cluster.
 *
 * <code>
 * $nodes = (new Node\Builder)
 *   ->atHost('localhost')
 *   ->onPort(8098)
 *   ->build()
 *
 * $riak = new Riak($nodes);
 *
 * $command = (new Command\Builder\FetchObject($riak))
 *   ->buildLocation('username', 'users')
 *   ->build();
 *
 * $response = $command->execute();
 *
 * $user = $response->getObject();
 * </code>
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Riak
{
    const VERSION = "2.0.3";

    /**
     * Riak server ring
     *
     * @var Node[]
     */
    protected $nodes = [];

    /**
     * Configuration options for this client
     *
     * @var array
     */
    protected $config = [
        'prefix'               => 'riak',
        'mapred_prefix'        => 'mapred',
        'index_prefix'         => 'buckets',
        'dns_server'           => '8.8.8.8',
        'max_connect_attempts' => 3,
    ];

    /**
     * The actively connected Riak Node from the ring
     *
     * @var int
     */
    protected $activeNodeIndex = 0;

    /**
     * API Bridge class to use
     *
     * @var Api|null
     */
    protected $api = NULL;

    /**
     * List of nodes marked inactive
     *
     * @var array
     */
    protected $inactiveNodes = [];

    /**
     * Connection attempts made that failed
     *
     * @var int
     */
    protected $attempts = 0;

    /**
     * Construct a new Client object, defaults to port 8098.
     *
     * @param Node[] $nodes an array of Basho\Riak\Node objects
     * @param array $config a configuration object
     * @param Api $api
     *
     * @throws Exception
     */
    public function __construct(array $nodes, array $config = [], Api $api = NULL)
    {
        // wash any custom keys if any
        $this->nodes = array_values($nodes);
        $this->setActiveNodeIndex($this->pickNode());

        if (!empty($config)) {
            // use php array merge so passed in config overrides defaults
            $this->config = array_merge($this->config, $config);
        }

        if ($api) {
            $this->api = $api;
        } else {
            // default to HTTP bridge class
            $this->api = new Http($this->config);
        }
    }

    /**
     * Pick a random Node from the ring
     *
     * You can pick your friends, you can pick your node, but you can't pick your friend's node.  :)
     *
     * @return int
     * @throws Exception
     */
    protected function pickNode()
    {
        $nodes       = $this->getNodes();
        $index = mt_rand(0, count($nodes) - 1);
        return array_keys($nodes)[$index];
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Get value from connection config
     *
     * @param $key
     *
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->config[$key];
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Execute a Riak command
     *
     * @param Command $command
     * @return Command\Response
     * @throws Exception
     */
    public function execute(Command $command)
    {
        $response = $this->getActiveNode()->execute($command, $this->api);

        // if more than 1 node configured, lets try a different node up to max connection attempts
        if (empty($response) && count($this->nodes) > 1 && $this->attempts < $this->getConfigValue('max_connect_attempts')) {
            $response = $this->pickNewNode()->execute($command);
        } elseif (empty($response) && $this->attempts >= $this->getConfigValue('max_connect_attempts')) {
            throw new Exception('Nodes unreachable. Error Msg: ' . $this->api->getError());
        } elseif ($response == false) {
            throw new Exception('Command failed to execute against Riak. Error Msg: ' . $this->api->getError());
        }

        return $response;
    }

    /**
     * @return Node
     */
    public function getActiveNode()
    {
        $nodes = $this->getNodes();

        return $nodes[$this->getActiveNodeIndex()];
    }

    /**
     * @return int
     */
    public function getActiveNodeIndex()
    {
        return $this->activeNodeIndex;
    }

    /**
     * @param int $activeNodeIndex
     */
    public function setActiveNodeIndex($activeNodeIndex)
    {
        $this->activeNodeIndex = $activeNodeIndex;
    }

    /**
     * @return Api|null
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Accessor for the last request issued to the API. For debugging purposes.
     *
     * @return string
     */
    public function getLastRequest()
    {
        return $this->api->getRequest();
    }

    /**
     * Pick new active node
     *
     * Used when the currently active node fails to complete a command / query
     *
     * @return $this
     * @throws Exception
     */
    public function pickNewNode()
    {
        // mark current active node as inactive and increment attempts
        $this->getActiveNode()->setInactive(true);
        $this->attempts++;
        $this->inactiveNodes[$this->getActiveNodeIndex()] = $this->getActiveNode();

        // move active node to inactive nodes structure to prevent selecting again
        unset($this->nodes[$this->getActiveNodeIndex()]);
        $this->setActiveNodeIndex($this->pickNode());

        return $this;
    }
}
