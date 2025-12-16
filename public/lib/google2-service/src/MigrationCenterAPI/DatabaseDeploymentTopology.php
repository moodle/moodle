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

namespace Google\Service\MigrationCenterAPI;

class DatabaseDeploymentTopology extends \Google\Collection
{
  protected $collection_key = 'instances';
  /**
   * Optional. Number of total logical cores.
   *
   * @var int
   */
  public $coreCount;
  /**
   * Optional. Number of total logical cores limited by db deployment.
   *
   * @var int
   */
  public $coreLimit;
  /**
   * Optional. Disk allocated in bytes.
   *
   * @var string
   */
  public $diskAllocatedBytes;
  /**
   * Optional. Disk used in bytes.
   *
   * @var string
   */
  public $diskUsedBytes;
  protected $instancesType = DatabaseInstance::class;
  protected $instancesDataType = 'array';
  /**
   * Optional. Total memory in bytes.
   *
   * @var string
   */
  public $memoryBytes;
  /**
   * Optional. Total memory in bytes limited by db deployment.
   *
   * @var string
   */
  public $memoryLimitBytes;
  /**
   * Optional. Number of total physical cores.
   *
   * @var int
   */
  public $physicalCoreCount;
  /**
   * Optional. Number of total physical cores limited by db deployment.
   *
   * @var int
   */
  public $physicalCoreLimit;

  /**
   * Optional. Number of total logical cores.
   *
   * @param int $coreCount
   */
  public function setCoreCount($coreCount)
  {
    $this->coreCount = $coreCount;
  }
  /**
   * @return int
   */
  public function getCoreCount()
  {
    return $this->coreCount;
  }
  /**
   * Optional. Number of total logical cores limited by db deployment.
   *
   * @param int $coreLimit
   */
  public function setCoreLimit($coreLimit)
  {
    $this->coreLimit = $coreLimit;
  }
  /**
   * @return int
   */
  public function getCoreLimit()
  {
    return $this->coreLimit;
  }
  /**
   * Optional. Disk allocated in bytes.
   *
   * @param string $diskAllocatedBytes
   */
  public function setDiskAllocatedBytes($diskAllocatedBytes)
  {
    $this->diskAllocatedBytes = $diskAllocatedBytes;
  }
  /**
   * @return string
   */
  public function getDiskAllocatedBytes()
  {
    return $this->diskAllocatedBytes;
  }
  /**
   * Optional. Disk used in bytes.
   *
   * @param string $diskUsedBytes
   */
  public function setDiskUsedBytes($diskUsedBytes)
  {
    $this->diskUsedBytes = $diskUsedBytes;
  }
  /**
   * @return string
   */
  public function getDiskUsedBytes()
  {
    return $this->diskUsedBytes;
  }
  /**
   * Optional. List of database instances.
   *
   * @param DatabaseInstance[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return DatabaseInstance[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Optional. Total memory in bytes.
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
   * Optional. Total memory in bytes limited by db deployment.
   *
   * @param string $memoryLimitBytes
   */
  public function setMemoryLimitBytes($memoryLimitBytes)
  {
    $this->memoryLimitBytes = $memoryLimitBytes;
  }
  /**
   * @return string
   */
  public function getMemoryLimitBytes()
  {
    return $this->memoryLimitBytes;
  }
  /**
   * Optional. Number of total physical cores.
   *
   * @param int $physicalCoreCount
   */
  public function setPhysicalCoreCount($physicalCoreCount)
  {
    $this->physicalCoreCount = $physicalCoreCount;
  }
  /**
   * @return int
   */
  public function getPhysicalCoreCount()
  {
    return $this->physicalCoreCount;
  }
  /**
   * Optional. Number of total physical cores limited by db deployment.
   *
   * @param int $physicalCoreLimit
   */
  public function setPhysicalCoreLimit($physicalCoreLimit)
  {
    $this->physicalCoreLimit = $physicalCoreLimit;
  }
  /**
   * @return int
   */
  public function getPhysicalCoreLimit()
  {
    return $this->physicalCoreLimit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseDeploymentTopology::class, 'Google_Service_MigrationCenterAPI_DatabaseDeploymentTopology');
