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

class GoogleCloudAiplatformV1FeatureStatsAnomaly extends \Google\Model
{
  /**
   * This is the threshold used when detecting anomalies. The threshold can be
   * changed by user, so this one might be different from ThresholdConfig.value.
   *
   * @var 
   */
  public $anomalyDetectionThreshold;
  /**
   * Path of the anomaly file for current feature values in Cloud Storage
   * bucket. Format: gs:/anomalies. Example:
   * gs://monitoring_bucket/feature_name/anomalies. Stats are stored as binary
   * format with Protobuf message Anoamlies are stored as binary format with
   * Protobuf message [tensorflow.metadata.v0.AnomalyInfo] (https://github.com/t
   * ensorflow/metadata/blob/master/tensorflow_metadata/proto/v0/anomalies.proto
   * ).
   *
   * @var string
   */
  public $anomalyUri;
  /**
   * Deviation from the current stats to baseline stats. 1. For categorical
   * feature, the distribution distance is calculated by L-inifinity norm. 2.
   * For numerical feature, the distribution distance is calculated by
   * Jensen–Shannon divergence.
   *
   * @var 
   */
  public $distributionDeviation;
  /**
   * The end timestamp of window where stats were generated. For objectives
   * where time window doesn't make sense (e.g. Featurestore Snapshot
   * Monitoring), end_time indicates the timestamp of the data used to generate
   * stats (e.g. timestamp we take snapshots for feature values).
   *
   * @var string
   */
  public $endTime;
  /**
   * Feature importance score, only populated when cross-feature monitoring is
   * enabled. For now only used to represent feature attribution score within
   * range [0, 1] for
   * ModelDeploymentMonitoringObjectiveType.FEATURE_ATTRIBUTION_SKEW and
   * ModelDeploymentMonitoringObjectiveType.FEATURE_ATTRIBUTION_DRIFT.
   *
   * @var 
   */
  public $score;
  /**
   * The start timestamp of window where stats were generated. For objectives
   * where time window doesn't make sense (e.g. Featurestore Snapshot
   * Monitoring), start_time is only used to indicate the monitoring intervals,
   * so it always equals to (end_time - monitoring_interval).
   *
   * @var string
   */
  public $startTime;
  /**
   * Path of the stats file for current feature values in Cloud Storage bucket.
   * Format: gs:/stats. Example: gs://monitoring_bucket/feature_name/stats.
   * Stats are stored as binary format with Protobuf message [tensorflow.metadat
   * a.v0.FeatureNameStatistics](https://github.com/tensorflow/metadata/blob/mas
   * ter/tensorflow_metadata/proto/v0/statistics.proto).
   *
   * @var string
   */
  public $statsUri;

  public function setAnomalyDetectionThreshold($anomalyDetectionThreshold)
  {
    $this->anomalyDetectionThreshold = $anomalyDetectionThreshold;
  }
  public function getAnomalyDetectionThreshold()
  {
    return $this->anomalyDetectionThreshold;
  }
  /**
   * Path of the anomaly file for current feature values in Cloud Storage
   * bucket. Format: gs:/anomalies. Example:
   * gs://monitoring_bucket/feature_name/anomalies. Stats are stored as binary
   * format with Protobuf message Anoamlies are stored as binary format with
   * Protobuf message [tensorflow.metadata.v0.AnomalyInfo] (https://github.com/t
   * ensorflow/metadata/blob/master/tensorflow_metadata/proto/v0/anomalies.proto
   * ).
   *
   * @param string $anomalyUri
   */
  public function setAnomalyUri($anomalyUri)
  {
    $this->anomalyUri = $anomalyUri;
  }
  /**
   * @return string
   */
  public function getAnomalyUri()
  {
    return $this->anomalyUri;
  }
  public function setDistributionDeviation($distributionDeviation)
  {
    $this->distributionDeviation = $distributionDeviation;
  }
  public function getDistributionDeviation()
  {
    return $this->distributionDeviation;
  }
  /**
   * The end timestamp of window where stats were generated. For objectives
   * where time window doesn't make sense (e.g. Featurestore Snapshot
   * Monitoring), end_time indicates the timestamp of the data used to generate
   * stats (e.g. timestamp we take snapshots for feature values).
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
  public function setScore($score)
  {
    $this->score = $score;
  }
  public function getScore()
  {
    return $this->score;
  }
  /**
   * The start timestamp of window where stats were generated. For objectives
   * where time window doesn't make sense (e.g. Featurestore Snapshot
   * Monitoring), start_time is only used to indicate the monitoring intervals,
   * so it always equals to (end_time - monitoring_interval).
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
  /**
   * Path of the stats file for current feature values in Cloud Storage bucket.
   * Format: gs:/stats. Example: gs://monitoring_bucket/feature_name/stats.
   * Stats are stored as binary format with Protobuf message [tensorflow.metadat
   * a.v0.FeatureNameStatistics](https://github.com/tensorflow/metadata/blob/mas
   * ter/tensorflow_metadata/proto/v0/statistics.proto).
   *
   * @param string $statsUri
   */
  public function setStatsUri($statsUri)
  {
    $this->statsUri = $statsUri;
  }
  /**
   * @return string
   */
  public function getStatsUri()
  {
    return $this->statsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureStatsAnomaly::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureStatsAnomaly');
