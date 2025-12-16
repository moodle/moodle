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

namespace Google\Service\Backupdr;

class ComputeInstanceDataSourceProperties extends \Google\Model
{
  /**
   * The description of the Compute Engine instance.
   *
   * @var string
   */
  public $description;
  /**
   * The machine type of the instance.
   *
   * @var string
   */
  public $machineType;
  /**
   * Name of the compute instance backed up by the datasource.
   *
   * @var string
   */
  public $name;
  /**
   * The total number of disks attached to the Instance.
   *
   * @var string
   */
  public $totalDiskCount;
  /**
   * The sum of all the disk sizes.
   *
   * @var string
   */
  public $totalDiskSizeGb;

  /**
   * The description of the Compute Engine instance.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The machine type of the instance.
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
   * Name of the compute instance backed up by the datasource.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The total number of disks attached to the Instance.
   *
   * @param string $totalDiskCount
   */
  public function setTotalDiskCount($totalDiskCount)
  {
    $this->totalDiskCount = $totalDiskCount;
  }
  /**
   * @return string
   */
  public function getTotalDiskCount()
  {
    return $this->totalDiskCount;
  }
  /**
   * The sum of all the disk sizes.
   *
   * @param string $totalDiskSizeGb
   */
  public function setTotalDiskSizeGb($totalDiskSizeGb)
  {
    $this->totalDiskSizeGb = $totalDiskSizeGb;
  }
  /**
   * @return string
   */
  public function getTotalDiskSizeGb()
  {
    return $this->totalDiskSizeGb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeInstanceDataSourceProperties::class, 'Google_Service_Backupdr_ComputeInstanceDataSourceProperties');
