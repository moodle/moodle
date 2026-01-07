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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1ConfidenceMetricsEntry extends \Google\Model
{
  /**
   * Threshold used for this entry. For classification tasks, this is a
   * classification threshold: a predicted label is categorized as positive or
   * negative (in the context of this point on the PR curve) based on whether
   * the label's score meets this threshold. For image object detection
   * (bounding box) tasks, this is the [intersection-over-union
   * (IOU)](/vision/automl/object-detection/docs/evaluate#intersection-over-
   * union) threshold for the context of this point on the PR curve.
   *
   * @var float
   */
  public $confidenceThreshold;
  /**
   * Harmonic mean of recall and precision.
   *
   * @var float
   */
  public $f1Score;
  /**
   * The harmonic mean of recall_at1 and precision_at1.
   *
   * @var float
   */
  public $f1ScoreAt1;
  /**
   * The harmonic mean of recall_at5 and precision_at5.
   *
   * @var float
   */
  public $f1ScoreAt5;
  /**
   * Precision value.
   *
   * @var float
   */
  public $precision;
  /**
   * Precision value for entries with label that has highest score.
   *
   * @var float
   */
  public $precisionAt1;
  /**
   * Precision value for entries with label that has highest 5 scores.
   *
   * @var float
   */
  public $precisionAt5;
  /**
   * Recall value.
   *
   * @var float
   */
  public $recall;
  /**
   * Recall value for entries with label that has highest score.
   *
   * @var float
   */
  public $recallAt1;
  /**
   * Recall value for entries with label that has highest 5 scores.
   *
   * @var float
   */
  public $recallAt5;

  /**
   * Threshold used for this entry. For classification tasks, this is a
   * classification threshold: a predicted label is categorized as positive or
   * negative (in the context of this point on the PR curve) based on whether
   * the label's score meets this threshold. For image object detection
   * (bounding box) tasks, this is the [intersection-over-union
   * (IOU)](/vision/automl/object-detection/docs/evaluate#intersection-over-
   * union) threshold for the context of this point on the PR curve.
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
   * Harmonic mean of recall and precision.
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
   * The harmonic mean of recall_at1 and precision_at1.
   *
   * @param float $f1ScoreAt1
   */
  public function setF1ScoreAt1($f1ScoreAt1)
  {
    $this->f1ScoreAt1 = $f1ScoreAt1;
  }
  /**
   * @return float
   */
  public function getF1ScoreAt1()
  {
    return $this->f1ScoreAt1;
  }
  /**
   * The harmonic mean of recall_at5 and precision_at5.
   *
   * @param float $f1ScoreAt5
   */
  public function setF1ScoreAt5($f1ScoreAt5)
  {
    $this->f1ScoreAt5 = $f1ScoreAt5;
  }
  /**
   * @return float
   */
  public function getF1ScoreAt5()
  {
    return $this->f1ScoreAt5;
  }
  /**
   * Precision value.
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
   * Precision value for entries with label that has highest score.
   *
   * @param float $precisionAt1
   */
  public function setPrecisionAt1($precisionAt1)
  {
    $this->precisionAt1 = $precisionAt1;
  }
  /**
   * @return float
   */
  public function getPrecisionAt1()
  {
    return $this->precisionAt1;
  }
  /**
   * Precision value for entries with label that has highest 5 scores.
   *
   * @param float $precisionAt5
   */
  public function setPrecisionAt5($precisionAt5)
  {
    $this->precisionAt5 = $precisionAt5;
  }
  /**
   * @return float
   */
  public function getPrecisionAt5()
  {
    return $this->precisionAt5;
  }
  /**
   * Recall value.
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
  /**
   * Recall value for entries with label that has highest score.
   *
   * @param float $recallAt1
   */
  public function setRecallAt1($recallAt1)
  {
    $this->recallAt1 = $recallAt1;
  }
  /**
   * @return float
   */
  public function getRecallAt1()
  {
    return $this->recallAt1;
  }
  /**
   * Recall value for entries with label that has highest 5 scores.
   *
   * @param float $recallAt5
   */
  public function setRecallAt5($recallAt5)
  {
    $this->recallAt5 = $recallAt5;
  }
  /**
   * @return float
   */
  public function getRecallAt5()
  {
    return $this->recallAt5;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatalabelingV1beta1ConfidenceMetricsEntry::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1ConfidenceMetricsEntry');
