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

namespace Google\Service\ManagedKafka;

class CapacityConfig extends \Google\Model
{
  /**
   * Required. The memory to provision for the cluster in bytes. The CPU:memory
   * ratio (vCPU:GiB) must be between 1:1 and 1:8. Minimum: 3221225472 (3 GiB).
   *
   * @var string
   */
  public $memoryBytes;
  /**
   * Required. The number of vCPUs to provision for the cluster. Minimum: 3.
   *
   * @var string
   */
  public $vcpuCount;

  /**
   * Required. The memory to provision for the cluster in bytes. The CPU:memory
   * ratio (vCPU:GiB) must be between 1:1 and 1:8. Minimum: 3221225472 (3 GiB).
   *
   * @param string $memoryBytes
   */
  public function setMemoryBytes($memoryBytes)
  {
    $this->memoryBytes = $memoryBytes;
  }
  /**
   * @return string
   */
  public function getMemoryBytes()
  {
    return $this->memoryBytes;
  }
  /**
   * Required. The number of vCPUs to provision for the cluster. Minimum: 3.
   *
   * @param string $vcpuCount
   */
  public function setVcpuCount($vcpuCount)
  {
    $this->vcpuCount = $vcpuCount;
  }
  /**
   * @return string
   */
  public function getVcpuCount()
  {
    return $this->vcpuCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CapacityConfig::class, 'Google_Service_ManagedKafka_CapacityConfig');
