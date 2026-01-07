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

class GoogleCloudAiplatformV1FeaturestoreMonitoringConfigSnapshotAnalysis extends \Google\Model
{
  /**
   * The monitoring schedule for snapshot analysis. For EntityType-level config:
   * unset / disabled = true indicates disabled by default for Features under
   * it; otherwise by default enable snapshot analysis monitoring with
   * monitoring_interval for Features under it. Feature-level config: disabled =
   * true indicates disabled regardless of the EntityType-level config; unset
   * monitoring_interval indicates going with EntityType-level config; otherwise
   * run snapshot analysis monitoring with monitoring_interval regardless of the
   * EntityType-level config. Explicitly Disable the snapshot analysis based
   * monitoring.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Configuration of the snapshot analysis based monitoring pipeline running
   * interval. The value indicates number of days.
   *
   * @var int
   */
  public $monitoringIntervalDays;
  /**
   * Customized export features time window for snapshot analysis. Unit is one
   * day. Default value is 3 weeks. Minimum value is 1 day. Maximum value is
   * 4000 days.
   *
   * @var int
   */
  public $stalenessDays;

  /**
   * The monitoring schedule for snapshot analysis. For EntityType-level config:
   * unset / disabled = true indicates disabled by default for Features under
   * it; otherwise by default enable snapshot analysis monitoring with
   * monitoring_interval for Features under it. Feature-level config: disabled =
   * true indicates disabled regardless of the EntityType-level config; unset
   * monitoring_interval indicates going with EntityType-level config; otherwise
   * run snapshot analysis monitoring with monitoring_interval regardless of the
   * EntityType-level config. Explicitly Disable the snapshot analysis based
   * monitoring.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Configuration of the snapshot analysis based monitoring pipeline running
   * interval. The value indicates number of days.
   *
   * @param int $monitoringIntervalDays
   */
  public function setMonitoringIntervalDays($monitoringIntervalDays)
  {
    $this->monitoringIntervalDays = $monitoringIntervalDays;
  }
  /**
   * @return int
   */
  public function getMonitoringIntervalDays()
  {
    return $this->monitoringIntervalDays;
  }
  /**
   * Customized export features time window for snapshot analysis. Unit is one
   * day. Default value is 3 weeks. Minimum value is 1 day. Maximum value is
   * 4000 days.
   *
   * @param int $stalenessDays
   */
  public function setStalenessDays($stalenessDays)
  {
    $this->stalenessDays = $stalenessDays;
  }
  /**
   * @return int
   */
  public function getStalenessDays()
  {
    return $this->stalenessDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeaturestoreMonitoringConfigSnapshotAnalysis::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeaturestoreMonitoringConfigSnapshotAnalysis');
