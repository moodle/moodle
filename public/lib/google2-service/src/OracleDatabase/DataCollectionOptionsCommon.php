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

namespace Google\Service\OracleDatabase;

class DataCollectionOptionsCommon extends \Google\Model
{
  /**
   * Optional. Indicates whether to enable data collection for diagnostics.
   *
   * @var bool
   */
  public $isDiagnosticsEventsEnabled;
  /**
   * Optional. Indicates whether to enable health monitoring.
   *
   * @var bool
   */
  public $isHealthMonitoringEnabled;
  /**
   * Optional. Indicates whether to enable incident logs and trace collection.
   *
   * @var bool
   */
  public $isIncidentLogsEnabled;

  /**
   * Optional. Indicates whether to enable data collection for diagnostics.
   *
   * @param bool $isDiagnosticsEventsEnabled
   */
  public function setIsDiagnosticsEventsEnabled($isDiagnosticsEventsEnabled)
  {
    $this->isDiagnosticsEventsEnabled = $isDiagnosticsEventsEnabled;
  }
  /**
   * @return bool
   */
  public function getIsDiagnosticsEventsEnabled()
  {
    return $this->isDiagnosticsEventsEnabled;
  }
  /**
   * Optional. Indicates whether to enable health monitoring.
   *
   * @param bool $isHealthMonitoringEnabled
   */
  public function setIsHealthMonitoringEnabled($isHealthMonitoringEnabled)
  {
    $this->isHealthMonitoringEnabled = $isHealthMonitoringEnabled;
  }
  /**
   * @return bool
   */
  public function getIsHealthMonitoringEnabled()
  {
    return $this->isHealthMonitoringEnabled;
  }
  /**
   * Optional. Indicates whether to enable incident logs and trace collection.
   *
   * @param bool $isIncidentLogsEnabled
   */
  public function setIsIncidentLogsEnabled($isIncidentLogsEnabled)
  {
    $this->isIncidentLogsEnabled = $isIncidentLogsEnabled;
  }
  /**
   * @return bool
   */
  public function getIsIncidentLogsEnabled()
  {
    return $this->isIncidentLogsEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataCollectionOptionsCommon::class, 'Google_Service_OracleDatabase_DataCollectionOptionsCommon');
