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

class ReportSummaryComputeEngineFinding extends \Google\Collection
{
  protected $collection_key = 'machineSeriesAllocations';
  /**
   * Count of assets which were allocated.
   *
   * @var string
   */
  public $allocatedAssetCount;
  /**
   * Set of disk types allocated to assets.
   *
   * @var string[]
   */
  public $allocatedDiskTypes;
  /**
   * Set of regions in which the assets were allocated.
   *
   * @var string[]
   */
  public $allocatedRegions;
  protected $machineSeriesAllocationsType = ReportSummaryMachineSeriesAllocation::class;
  protected $machineSeriesAllocationsDataType = 'array';

  /**
   * Count of assets which were allocated.
   *
   * @param string $allocatedAssetCount
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
   * Set of disk types allocated to assets.
   *
   * @param string[] $allocatedDiskTypes
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
   * Set of regions in which the assets were allocated.
   *
   * @param string[] $allocatedRegions
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
   * Distribution of assets based on the Machine Series.
   *
   * @param ReportSummaryMachineSeriesAllocation[] $machineSeriesAllocations
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
class_alias(ReportSummaryComputeEngineFinding::class, 'Google_Service_MigrationCenterAPI_ReportSummaryComputeEngineFinding');
