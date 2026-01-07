<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudMemorystoreforMemcached;

class Node extends \Google\Model
{
  /**
   * Memcache version is not specified by customer
   */
  public const MEMCACHE_VERSION_MEMCACHE_VERSION_UNSPECIFIED = 'MEMCACHE_VERSION_UNSPECIFIED';
  /**
   * Memcached 1.5 version.
   */
  public const MEMCACHE_VERSION_MEMCACHE_1_5 = 'MEMCACHE_1_5';
  /**
   * Memcached 1.6.15 version.
   */
  public const MEMCACHE_VERSION_MEMCACHE_1_6_15 = 'MEMCACHE_1_6_15';
  /**
   * Node state is not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Node is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Node has been created and ready to be used.
   */
  public const STATE_READY = 'READY';
  /**
   * Node is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Node is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Output only. Hostname or IP address of the Memcached node used by the
   * clients to connect to the Memcached server on this node.
   *
   * @var string
   */
  public $host;
  /**
   * Output only. The full version of memcached server running on this node.
   * e.g. - memcached-1.5.16
   *
   * @var string
   */
  public $memcacheFullVersion;
  /**
   * Output only. Major version of memcached server running on this node, e.g.
   * MEMCACHE_1_5
   *
   * @var string
   */
  public $memcacheVersion;
  /**
   * Output only. Identifier of the Memcached node. The node id does not include
   * project or location like the Memcached instance name.
   *
   * @var string
   */
  public $nodeId;
  protected $parametersType = MemcacheParameters::class;
  protected $parametersDataType = '';
  /**
   * Output only. The port number of the Memcached server on this node.
   *
   * @var int
   */
  public $port;
  /**
   * Output only. Current state of the Memcached node.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Location (GCP Zone) for the Memcached node.
   *
   * @var string
   */
  public $zone;

  /**
   * Output only. Hostname or IP address of the Memcached node used by the
   * clients to connect to the Memcached server on this node.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Output only. The full version of memcached server running on this node.
   * e.g. - memcached-1.5.16
   *
   * @param string $memcacheFullVersion
   */
  public function setMemcacheFullVersion($memcacheFullVersion)
  {
    $this->memcacheFullVersion = $memcacheFullVersion;
  }
  /**
   * @return string
   */
  public function getMemcacheFullVersion()
  {
    return $this->memcacheFullVersion;
  }
  /**
   * Output only. Major version of memcached server running on this node, e.g.
   * MEMCACHE_1_5
   *
   * Accepted values: MEMCACHE_VERSION_UNSPECIFIED, MEMCACHE_1_5,
   * MEMCACHE_1_6_15
   *
   * @param self::MEMCACHE_VERSION_* $memcacheVersion
   */
  public function setMemcacheVersion($memcacheVersion)
  {
    $this->memcacheVersion = $memcacheVersion;
  }
  /**
   * @return self::MEMCACHE_VERSION_*
   */
  public function getMemcacheVersion()
  {
    return $this->memcacheVersion;
  }
  /**
   * Output only. Identifier of the Memcached node. The node id does not include
   * project or location like the Memcached instance name.
   *
   * @param string $nodeId
   */
  public function setNodeId($nodeId)
  {
    $this->nodeId = $nodeId;
  }
  /**
   * @return string
   */
  public function getNodeId()
  {
    return $this->nodeId;
  }
  /**
   * User defined parameters currently applied to the node.
   *
   * @param MemcacheParameters $parameters
   */
  public function setParameters(MemcacheParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return MemcacheParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Output only. The port number of the Memcached server on this node.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Output only. Current state of the Memcached node.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, DELETING, UPDATING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Location (GCP Zone) for the Memcached node.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Node::class, 'Google_Service_CloudMemorystoreforMemcached_Node');
