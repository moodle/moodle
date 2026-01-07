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

class GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequest extends \Google\Collection
{
  protected $collection_key = 'objectives';
  /**
   * Required. The DeployedModel ID of the
   * [ModelDeploymentMonitoringObjectiveConfig.deployed_model_id].
   *
   * @var string
   */
  public $deployedModelId;
  /**
   * The latest timestamp of stats being generated. If not set, indicates
   * feching stats till the latest possible one.
   *
   * @var string
   */
  public $endTime;
  /**
   * The feature display name. If specified, only return the stats belonging to
   * this feature. Format: ModelMonitoringStatsAnomalies.FeatureHistoricStatsAno
   * malies.feature_display_name, example: "user_destination".
   *
   * @var string
   */
  public $featureDisplayName;
  protected $objectivesType = GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequestStatsAnomaliesObjective::class;
  protected $objectivesDataType = 'array';
  /**
   * The standard list page size.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A page token received from a previous
   * JobService.SearchModelDeploymentMonitoringStatsAnomalies call.
   *
   * @var string
   */
  public $pageToken;
  /**
   * The earliest timestamp of stats being generated. If not set, indicates
   * fetching stats till the earliest possible one.
   *
   * @var string
   */
  public $startTime;

  /**
   * Required. The DeployedModel ID of the
   * [ModelDeploymentMonitoringObjectiveConfig.deployed_model_id].
   *
   * @param string $deployedModelId
   */
  public function setDeployedModelId($deployedModelId)
  {
    $this->deployedModelId = $deployedModelId;
  }
  /**
   * @return string
   */
  public function getDeployedModelId()
  {
    return $this->deployedModelId;
  }
  /**
   * The latest timestamp of stats being generated. If not set, indicates
   * feching stats till the latest possible one.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The feature display name. If specified, only return the stats belonging to
   * this feature. Format: ModelMonitoringStatsAnomalies.FeatureHistoricStatsAno
   * malies.feature_display_name, example: "user_destination".
   *
   * @param string $featureDisplayName
   */
  public function setFeatureDisplayName($featureDisplayName)
  {
    $this->featureDisplayName = $featureDisplayName;
  }
  /**
   * @return string
   */
  public function getFeatureDisplayName()
  {
    return $this->featureDisplayName;
  }
  /**
   * Required. Objectives of the stats to retrieve.
   *
   * @param GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequestStatsAnomaliesObjective[] $objectives
   */
  public function setObjectives($objectives)
  {
    $this->objectives = $objectives;
  }
  /**
   * @return GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequestStatsAnomaliesObjective[]
   */
  public function getObjectives()
  {
    return $this->objectives;
  }
  /**
   * The standard list page size.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * A page token received from a previous
   * JobService.SearchModelDeploymentMonitoringStatsAnomalies call.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * The earliest timestamp of stats being generated. If not set, indicates
   * fetching stats till the earliest possible one.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequest');
