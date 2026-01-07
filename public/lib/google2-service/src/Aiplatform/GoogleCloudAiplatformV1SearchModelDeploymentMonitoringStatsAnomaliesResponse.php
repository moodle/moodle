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

class GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesResponse extends \Google\Collection
{
  protected $collection_key = 'monitoringStats';
  protected $monitoringStatsType = GoogleCloudAiplatformV1ModelMonitoringStatsAnomalies::class;
  protected $monitoringStatsDataType = 'array';
  /**
   * The page token that can be used by the next
   * JobService.SearchModelDeploymentMonitoringStatsAnomalies call.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Stats retrieved for requested objectives. There are at most 1000 ModelMonit
   * oringStatsAnomalies.FeatureHistoricStatsAnomalies.prediction_stats in the
   * response.
   *
   * @param GoogleCloudAiplatformV1ModelMonitoringStatsAnomalies[] $monitoringStats
   */
  public function setMonitoringStats($monitoringStats)
  {
    $this->monitoringStats = $monitoringStats;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelMonitoringStatsAnomalies[]
   */
  public function getMonitoringStats()
  {
    return $this->monitoringStats;
  }
  /**
   * The page token that can be used by the next
   * JobService.SearchModelDeploymentMonitoringStatsAnomalies call.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesResponse');
