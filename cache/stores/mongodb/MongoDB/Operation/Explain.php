<?php
/*
 * Copyright 2018 MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Operation;

use MongoDB\Driver\Command;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\Server;
use MongoDB\Driver\Session;
use MongoDB\Exception\InvalidArgumentException;
use MongoDB\Exception\UnsupportedException;
use function current;
use function is_array;
use function is_string;
use function MongoDB\server_supports_feature;

/**
 * Operation for the explain command.
 *
 * @api
 * @see \MongoDB\Collection::explain()
 * @see http://docs.mongodb.org/manual/reference/command/explain/
 */
class Explain implements Executable
{
    const VERBOSITY_ALL_PLANS = 'allPlansExecution';
    const VERBOSITY_EXEC_STATS = 'executionStats';
    const VERBOSITY_QUERY = 'queryPlanner';

    /** @var integer */
    private static $wireVersionForDistinct = 4;

    /** @var integer */
    private static $wireVersionForFindAndModify = 4;

    /** @var string */
    private $databaseName;

    /** @var Explainable */
    private $explainable;

    /** @var array */
    private $options;

    /**
     * Constructs an explain command for explainable operations.
     *
     * Supported options:
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): Read preference.
     *
     *  * session (MongoDB\Driver\Session): Client session.
     *
     *  * typeMap (array): Type map for BSON deserialization. This will be used
     *    used for the returned command result document.
     *
     *  * verbosity (string): The mode in which the explain command will be run.
     *
     * @param string      $databaseName Database name
     * @param Explainable $explainable  Operation to explain
     * @param array       $options      Command options
     * @throws InvalidArgumentException for parameter/option parsing errors
     */
    public function __construct($databaseName, Explainable $explainable, array $options = [])
    {
        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], ReadPreference::class);
        }

        if (isset($options['session']) && ! $options['session'] instanceof Session) {
            throw InvalidArgumentException::invalidType('"session" option', $options['session'], Session::class);
        }

        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }

        if (isset($options['verbosity']) && ! is_string($options['verbosity'])) {
            throw InvalidArgumentException::invalidType('"verbosity" option', $options['verbosity'], 'string');
        }

        $this->databaseName = $databaseName;
        $this->explainable = $explainable;
        $this->options = $options;
    }

    public function execute(Server $server)
    {
        if ($this->explainable instanceof Distinct && ! server_supports_feature($server, self::$wireVersionForDistinct)) {
            throw UnsupportedException::explainNotSupported();
        }

        if ($this->isFindAndModify($this->explainable) && ! server_supports_feature($server, self::$wireVersionForFindAndModify)) {
            throw UnsupportedException::explainNotSupported();
        }

        $cmd = ['explain' => $this->explainable->getCommandDocument($server)];

        if (isset($this->options['verbosity'])) {
            $cmd['verbosity'] = $this->options['verbosity'];
        }

        $cursor = $server->executeCommand($this->databaseName, new Command($cmd), $this->createOptions());

        if (isset($this->options['typeMap'])) {
            $cursor->setTypeMap($this->options['typeMap']);
        }

        return current($cursor->toArray());
    }

    /**
     * Create options for executing the command.
     *
     * @see http://php.net/manual/en/mongodb-driver-server.executecommand.php
     * @return array
     */
    private function createOptions()
    {
        $options = [];

        if (isset($this->options['readPreference'])) {
            $options['readPreference'] = $this->options['readPreference'];
        }

        if (isset($this->options['session'])) {
            $options['session'] = $this->options['session'];
        }

        return $options;
    }

    private function isFindAndModify($explainable)
    {
        if ($explainable instanceof FindAndModify || $explainable instanceof FindOneAndDelete || $explainable instanceof FindOneAndReplace || $explainable instanceof FindOneAndUpdate) {
            return true;
        }

        return false;
    }
}
