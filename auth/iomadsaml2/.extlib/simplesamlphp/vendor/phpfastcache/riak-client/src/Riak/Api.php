<?php

namespace Basho\Riak;

/**
 * Extend this class to implement your own API bridge.
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
abstract class Api
{
    /**
     * Request string to be sent
     *
     * @var string
     */
    protected $request = '';

    /**
     * @var Command\Response|null
     */
    protected $response = null;

    /**
     * @var Command|null
     */
    protected $command = null;

    /**
     * @var Node|null
     */
    protected $node = null;

    protected $success = null;

    protected $error = '';

    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return Command|null
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param Command|null $command
     *
     * @return $this
     */
    protected function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return Node|null
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param Node|null $node
     *
     * @return $this
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Prepare the api connection
     *
     * @param Command $command
     * @param Node $node
     *
     * @return $this
     */
    public function prepare(Command $command, Node $node)
    {
        $this->setCommand($command);
        $this->setNode($node);

        return $this;
    }

    /**
     * @return Command\Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return null
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * send
     *
     * @return bool
     */
    abstract public function send();

    /**
     * Closes the connection to the Riak Interface
     *
     * @return null
     */
    abstract public function closeConnection();
}
