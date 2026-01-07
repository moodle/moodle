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

class GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigPredictionDriftDetectionConfig extends \Google\Model
{
  protected $attributionScoreDriftThresholdsType = GoogleCloudAiplatformV1ThresholdConfig::class;
  protected $attributionScoreDriftThresholdsDataType = 'map';
  protected $defaultDriftThresholdType = GoogleCloudAiplatformV1ThresholdConfig::class;
  protected $defaultDriftThresholdDataType = '';
  protected $driftThresholdsType = GoogleCloudAiplatformV1ThresholdConfig::class;
  protected $driftThresholdsDataType = 'map';

  /**
   * Key is the feature name and value is the threshold. The threshold here is
   * against attribution score distance between different time windows.
   *
   * @param GoogleCloudAiplatformV1ThresholdConfig[] $attributionScoreDriftThresholds
   */
  public function setAttributionScoreDriftThresholds($attributionScoreDriftThresholds)
  {
    $this->attributionScoreDriftThresholds = $attributionScoreDriftThresholds;
  }
  /**
   * @return GoogleCloudAiplatformV1ThresholdConfig[]
   */
  public function getAttributionScoreDriftThresholds()
  {
    return $this->attributionScoreDriftThresholds;
  }
  /**
   * Drift anomaly detection threshold used by all features. When the per-
   * feature thresholds are not set, this field can be used to specify a
   * threshold for all features.
   *
   * @param GoogleCloudAiplatformV1ThresholdConfig $defaultDriftThreshold
   */
  public function setDefaultDriftThreshold(GoogleCloudAiplatformV1ThresholdConfig $defaultDriftThreshold)
  {
    $this->defaultDriftThreshold = $defaultDriftThreshold;
  }
  /**
   * @return GoogleCloudAiplatformV1ThresholdConfig
   */
  public function getDefaultDriftThreshold()
  {
    return $this->defaultDriftThreshold;
  }
  /**
   * Key is the feature name and value is the threshold. If a feature needs to
   * be monitored for drift, a value threshold must be configured for that
   * feature. The threshold here is against feature distribution distance
   * between different time windws.
   *
   * @param GoogleCloudAiplatformV1ThresholdConfig[] $driftThresholds
   */
  public function setDriftThresholds($driftThresholds)
  {
    $this->driftThresholds = $driftThresholds;
  }
  /**
   * @return GoogleCloudAiplatformV1ThresholdConfig[]
   */
  public function getDriftThresholds()
  {
    return $this->driftThresholds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigPredictionDriftDetectionConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigPredictionDriftDetectionConfig');
