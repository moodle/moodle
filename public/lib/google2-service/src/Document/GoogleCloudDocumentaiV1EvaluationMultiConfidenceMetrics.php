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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics extends \Google\Collection
{
  /**
   * The metrics type is unspecified. By default, metrics without a particular
   * specification are for leaf entity types (i.e., top-level entity types
   * without child types, or child types which are not parent types themselves).
   */
  public const METRICS_TYPE_METRICS_TYPE_UNSPECIFIED = 'METRICS_TYPE_UNSPECIFIED';
  /**
   * Indicates whether metrics for this particular label type represent an
   * aggregate of metrics for other types instead of being based on actual
   * TP/FP/FN values for the label type. Metrics for parent (i.e., non-leaf)
   * entity types are an aggregate of metrics for their children.
   */
  public const METRICS_TYPE_AGGREGATE = 'AGGREGATE';
  protected $collection_key = 'confidenceLevelMetricsExact';
  /**
   * The calculated area under the precision recall curve (AUPRC), computed by
   * integrating over all confidence thresholds.
   *
   * @var float
   */
  public $auprc;
  /**
   * The AUPRC for metrics with fuzzy matching disabled, i.e., exact matching
   * only.
   *
   * @var float
   */
  public $auprcExact;
  protected $confidenceLevelMetricsType = GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics::class;
  protected $confidenceLevelMetricsDataType = 'array';
  protected $confidenceLevelMetricsExactType = GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics::class;
  protected $confidenceLevelMetricsExactDataType = 'array';
  /**
   * The Estimated Calibration Error (ECE) of the confidence of the predicted
   * entities.
   *
   * @var float
   */
  public $estimatedCalibrationError;
  /**
   * The ECE for the predicted entities with fuzzy matching disabled, i.e.,
   * exact matching only.
   *
   * @var float
   */
  public $estimatedCalibrationErrorExact;
  /**
   * The metrics type for the label.
   *
   * @var string
   */
  public $metricsType;

  /**
   * The calculated area under the precision recall curve (AUPRC), computed by
   * integrating over all confidence thresholds.
   *
   * @param float $auprc
   */
  public function setAuprc($auprc)
  {
    $this->auprc = $auprc;
  }
  /**
   * @return float
   */
  public function getAuprc()
  {
    return $this->auprc;
  }
  /**
   * The AUPRC for metrics with fuzzy matching disabled, i.e., exact matching
   * only.
   *
   * @param float $auprcExact
   */
  public function setAuprcExact($auprcExact)
  {
    $this->auprcExact = $auprcExact;
  }
  /**
   * @return float
   */
  public function getAuprcExact()
  {
    return $this->auprcExact;
  }
  /**
   * Metrics across confidence levels with fuzzy matching enabled.
   *
   * @param GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics[] $confidenceLevelMetrics
   */
  public function setConfidenceLevelMetrics($confidenceLevelMetrics)
  {
    $this->confidenceLevelMetrics = $confidenceLevelMetrics;
  }
  /**
   * @return GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics[]
   */
  public function getConfidenceLevelMetrics()
  {
    return $this->confidenceLevelMetrics;
  }
  /**
   * Metrics across confidence levels with only exact matching.
   *
   * @param GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics[] $confidenceLevelMetricsExact
   */
  public function setConfidenceLevelMetricsExact($confidenceLevelMetricsExact)
  {
    $this->confidenceLevelMetricsExact = $confidenceLevelMetricsExact;
  }
  /**
   * @return GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics[]
   */
  public function getConfidenceLevelMetricsExact()
  {
    return $this->confidenceLevelMetricsExact;
  }
  /**
   * The Estimated Calibration Error (ECE) of the confidence of the predicted
   * entities.
   *
   * @param float $estimatedCalibrationError
   */
  public function setEstimatedCalibrationError($estimatedCalibrationError)
  {
    $this->estimatedCalibrationError = $estimatedCalibrationError;
  }
  /**
   * @return float
   */
  public function getEstimatedCalibrationError()
  {
    return $this->estimatedCalibrationError;
  }
  /**
   * The ECE for the predicted entities with fuzzy matching disabled, i.e.,
   * exact matching only.
   *
   * @param float $estimatedCalibrationErrorExact
   */
  public function setEstimatedCalibrationErrorExact($estimatedCalibrationErrorExact)
  {
    $this->estimatedCalibrationErrorExact = $estimatedCalibrationErrorExact;
  }
  /**
   * @return float
   */
  public function getEstimatedCalibrationErrorExact()
  {
    return $this->estimatedCalibrationErrorExact;
  }
  /**
   * The metrics type for the label.
   *
   * Accepted values: METRICS_TYPE_UNSPECIFIED, AGGREGATE
   *
   * @param self::METRICS_TYPE_* $metricsType
   */
  public function setMetricsType($metricsType)
  {
    $this->metricsType = $metricsType;
  }
  /**
   * @return self::METRICS_TYPE_*
   */
  public function getMetricsType()
  {
    return $this->metricsType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics::class, 'Google_Service_Document_GoogleCloudDocumentaiV1EvaluationMultiConfidenceMetrics');
