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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsImageSegmentationEvaluationMetricsConfidenceMetricsEntry extends \Google\Model
{
  /**
   * Metrics are computed with an assumption that the model never returns
   * predictions with score lower than this value.
   *
   * @var float
   */
  public $confidenceThreshold;
  protected $confusionMatrixType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix::class;
  protected $confusionMatrixDataType = '';
  /**
   * DSC or the F1 score, The harmonic mean of recall and precision.
   *
   * @var float
   */
  public $diceScoreCoefficient;
  /**
   * The intersection-over-union score. The measure of overlap of the
   * annotation's category mask with ground truth category mask on the DataItem.
   *
   * @var float
   */
  public $iouScore;
  /**
   * Precision for the given confidence threshold.
   *
   * @var float
   */
  public $precision;
  /**
   * Recall (True Positive Rate) for the given confidence threshold.
   *
   * @var float
   */
  public $recall;

  /**
   * Metrics are computed with an assumption that the model never returns
   * predictions with score lower than this value.
   *
   * @param float $confidenceThreshold
   */
  public function setConfidenceThreshold($confidenceThreshold)
  {
    $this->confidenceThreshold = $confidenceThreshold;
  }
  /**
   * @return float
   */
  public function getConfidenceThreshold()
  {
    return $this->confidenceThreshold;
  }
  /**
   * Confusion matrix for the given confidence threshold.
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
   * DSC or the F1 score, The harmonic mean of recall and precision.
   *
   * @param float $diceScoreCoefficient
   */
  public function setDiceScoreCoefficient($diceScoreCoefficient)
  {
    $this->diceScoreCoefficient = $diceScoreCoefficient;
  }
  /**
   * @return float
   */
  public function getDiceScoreCoefficient()
  {
    return $this->diceScoreCoefficient;
  }
  /**
   * The intersection-over-union score. The measure of overlap of the
   * annotation's category mask with ground truth category mask on the DataItem.
   *
   * @param float $iouScore
   */
  public function setIouScore($iouScore)
  {
    $this->iouScore = $iouScore;
  }
  /**
   * @return float
   */
  public function getIouScore()
  {
    return $this->iouScore;
  }
  /**
   * Precision for the given confidence threshold.
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
   * Recall (True Positive Rate) for the given confidence threshold.
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
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsImageSegmentationEvaluationMetricsConfidenceMetricsEntry::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsImageSegmentationEvaluationMetricsConfidenceMetricsEntry');
