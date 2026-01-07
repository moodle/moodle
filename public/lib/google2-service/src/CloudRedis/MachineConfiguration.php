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

namespace Google\Service\CloudRedis;

class MachineConfiguration extends \Google\Model
{
  /**
   * The number of CPUs. Deprecated. Use vcpu_count instead. TODO(b/342344482)
   * add proto validations again after bug fix.
   *
   * @deprecated
   * @var int
   */
  public $cpuCount;
  /**
   * Memory size in bytes. TODO(b/342344482) add proto validations again after
   * bug fix.
   *
   * @var string
   */
  public $memorySizeInBytes;
  /**
   * Optional. Number of shards (if applicable).
   *
   * @var int
   */
  public $shardCount;
  /**
   * Optional. The number of vCPUs. TODO(b/342344482) add proto validations
   * again after bug fix.
   *
   * @var 
   */
  public $vcpuCount;

  /**
   * The number of CPUs. Deprecated. Use vcpu_count instead. TODO(b/342344482)
   * add proto validations again after bug fix.
   *
   * @deprecated
   * @param int $cpuCount
   */
  public function setCpuCount($cpuCount)
  {
    $this->cpuCount = $cpuCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getCpuCount()
  {
    return $this->cpuCount;
  }
  /**
   * Memory size in bytes. TODO(b/342344482) add proto validations again after
   * bug fix.
   *
   * @param string $memorySizeInBytes
   */
  public function setMemorySizeInBytes($memorySizeInBytes)
  {
    $this->memorySizeInBytes = $memorySizeInBytes;
  }
  /**
   * @return string
   */
  public function getMemorySizeInBytes()
  {
    return $this->memorySizeInBytes;
  }
  /**
   * Optional. Number of shards (if applicable).
   *
   * @param int $shardCount
   */
  public function setShardCount($shardCount)
  {
    $this->shardCount = $shardCount;
  }
  /**
   * @return int
   */
  public function getShardCount()
  {
    return $this->shardCount;
  }
  public function setVcpuCount($vcpuCount)
  {
    $this->vcpuCount = $vcpuCount;
  }
  public function getVcpuCount()
  {
    return $this->vcpuCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachineConfiguration::class, 'Google_Service_CloudRedis_MachineConfiguration');
