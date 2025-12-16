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

namespace Google\Service\SQLAdmin;

class ConnectionPoolConfig extends \Google\Collection
{
  protected $collection_key = 'flags';
  /**
   * Whether managed connection pooling is enabled.
   *
   * @var bool
   */
  public $connectionPoolingEnabled;
  protected $flagsType = ConnectionPoolFlags::class;
  protected $flagsDataType = 'array';
  /**
   * Output only. Number of connection poolers.
   *
   * @var int
   */
  public $poolerCount;

  /**
   * Whether managed connection pooling is enabled.
   *
   * @param bool $connectionPoolingEnabled
   */
  public function setConnectionPoolingEnabled($connectionPoolingEnabled)
  {
    $this->connectionPoolingEnabled = $connectionPoolingEnabled;
  }
  /**
   * @return bool
   */
  public function getConnectionPoolingEnabled()
  {
    return $this->connectionPoolingEnabled;
  }
  /**
   * Optional. List of connection pool configuration flags.
   *
   * @param ConnectionPoolFlags[] $flags
   */
  public function setFlags($flags)
  {
    $this->flags = $flags;
  }
  /**
   * @return ConnectionPoolFlags[]
   */
  public function getFlags()
  {
    return $this->flags;
  }
  /**
   * Output only. Number of connection poolers.
   *
   * @param int $poolerCount
   */
  public function setPoolerCount($poolerCount)
  {
    $this->poolerCount = $poolerCount;
  }
  /**
   * @return int
   */
  public function getPoolerCount()
  {
    return $this->poolerCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectionPoolConfig::class, 'Google_Service_SQLAdmin_ConnectionPoolConfig');
