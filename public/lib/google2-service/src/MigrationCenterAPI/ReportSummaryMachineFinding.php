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

class ReportSummaryMachineFinding extends \Google\Collection
{
  protected $collection_key = 'machineSeriesAllocations';
  /**
   * @var string
   */
  public $allocatedAssetCount;
  /**
   * @var string[]
   */
  public $allocatedDiskTypes;
  /**
   * @var string[]
   */
  public $allocatedRegions;
  protected $machineSeriesAllocationsType = ReportSummaryMachineSeriesAllocation::class;
  protected $machineSeriesAllocationsDataType = 'array';

  /**
   * @param string
   */
  public function setAllocatedAssetCount($allocatedAssetCount)
  {
    $this->allocatedAssetCount = $allocatedAssetCount;
  }
  /**
   * @return string
   */
  public function getAllocatedAssetCount()
  {
    return $this->allocatedAssetCount;
  }
  /**
   * @param string[]
   */
  public function setAllocatedDiskTypes($allocatedDiskTypes)
  {
    $this->allocatedDiskTypes = $allocatedDiskTypes;
  }
  /**
   * @return string[]
   */
  public function getAllocatedDiskTypes()
  {
    return $this->allocatedDiskTypes;
  }
  /**
   * @param string[]
   */
  public function setAllocatedRegions($allocatedRegions)
  {
    $this->allocatedRegions = $allocatedRegions;
  }
  /**
   * @return string[]
   */
  public function getAllocatedRegions()
  {
    return $this->allocatedRegions;
  }
  /**
   * @param ReportSummaryMachineSeriesAllocation[]
   */
  public function setMachineSeriesAllocations($machineSeriesAllocations)
  {
    $this->machineSeriesAllocations = $machineSeriesAllocations;
  }
  /**
   * @return ReportSummaryMachineSeriesAllocation[]
   */
  public function getMachineSeriesAllocations()
  {
    return $this->machineSeriesAllocations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSummaryMachineFinding::class, 'Google_Service_MigrationCenterAPI_ReportSummaryMachineFinding');
