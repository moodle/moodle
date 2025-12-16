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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'confidenceMetrics';
  /**
   * The Area Under Precision-Recall Curve metric. Micro-averaged for the
   * overall evaluation.
   *
   * @var float
   */
  public $auPrc;
  /**
   * The Area Under Receiver Operating Characteristic curve metric. Micro-
   * averaged for the overall evaluation.
   *
   * @var float
   */
  public $auRoc;
  protected $confidenceMetricsType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetricsConfidenceMetrics::class;
  protected $confidenceMetricsDataType = 'array';
  protected $confusionMatrixType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix::class;
  protected $confusionMatrixDataType = '';
  /**
   * The Log Loss metric.
   *
   * @var float
   */
  public $logLoss;

  /**
   * The Area Under Precision-Recall Curve metric. Micro-averaged for the
   * overall evaluation.
   *
   * @param float $auPrc
   */
  public function setAuPrc($auPrc)
  {
    $this->auPrc = $auPrc;
  }
  /**
   * @return float
   */
  public function getAuPrc()
  {
    return $this->auPrc;
  }
  /**
   * The Area Under Receiver Operating Characteristic curve metric. Micro-
   * averaged for the overall evaluation.
   *
   * @param float $auRoc
   */
  public function setAuRoc($auRoc)
  {
    $this->auRoc = $auRoc;
  }
  /**
   * @return float
   */
  public function getAuRoc()
  {
    return $this->auRoc;
  }
  /**
   * Metrics for each `confidenceThreshold` in
   * 0.00,0.05,0.10,...,0.95,0.96,0.97,0.98,0.99 and `positionThreshold` =
   * INT32_MAX_VALUE. ROC and precision-recall curves, and other aggregated
   * metrics are derived from them. The confidence metrics entries may also be
   * supplied for additional values of `positionThreshold`, but from these no
   * aggregated metrics are computed.
   *
   * @param GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetricsConfidenceMetrics[] $confidenceMetrics
   */
  public function setConfidenceMetrics($confidenceMetrics)
  {
    $this->confidenceMetrics = $confidenceMetrics;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetricsConfidenceMetrics[]
   */
  public function getConfidenceMetrics()
  {
    return $this->confidenceMetrics;
  }
  /**
   * Confusion matrix of the evaluation.
   *
   * @param GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix $confusionMatrix
   */
  public function setConfusionMatrix(GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix $confusionMatrix)
  {
    $this->confusionMatrix = $confusionMatrix;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix
   */
  public function getConfusionMatrix()
  {
    return $this->confusionMatrix;
  }
  /**
   * The Log Loss metric.
   *
   * @param float $logLoss
   */
  public function setLogLoss($logLoss)
  {
    $this->logLoss = $logLoss;
  }
  /**
   * @return float
   */
  public function getLogLoss()
  {
    return $this->logLoss;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetrics');
