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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ModelDeploymentMonitoringScheduleConfig extends \Google\Model
{
  /**
   * Required. The model monitoring job scheduling interval. It will be rounded
   * up to next full hour. This defines how often the monitoring jobs are
   * triggered.
   *
   * @var string
   */
  public $monitorInterval;
  /**
   * The time window of the prediction data being included in each prediction
   * dataset. This window specifies how long the data should be collected from
   * historical model results for each run. If not set,
   * ModelDeploymentMonitoringScheduleConfig.monitor_interval will be used. e.g.
   * If currently the cutoff time is 2022-01-08 14:30:00 and the monitor_window
   * is set to be 3600, then data from 2022-01-08 13:30:00 to 2022-01-08
   * 14:30:00 will be retrieved and aggregated to calculate the monitoring
   * statistics.
   *
   * @var string
   */
  public $monitorWindow;

  /**
   * Required. The model monitoring job scheduling interval. It will be rounded
   * up to next full hour. This defines how often the monitoring jobs are
   * triggered.
   *
   * @param string $monitorInterval
   */
  public function setMonitorInterval($monitorInterval)
  {
    $this->monitorInterval = $monitorInterval;
  }
  /**
   * @return string
   */
  public function getMonitorInterval()
  {
    return $this->monitorInterval;
  }
  /**
   * The time window of the prediction data being included in each prediction
   * dataset. This window specifies how long the data should be collected from
   * historical model results for each run. If not set,
   * ModelDeploymentMonitoringScheduleConfig.monitor_interval will be used. e.g.
   * If currently the cutoff time is 2022-01-08 14:30:00 and the monitor_window
   * is set to be 3600, then data from 2022-01-08 13:30:00 to 2022-01-08
   * 14:30:00 will be retrieved and aggregated to calculate the monitoring
   * statistics.
   *
   * @param string $monitorWindow
   */
  public function setMonitorWindow($monitorWindow)
  {
    $this->monitorWindow = $monitorWindow;
  }
  /**
   * @return string
   */
  public function getMonitorWindow()
  {
    return $this->monitorWindow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelDeploymentMonitoringScheduleConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelDeploymentMonitoringScheduleConfig');
