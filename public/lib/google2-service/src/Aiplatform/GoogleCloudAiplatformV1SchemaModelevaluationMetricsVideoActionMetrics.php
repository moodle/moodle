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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoActionMetrics extends \Google\Collection
{
  protected $collection_key = 'confidenceMetrics';
  protected $confidenceMetricsType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoActionMetricsConfidenceMetrics::class;
  protected $confidenceMetricsDataType = 'array';
  /**
   * The mean average precision.
   *
   * @var float
   */
  public $meanAveragePrecision;
  /**
   * This VideoActionMetrics is calculated based on this prediction window
   * length. If the predicted action's timestamp is inside the time window whose
   * center is the ground truth action's timestamp with this specific length,
   * the prediction result is treated as a true positive.
   *
   * @var string
   */
  public $precisionWindowLength;

  /**
   * Metrics for each label-match confidence_threshold from
   * 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99.
   *
   * @param GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoActionMetricsConfidenceMetrics[] $confidenceMetrics
   */
  public function setConfidenceMetrics($confidenceMetrics)
  {
    $this->confidenceMetrics = $confidenceMetrics;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoActionMetricsConfidenceMetrics[]
   */
  public function getConfidenceMetrics()
  {
    return $this->confidenceMetrics;
  }
  /**
   * The mean average precision.
   *
   * @param float $meanAveragePrecision
   */
  public function setMeanAveragePrecision($meanAveragePrecision)
  {
    $this->meanAveragePrecision = $meanAveragePrecision;
  }
  /**
   * @return float
   */
  public function getMeanAveragePrecision()
  {
    return $this->meanAveragePrecision;
  }
  /**
   * This VideoActionMetrics is calculated based on this prediction window
   * length. If the predicted action's timestamp is inside the time window whose
   * center is the ground truth action's timestamp with this specific length,
   * the prediction result is treated as a true positive.
   *
   * @param string $precisionWindowLength
   */
  public function setPrecisionWindowLength($precisionWindowLength)
  {
    $this->precisionWindowLength = $precisionWindowLength;
  }
  /**
   * @return string
   */
  public function getPrecisionWindowLength()
  {
    return $this->precisionWindowLength;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoActionMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsVideoActionMetrics');
