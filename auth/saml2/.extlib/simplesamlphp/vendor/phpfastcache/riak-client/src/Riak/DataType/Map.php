<?php

/*
Copyright 2014 Basho Technologies, Inc.

Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file
distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file
to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance
with the License.  You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an
"AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the License for the
specific language governing permissions and limitations under the License.
*/

namespace Basho\Riak\DataType;

use Basho\Riak\DataType;

/**
 * Data structure for map crdt
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class Map extends DataType
{
    /**
     * {@inheritdoc}
     */
    const TYPE = 'map';

    /**
     * Used within a composite key to identify a flag (bool) element
     *
     * @var string
     */
    const FLAG = 'flag';

    /**
     * Used within a composite key to identify a register (string) element
     *
     * @var string
     */
    const REGISTER = 'register';

    /**
     * @var string
     */
    private $context;

    /**
     * @param array $data
     * @param $context
     */
    public function __construct(array $data, $context)
    {
        $this->data = $data;
        $this->context = $context;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function getRegister($key)
    {
        return $this->getDataByKey($key, static::REGISTER);
    }

    protected function getDataByKey($key, $type)
    {
        $compKey = $this->getCompKey($key, $type);
        if (!isset($this->data[$compKey])) {
            throw new Exception("{$type} {$key} not found within Map.");
        }

        return $this->data[$compKey];
    }

    /**
     * Fetches the composite key used with
     *
     * @param $key
     * @param $type
     *
     * @return string
     */
    protected function getCompKey($key, $type)
    {
        return sprintf('%s_%s', $key, $type);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function getFlag($key)
    {
        return $this->getDataByKey($key, static::FLAG) == 'enabled' ? TRUE : FALSE;
    }

    /**
     * @param $key
     *
     * @return Counter
     */
    public function getCounter($key)
    {
        return new Counter($this->getDataByKey($key, Counter::TYPE));
    }

    /**
     * @param $key
     *
     * @return Set
     */
    public function getSet($key)
    {
        return new Set($this->getDataByKey($key, Set::TYPE), $this->context);
    }

    /**
     * @param $key
     *
     * @return Map
     */
    public function getMap($key)
    {
        return new Map($this->getDataByKey($key, Map::TYPE), $this->context);
    }

    public function getContext()
    {
        return $this->context;
    }
}