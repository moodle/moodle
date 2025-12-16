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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextExtractionEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'confidenceMetrics';
  protected $confidenceMetricsType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextExtractionEvaluationMetricsConfidenceMetrics::class;
  protected $confidenceMetricsDataType = 'array';
  protected $confusionMatrixType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix::class;
  protected $confusionMatrixDataType = '';

  /**
   * Metrics that have confidence thresholds. Precision-recall curve can be
   * derived from them.
   *
   * @param GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextExtractionEvaluationMetricsConfidenceMetrics[] $confidenceMetrics
   */
  public function setConfidenceMetrics($confidenceMetrics)
  {
    $this->confidenceMetrics = $confidenceMetrics;
  }
  /**
   * @return GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextExtractionEvaluationMetricsConfidenceMetrics[]
   */
  public function getConfidenceMetrics()
  {
    return $this->confidenceMetrics;
  }
  /**
   * Confusion matrix of the evaluation. Only set for Models where number of
   * AnnotationSpecs is no more than 10. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextExtractionEvaluationMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextExtractionEvaluationMetrics');
