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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetricsConfidenceMetrics extends \Google\Model
{
  /**
   * Metrics are computed with an assumption that the Model never returns
   * predictions with score lower than this value.
   *
   * @var float
   */
  public $confidenceThreshold;
  protected $confusionMatrixType = GoogleCloudAiplatformV1SchemaModelevaluationMetricsConfusionMatrix::class;
  protected $confusionMatrixDataType = '';
  /**
   * The harmonic mean of recall and precision. For summary metrics, it computes
   * the micro-averaged F1 score.
   *
   * @var float
   */
  public $f1Score;
  /**
   * The harmonic mean of recallAt1 and precisionAt1.
   *
   * @var float
   */
  public $f1ScoreAt1;
  /**
   * Macro-averaged F1 Score.
   *
   * @var float
   */
  public $f1ScoreMacro;
  /**
   * Micro-averaged F1 Score.
   *
   * @var float
   */
  public $f1ScoreMicro;
  /**
   * The number of ground truth labels that are not matched by a Model created
   * label.
   *
   * @var string
   */
  public $falseNegativeCount;
  /**
   * The number of Model created labels that do not match a ground truth label.
   *
   * @var string
   */
  public $falsePositiveCount;
  /**
   * False Positive Rate for the given confidence threshold.
   *
   * @var float
   */
  public $falsePositiveRate;
  /**
   * The False Positive Rate when only considering the label that has the
   * highest prediction score and not below the confidence threshold for each
   * DataItem.
   *
   * @var float
   */
  public $falsePositiveRateAt1;
  /**
   * Metrics are computed with an assumption that the Model always returns at
   * most this many predictions (ordered by their score, descendingly), but they
   * all still need to meet the `confidenceThreshold`.
   *
   * @var int
   */
  public $maxPredictions;
  /**
   * Precision for the given confidence threshold.
   *
   * @var float
   */
  public $precision;
  /**
   * The precision when only considering the label that has the highest
   * prediction score and not below the confidence threshold for each DataItem.
   *
   * @var float
   */
  public $precisionAt1;
  /**
   * Recall (True Positive Rate) for the given confidence threshold.
   *
   * @var float
   */
  public $recall;
  /**
   * The Recall (True Positive Rate) when only considering the label that has
   * the highest prediction score and not below the confidence threshold for
   * each DataItem.
   *
   * @var float
   */
  public $recallAt1;
  /**
   * The number of labels that were not created by the Model, but if they would,
   * they would not match a ground truth label.
   *
   * @var string
   */
  public $trueNegativeCount;
  /**
   * The number of Model created labels that match a ground truth label.
   *
   * @var string
   */
  public $truePositiveCount;

  /**
   * Metrics are computed with an assumption that the Model never returns
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
   * Confusion matrix of the evaluation for this confidence_threshold.
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
   * The harmonic mean of recall and precision. For summary metrics, it computes
   * the micro-averaged F1 score.
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
   * The harmonic mean of recallAt1 and precisionAt1.
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
   * Macro-averaged F1 Score.
   *
   * @param float $f1ScoreMacro
   */
  public function setF1ScoreMacro($f1ScoreMacro)
  {
    $this->f1ScoreMacro = $f1ScoreMacro;
  }
  /**
   * @return float
   */
  public function getF1ScoreMacro()
  {
    return $this->f1ScoreMacro;
  }
  /**
   * Micro-averaged F1 Score.
   *
   * @param float $f1ScoreMicro
   */
  public function setF1ScoreMicro($f1ScoreMicro)
  {
    $this->f1ScoreMicro = $f1ScoreMicro;
  }
  /**
   * @return float
   */
  public function getF1ScoreMicro()
  {
    return $this->f1ScoreMicro;
  }
  /**
   * The number of ground truth labels that are not matched by a Model created
   * label.
   *
   * @param string $falseNegativeCount
   */
  public function setFalseNegativeCount($falseNegativeCount)
  {
    $this->falseNegativeCount = $falseNegativeCount;
  }
  /**
   * @return string
   */
  public function getFalseNegativeCount()
  {
    return $this->falseNegativeCount;
  }
  /**
   * The number of Model created labels that do not match a ground truth label.
   *
   * @param string $falsePositiveCount
   */
  public function setFalsePositiveCount($falsePositiveCount)
  {
    $this->falsePositiveCount = $falsePositiveCount;
  }
  /**
   * @return string
   */
  public function getFalsePositiveCount()
  {
    return $this->falsePositiveCount;
  }
  /**
   * False Positive Rate for the given confidence threshold.
   *
   * @param float $falsePositiveRate
   */
  public function setFalsePositiveRate($falsePositiveRate)
  {
    $this->falsePositiveRate = $falsePositiveRate;
  }
  /**
   * @return float
   */
  public function getFalsePositiveRate()
  {
    return $this->falsePositiveRate;
  }
  /**
   * The False Positive Rate when only considering the label that has the
   * highest prediction score and not below the confidence threshold for each
   * DataItem.
   *
   * @param float $falsePositiveRateAt1
   */
  public function setFalsePositiveRateAt1($falsePositiveRateAt1)
  {
    $this->falsePositiveRateAt1 = $falsePositiveRateAt1;
  }
  /**
   * @return float
   */
  public function getFalsePositiveRateAt1()
  {
    return $this->falsePositiveRateAt1;
  }
  /**
   * Metrics are computed with an assumption that the Model always returns at
   * most this many predictions (ordered by their score, descendingly), but they
   * all still need to meet the `confidenceThreshold`.
   *
   * @param int $maxPredictions
   */
  public function setMaxPredictions($maxPredictions)
  {
    $this->maxPredictions = $maxPredictions;
  }
  /**
   * @return int
   */
  public function getMaxPredictions()
  {
    return $this->maxPredictions;
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
   * The precision when only considering the label that has the highest
   * prediction score and not below the confidence threshold for each DataItem.
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
  /**
   * The Recall (True Positive Rate) when only considering the label that has
   * the highest prediction score and not below the confidence threshold for
   * each DataItem.
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
   * The number of labels that were not created by the Model, but if they would,
   * they would not match a ground truth label.
   *
   * @param string $trueNegativeCount
   */
  public function setTrueNegativeCount($trueNegativeCount)
  {
    $this->trueNegativeCount = $trueNegativeCount;
  }
  /**
   * @return string
   */
  public function getTrueNegativeCount()
  {
    return $this->trueNegativeCount;
  }
  /**
   * The number of Model created labels that match a ground truth label.
   *
   * @param string $truePositiveCount
   */
  public function setTruePositiveCount($truePositiveCount)
  {
    $this->truePositiveCount = $truePositiveCount;
  }
  /**
   * @return string
   */
  public function getTruePositiveCount()
  {
    return $this->truePositiveCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetricsConfidenceMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsClassificationEvaluationMetricsConfidenceMetrics');
