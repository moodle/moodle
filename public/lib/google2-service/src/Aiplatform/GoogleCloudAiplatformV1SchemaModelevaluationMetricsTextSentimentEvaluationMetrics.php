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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextSentimentEvaluationMetrics extends \Google\Model
{
  protected $confusionMatrixType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix::class;
  protected $confusionMatrixDataType = '';
  /**
   * The harmonic mean of recall and precision.
   *
   * @var float
   */
  public $f1Score;
  /**
   * Linear weighted kappa. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
   *
   * @var float
   */
  public $linearKappa;
  /**
   * Mean absolute error. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
   *
   * @var float
   */
  public $meanAbsoluteError;
  /**
   * Mean squared error. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
   *
   * @var float
   */
  public $meanSquaredError;
  /**
   * Precision.
   *
   * @var float
   */
  public $precision;
  /**
   * Quadratic weighted kappa. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
   *
   * @var float
   */
  public $quadraticKappa;
  /**
   * Recall.
   *
   * @var float
   */
  public $recall;

  /**
   * Confusion matrix of the evaluation. Only set for ModelEvaluations, not for
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
  /**
   * The harmonic mean of recall and precision.
   *
   * @param float $f1Score
   */
  public function setF1Score($f1Score)
  {
    $this->f1Score = $f1Score;
  }
  /**
   * @return float
   */
  public function getF1Score()
  {
    return $this->f1Score;
  }
  /**
   * Linear weighted kappa. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
   *
   * @param float $linearKappa
   */
  public function setLinearKappa($linearKappa)
  {
    $this->linearKappa = $linearKappa;
  }
  /**
   * @return float
   */
  public function getLinearKappa()
  {
    return $this->linearKappa;
  }
  /**
   * Mean absolute error. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
   *
   * @param float $meanAbsoluteError
   */
  public function setMeanAbsoluteError($meanAbsoluteError)
  {
    $this->meanAbsoluteError = $meanAbsoluteError;
  }
  /**
   * @return float
   */
  public function getMeanAbsoluteError()
  {
    return $this->meanAbsoluteError;
  }
  /**
   * Mean squared error. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
   *
   * @param float $meanSquaredError
   */
  public function setMeanSquaredError($meanSquaredError)
  {
    $this->meanSquaredError = $meanSquaredError;
  }
  /**
   * @return float
   */
  public function getMeanSquaredError()
  {
    return $this->meanSquaredError;
  }
  /**
   * Precision.
   *
   * @param float $precision
   */
  public function setPrecision($precision)
  {
    $this->precision = $precision;
  }
  /**
   * @return float
   */
  public function getPrecision()
  {
    return $this->precision;
  }
  /**
   * Quadratic weighted kappa. Only set for ModelEvaluations, not for
   * ModelEvaluationSlices.
   *
   * @param float $quadraticKappa
   */
  public function setQuadraticKappa($quadraticKappa)
  {
    $this->quadraticKappa = $quadraticKappa;
  }
  /**
   * @return float
   */
  public function getQuadraticKappa()
  {
    return $this->quadraticKappa;
  }
  /**
   * Recall.
   *
   * @param float $recall
   */
  public function setRecall($recall)
  {
    $this->recall = $recall;
  }
  /**
   * @return float
   */
  public function getRecall()
  {
    return $this->recall;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextSentimentEvaluationMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsTextSentimentEvaluationMetrics');
