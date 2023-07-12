<?php

namespace Basho\Riak;

/**
 * Interface ApiInterface
 *
 * Forces object structure for API bridge classes
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
interface ApiInterface
{
    /**
     * Prepares the API bridge for the command to be sent
     *
     * @param Command $command
     * @param Node    $node
     */
    public function prepare(Command $command, Node $node);

    /**
     * Sends the command over the wire to Riak
     */
    public function send();

    /**
     * Gets the complete request string
     *
     * @return string
     */
    public function getRequest();

    /**
     * @return Command\Response|null
     */
    public function getResponse();

    /**
     * Closes the connection to the Riak Interface
     *
     * @return null
     */
    public function closeConnection();
}
