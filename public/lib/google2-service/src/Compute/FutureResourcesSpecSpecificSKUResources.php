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

class FutureResourcesSpecSpecificSKUResources extends \Google\Collection
{
  protected $collection_key = 'localSsdPartitions';
  /**
   * Size of the request, in instance count.
   *
   * @var string
   */
  public $instanceCount;
  protected $localSsdPartitionsType = FutureResourcesSpecLocalSsdPartition::class;
  protected $localSsdPartitionsDataType = 'array';
  /**
   * The machine type to use for instances that will use the reservation. This
   * field only accepts machine type names. e.g. n2-standard-4 and does not
   * accept machine type full or partial url. e.g. projects/my-l7ilb-
   * project/zones/us-central1-a/machineTypes/n2-standard-4. Use for GPU
   * reservations.
   *
   * @var string
   */
  public $machineType;

  /**
   * Size of the request, in instance count.
   *
   * @param string $instanceCount
   */
  public function setInstanceCount($instanceCount)
  {
    $this->instanceCount = $instanceCount;
  }
  /**
   * @return string
   */
  public function getInstanceCount()
  {
    return $this->instanceCount;
  }
  /**
   * Local SSD partitions. You do not have to include SSD partitions that are
   * built in the machine type.
   *
   * @param FutureResourcesSpecLocalSsdPartition[] $localSsdPartitions
   */
  public function setLocalSsdPartitions($localSsdPartitions)
  {
    $this->localSsdPartitions = $localSsdPartitions;
  }
  /**
   * @return FutureResourcesSpecLocalSsdPartition[]
   */
  public function getLocalSsdPartitions()
  {
    return $this->localSsdPartitions;
  }
  /**
   * The machine type to use for instances that will use the reservation. This
   * field only accepts machine type names. e.g. n2-standard-4 and does not
   * accept machine type full or partial url. e.g. projects/my-l7ilb-
   * project/zones/us-central1-a/machineTypes/n2-standard-4. Use for GPU
   * reservations.
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
class_alias(FutureResourcesSpecSpecificSKUResources::class, 'Google_Service_Compute_FutureResourcesSpecSpecificSKUResources');
