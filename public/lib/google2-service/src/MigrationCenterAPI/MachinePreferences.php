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

class MachinePreferences extends \Google\Collection
{
  protected $collection_key = 'allowedMachineSeries';
  protected $allowedMachineSeriesType = MachineSeries::class;
  protected $allowedMachineSeriesDataType = 'array';

  /**
   * Compute Engine machine series to consider for insights and recommendations.
   * If empty, no restriction is applied on the machine series.
   *
   * @param MachineSeries[] $allowedMachineSeries
   */
  public function setAllowedMachineSeries($allowedMachineSeries)
  {
    $this->allowedMachineSeries = $allowedMachineSeries;
  }
  /**
   * @return MachineSeries[]
   */
  public function getAllowedMachineSeries()
  {
    return $this->allowedMachineSeries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachinePreferences::class, 'Google_Service_MigrationCenterAPI_MachinePreferences');
