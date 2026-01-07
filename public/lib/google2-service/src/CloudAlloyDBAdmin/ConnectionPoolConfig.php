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

namespace Google\Service\CloudAlloyDBAdmin;

class ConnectionPoolConfig extends \Google\Model
{
  /**
   * Optional. Whether to enable Managed Connection Pool (MCP).
   *
   * @var bool
   */
  public $enabled;
  /**
   * Optional. Connection Pool flags, as a list of "key": "value" pairs.
   *
   * @var string[]
   */
  public $flags;
  /**
   * Output only. The number of running poolers per instance.
   *
   * @var int
   */
  public $poolerCount;

  /**
   * Optional. Whether to enable Managed Connection Pool (MCP).
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Optional. Connection Pool flags, as a list of "key": "value" pairs.
   *
   * @param string[] $flags
   */
  public function setFlags($flags)
  {
    $this->flags = $flags;
  }
  /**
   * @return string[]
   */
  public function getFlags()
  {
    return $this->flags;
  }
  /**
   * Output only. The number of running poolers per instance.
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
class_alias(ConnectionPoolConfig::class, 'Google_Service_CloudAlloyDBAdmin_ConnectionPoolConfig');
