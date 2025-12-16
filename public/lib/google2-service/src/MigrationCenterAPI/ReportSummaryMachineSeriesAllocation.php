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

class ReportSummaryMachineSeriesAllocation extends \Google\Model
{
  /**
   * Count of assets allocated to this machine series.
   *
   * @var string
   */
  public $allocatedAssetCount;
  protected $machineSeriesType = MachineSeries::class;
  protected $machineSeriesDataType = '';

  /**
   * Count of assets allocated to this machine series.
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
   * The Machine Series (e.g. "E2", "N2")
   *
   * @param MachineSeries $machineSeries
   */
  public function setMachineSeries(MachineSeries $machineSeries)
  {
    $this->machineSeries = $machineSeries;
  }
  /**
   * @return MachineSeries
   */
  public function getMachineSeries()
  {
    return $this->machineSeries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSummaryMachineSeriesAllocation::class, 'Google_Service_MigrationCenterAPI_ReportSummaryMachineSeriesAllocation');
