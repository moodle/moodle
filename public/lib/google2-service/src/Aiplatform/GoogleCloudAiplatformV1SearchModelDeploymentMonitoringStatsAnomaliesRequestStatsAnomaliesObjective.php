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

class GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequestStatsAnomaliesObjective extends \Google\Model
{
  /**
   * Default value, should not be set.
   */
  public const TYPE_MODEL_DEPLOYMENT_MONITORING_OBJECTIVE_TYPE_UNSPECIFIED = 'MODEL_DEPLOYMENT_MONITORING_OBJECTIVE_TYPE_UNSPECIFIED';
  /**
   * Raw feature values' stats to detect skew between Training-Prediction
   * datasets.
   */
  public const TYPE_RAW_FEATURE_SKEW = 'RAW_FEATURE_SKEW';
  /**
   * Raw feature values' stats to detect drift between Serving-Prediction
   * datasets.
   */
  public const TYPE_RAW_FEATURE_DRIFT = 'RAW_FEATURE_DRIFT';
  /**
   * Feature attribution scores to detect skew between Training-Prediction
   * datasets.
   */
  public const TYPE_FEATURE_ATTRIBUTION_SKEW = 'FEATURE_ATTRIBUTION_SKEW';
  /**
   * Feature attribution scores to detect skew between Prediction datasets
   * collected within different time windows.
   */
  public const TYPE_FEATURE_ATTRIBUTION_DRIFT = 'FEATURE_ATTRIBUTION_DRIFT';
  /**
   * If set, all attribution scores between
   * SearchModelDeploymentMonitoringStatsAnomaliesRequest.start_time and
   * SearchModelDeploymentMonitoringStatsAnomaliesRequest.end_time are fetched,
   * and page token doesn't take effect in this case. Only used to retrieve
   * attribution score for the top Features which has the highest attribution
   * score in the latest monitoring run.
   *
   * @var int
   */
  public $topFeatureCount;
  /**
   * @var string
   */
  public $type;

  /**
   * If set, all attribution scores between
   * SearchModelDeploymentMonitoringStatsAnomaliesRequest.start_time and
   * SearchModelDeploymentMonitoringStatsAnomaliesRequest.end_time are fetched,
   * and page token doesn't take effect in this case. Only used to retrieve
   * attribution score for the top Features which has the highest attribution
   * score in the latest monitoring run.
   *
   * @param int $topFeatureCount
   */
  public function setTopFeatureCount($topFeatureCount)
  {
    $this->topFeatureCount = $topFeatureCount;
  }
  /**
   * @return int
   */
  public function getTopFeatureCount()
  {
    return $this->topFeatureCount;
  }
  /**
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequestStatsAnomaliesObjective::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequestStatsAnomaliesObjective');
