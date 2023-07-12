<?php

namespace Basho\Riak;

/**
 * CommandInterface
 *
 * The interface for implementing a new Riak Command class.
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
interface CommandInterface
{
    public function getMethod();

    public function hasParameters();

    public function getParameters();

    public function getData();

    public function getEncodedData();

    public function getBucket();

    public function execute();
}
