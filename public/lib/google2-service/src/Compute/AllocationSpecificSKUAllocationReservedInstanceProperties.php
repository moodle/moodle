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

namespace Google\Service\Compute;

class AllocationSpecificSKUAllocationReservedInstanceProperties extends \Google\Collection
{
  protected $collection_key = 'localSsds';
  protected $guestAcceleratorsType = AcceleratorConfig::class;
  protected $guestAcceleratorsDataType = 'array';
  protected $localSsdsType = AllocationSpecificSKUAllocationAllocatedInstancePropertiesReservedDisk::class;
  protected $localSsdsDataType = 'array';
  /**
   * An opaque location hint used to place the allocation close to other
   * resources. This field is for use by internal tools that use the public API.
   *
   * @var string
   */
  public $locationHint;
  /**
   * Specifies type of machine (name only) which has fixed number of vCPUs and
   * fixed amount of memory. This also includes specifying custom machine type
   * following custom-NUMBER_OF_CPUS-AMOUNT_OF_MEMORY pattern.
   *
   * @var string
   */
  public $machineType;
  /**
   * Minimum cpu platform the reservation.
   *
   * @var string
   */
  public $minCpuPlatform;

  /**
   * Specifies accelerator type and count.
   *
   * @param AcceleratorConfig[] $guestAccelerators
   */
  public function setGuestAccelerators($guestAccelerators)
  {
    $this->guestAccelerators = $guestAccelerators;
  }
  /**
   * @return AcceleratorConfig[]
   */
  public function getGuestAccelerators()
  {
    return $this->guestAccelerators;
  }
  /**
   * Specifies amount of local ssd to reserve with each instance. The type of
   * disk is local-ssd.
   *
   * @param AllocationSpecificSKUAllocationAllocatedInstancePropertiesReservedDisk[] $localSsds
   */
  public function setLocalSsds($localSsds)
  {
    $this->localSsds = $localSsds;
  }
  /**
   * @return AllocationSpecificSKUAllocationAllocatedInstancePropertiesReservedDisk[]
   */
  public function getLocalSsds()
  {
    return $this->localSsds;
  }
  /**
   * An opaque location hint used to place the allocation close to other
   * resources. This field is for use by internal tools that use the public API.
   *
   * @param string $locationHint
   */
  public function setLocationHint($locationHint)
  {
    $this->locationHint = $locationHint;
  }
  /**
   * @return string
   */
  public function getLocationHint()
  {
    return $this->locationHint;
  }
  /**
   * Specifies type of machine (name only) which has fixed number of vCPUs and
   * fixed amount of memory. This also includes specifying custom machine type
   * following custom-NUMBER_OF_CPUS-AMOUNT_OF_MEMORY pattern.
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
   * Minimum cpu platform the reservation.
   *
   * @param string $minCpuPlatform
   */
  public function setMinCpuPlatform($minCpuPlatform)
  {
    $this->minCpuPlatform = $minCpuPlatform;
  }
  /**
   * @return string
   */
  public function getMinCpuPlatform()
  {
    return $this->minCpuPlatform;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationSpecificSKUAllocationReservedInstanceProperties::class, 'Google_Service_Compute_AllocationSpecificSKUAllocationReservedInstanceProperties');
