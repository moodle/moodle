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

namespace Google\Service\CloudNaturalLanguage;

class XPSImageSegmentationEvaluationMetricsConfidenceMetricsEntry extends \Google\Model
{
  /**
   * The confidence threshold value used to compute the metrics.
   *
   * @var float
   */
  public $confidenceThreshold;
  protected $confusionMatrixType = XPSConfusionMatrix::class;
  protected $confusionMatrixDataType = '';
  /**
   * DSC or the F1 score: The harmonic mean of recall and precision.
   *
   * @var float
   */
  public $diceScoreCoefficient;
  /**
   * IOU score.
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
   * Recall for the given confidence threshold.
   *
   * @var float
   */
  public $recall;

  /**
   * The confidence threshold value used to compute the metrics.
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
   * Confusion matrix of the per confidence_threshold evaluation. Pixel counts
   * are set here. Only set for model level evaluation, not for evaluation per
   * label.
   *
   * @param XPSConfusionMatrix $confusionMatrix
   */
  public function setConfusionMatrix(XPSConfusionMatrix $confusionMatrix)
  {
    $this->confusionMatrix = $confusionMatrix;
  }
  /**
   * @return XPSConfusionMatrix
   */
  public function getConfusionMatrix()
  {
    return $this->confusionMatrix;
  }
  /**
   * DSC or the F1 score: The harmonic mean of recall and precision.
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
   * IOU score.
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
   * Recall for the given confidence threshold.
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
class_alias(XPSImageSegmentationEvaluationMetricsConfidenceMetricsEntry::class, 'Google_Service_CloudNaturalLanguage_XPSImageSegmentationEvaluationMetricsConfidenceMetricsEntry');
