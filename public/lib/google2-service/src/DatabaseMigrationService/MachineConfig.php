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

namespace Google\Service\DatabaseMigrationService;

class MachineConfig extends \Google\Model
{
  /**
   * The number of CPU's in the VM instance.
   *
   * @var int
   */
  public $cpuCount;
  /**
   * Optional. Machine type of the VM instance. E.g. "n2-highmem-4",
   * "n2-highmem-8", "c4a-highmem-4-lssd". cpu_count must match the number of
   * vCPUs in the machine type.
   *
   * @var string
   */
  public $machineType;

  /**
   * The number of CPU's in the VM instance.
   *
   * @param int $cpuCount
   */
  public function setCpuCount($cpuCount)
  {
    $this->cpuCount = $cpuCount;
  }
  /**
   * @return int
   */
  public function getCpuCount()
  {
    return $this->cpuCount;
  }
  /**
   * Optional. Machine type of the VM instance. E.g. "n2-highmem-4",
   * "n2-highmem-8", "c4a-highmem-4-lssd". cpu_count must match the number of
   * vCPUs in the machine type.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachineConfig::class, 'Google_Service_DatabaseMigrationService_MachineConfig');
