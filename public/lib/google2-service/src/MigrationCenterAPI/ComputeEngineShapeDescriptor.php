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

class ComputeEngineShapeDescriptor extends \Google\Collection
{
  protected $collection_key = 'storage';
  /**
   * Output only. Number of logical cores.
   *
   * @var int
   */
  public $logicalCoreCount;
  /**
   * Output only. Compute Engine machine type.
   *
   * @var string
   */
  public $machineType;
  /**
   * Memory in mebibytes.
   *
   * @var int
   */
  public $memoryMb;
  /**
   * Number of physical cores.
   *
   * @var int
   */
  public $physicalCoreCount;
  /**
   * Output only. Compute Engine machine series.
   *
   * @var string
   */
  public $series;
  protected $storageType = ComputeStorageDescriptor::class;
  protected $storageDataType = 'array';

  /**
   * Output only. Number of logical cores.
   *
   * @param int $logicalCoreCount
   */
  public function setLogicalCoreCount($logicalCoreCount)
  {
    $this->logicalCoreCount = $logicalCoreCount;
  }
  /**
   * @return int
   */
  public function getLogicalCoreCount()
  {
    return $this->logicalCoreCount;
  }
  /**
   * Output only. Compute Engine machine type.
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
  /**
   * Memory in mebibytes.
   *
   * @param int $memoryMb
   */
  public function setMemoryMb($memoryMb)
  {
    $this->memoryMb = $memoryMb;
  }
  /**
   * @return int
   */
  public function getMemoryMb()
  {
    return $this->memoryMb;
  }
  /**
   * Number of physical cores.
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
   * Output only. Compute Engine machine series.
   *
   * @param string $series
   */
  public function setSeries($series)
  {
    $this->series = $series;
  }
  /**
   * @return string
   */
  public function getSeries()
  {
    return $this->series;
  }
  /**
   * Output only. Compute Engine storage. Never empty.
   *
   * @param ComputeStorageDescriptor[] $storage
   */
  public function setStorage($storage)
  {
    $this->storage = $storage;
  }
  /**
   * @return ComputeStorageDescriptor[]
   */
  public function getStorage()
  {
    return $this->storage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeEngineShapeDescriptor::class, 'Google_Service_MigrationCenterAPI_ComputeEngineShapeDescriptor');
