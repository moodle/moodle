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

class DataCollectionOptions extends \Google\Model
{
  /**
   * Optional. Indicates whether diagnostic collection is enabled for the VM
   * cluster
   *
   * @var bool
   */
  public $diagnosticsEventsEnabled;
  /**
   * Optional. Indicates whether health monitoring is enabled for the VM cluster
   *
   * @var bool
   */
  public $healthMonitoringEnabled;
  /**
   * Optional. Indicates whether incident logs and trace collection are enabled
   * for the VM cluster
   *
   * @var bool
   */
  public $incidentLogsEnabled;

  /**
   * Optional. Indicates whether diagnostic collection is enabled for the VM
   * cluster
   *
   * @param bool $diagnosticsEventsEnabled
   */
  public function setDiagnosticsEventsEnabled($diagnosticsEventsEnabled)
  {
    $this->diagnosticsEventsEnabled = $diagnosticsEventsEnabled;
  }
  /**
   * @return bool
   */
  public function getDiagnosticsEventsEnabled()
  {
    return $this->diagnosticsEventsEnabled;
  }
  /**
   * Optional. Indicates whether health monitoring is enabled for the VM cluster
   *
   * @param bool $healthMonitoringEnabled
   */
  public function setHealthMonitoringEnabled($healthMonitoringEnabled)
  {
    $this->healthMonitoringEnabled = $healthMonitoringEnabled;
  }
  /**
   * @return bool
   */
  public function getHealthMonitoringEnabled()
  {
    return $this->healthMonitoringEnabled;
  }
  /**
   * Optional. Indicates whether incident logs and trace collection are enabled
   * for the VM cluster
   *
   * @param bool $incidentLogsEnabled
   */
  public function setIncidentLogsEnabled($incidentLogsEnabled)
  {
    $this->incidentLogsEnabled = $incidentLogsEnabled;
  }
  /**
   * @return bool
   */
  public function getIncidentLogsEnabled()
  {
    return $this->incidentLogsEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataCollectionOptions::class, 'Google_Service_OracleDatabase_DataCollectionOptions');
