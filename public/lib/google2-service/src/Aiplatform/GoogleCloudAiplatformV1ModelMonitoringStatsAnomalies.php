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

class GoogleCloudAiplatformV1ModelMonitoringStatsAnomalies extends \Google\Collection
{
  /**
   * Default value, should not be set.
   */
  public const OBJECTIVE_MODEL_DEPLOYMENT_MONITORING_OBJECTIVE_TYPE_UNSPECIFIED = 'MODEL_DEPLOYMENT_MONITORING_OBJECTIVE_TYPE_UNSPECIFIED';
  /**
   * Raw feature values' stats to detect skew between Training-Prediction
   * datasets.
   */
  public const OBJECTIVE_RAW_FEATURE_SKEW = 'RAW_FEATURE_SKEW';
  /**
   * Raw feature values' stats to detect drift between Serving-Prediction
   * datasets.
   */
  public const OBJECTIVE_RAW_FEATURE_DRIFT = 'RAW_FEATURE_DRIFT';
  /**
   * Feature attribution scores to detect skew between Training-Prediction
   * datasets.
   */
  public const OBJECTIVE_FEATURE_ATTRIBUTION_SKEW = 'FEATURE_ATTRIBUTION_SKEW';
  /**
   * Feature attribution scores to detect skew between Prediction datasets
   * collected within different time windows.
   */
  public const OBJECTIVE_FEATURE_ATTRIBUTION_DRIFT = 'FEATURE_ATTRIBUTION_DRIFT';
  protected $collection_key = 'featureStats';
  /**
   * Number of anomalies within all stats.
   *
   * @var int
   */
  public $anomalyCount;
  /**
   * Deployed Model ID.
   *
   * @var string
   */
  public $deployedModelId;
  protected $featureStatsType = GoogleCloudAiplatformV1ModelMonitoringStatsAnomaliesFeatureHistoricStatsAnomalies::class;
  protected $featureStatsDataType = 'array';
  /**
   * Model Monitoring Objective those stats and anomalies belonging to.
   *
   * @var string
   */
  public $objective;

  /**
   * Number of anomalies within all stats.
   *
   * @param int $anomalyCount
   */
  public function setAnomalyCount($anomalyCount)
  {
    $this->anomalyCount = $anomalyCount;
  }
  /**
   * @return int
   */
  public function getAnomalyCount()
  {
    return $this->anomalyCount;
  }
  /**
   * Deployed Model ID.
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
   * A list of historical Stats and Anomalies generated for all Features.
   *
   * @param GoogleCloudAiplatformV1ModelMonitoringStatsAnomaliesFeatureHistoricStatsAnomalies[] $featureStats
   */
  public function setFeatureStats($featureStats)
  {
    $this->featureStats = $featureStats;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelMonitoringStatsAnomaliesFeatureHistoricStatsAnomalies[]
   */
  public function getFeatureStats()
  {
    return $this->featureStats;
  }
  /**
   * Model Monitoring Objective those stats and anomalies belonging to.
   *
   * Accepted values: MODEL_DEPLOYMENT_MONITORING_OBJECTIVE_TYPE_UNSPECIFIED,
   * RAW_FEATURE_SKEW, RAW_FEATURE_DRIFT, FEATURE_ATTRIBUTION_SKEW,
   * FEATURE_ATTRIBUTION_DRIFT
   *
   * @param self::OBJECTIVE_* $objective
   */
  public function setObjective($objective)
  {
    $this->objective = $objective;
  }
  /**
   * @return self::OBJECTIVE_*
   */
  public function getObjective()
  {
    return $this->objective;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelMonitoringStatsAnomalies::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelMonitoringStatsAnomalies');
