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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsPairwiseTextGenerationEvaluationMetrics extends \Google\Model
{
  /**
   * Fraction of cases where the autorater agreed with the human raters.
   *
   * @var float
   */
  public $accuracy;
  /**
   * Percentage of time the autorater decided the baseline model had the better
   * response.
   *
   * @var float
   */
  public $baselineModelWinRate;
  /**
   * A measurement of agreement between the autorater and human raters that
   * takes the likelihood of random agreement into account.
   *
   * @var float
   */
  public $cohensKappa;
  /**
   * Harmonic mean of precision and recall.
   *
   * @var float
   */
  public $f1Score;
  /**
   * Number of examples where the autorater chose the baseline model, but humans
   * preferred the model.
   *
   * @var string
   */
  public $falseNegativeCount;
  /**
   * Number of examples where the autorater chose the model, but humans
   * preferred the baseline model.
   *
   * @var string
   */
  public $falsePositiveCount;
  /**
   * Percentage of time humans decided the baseline model had the better
   * response.
   *
   * @var float
   */
  public $humanPreferenceBaselineModelWinRate;
  /**
   * Percentage of time humans decided the model had the better response.
   *
   * @var float
   */
  public $humanPreferenceModelWinRate;
  /**
   * Percentage of time the autorater decided the model had the better response.
   *
   * @var float
   */
  public $modelWinRate;
  /**
   * Fraction of cases where the autorater and humans thought the model had a
   * better response out of all cases where the autorater thought the model had
   * a better response. True positive divided by all positive.
   *
   * @var float
   */
  public $precision;
  /**
   * Fraction of cases where the autorater and humans thought the model had a
   * better response out of all cases where the humans thought the model had a
   * better response.
   *
   * @var float
   */
  public $recall;
  /**
   * Number of examples where both the autorater and humans decided that the
   * model had the worse response.
   *
   * @var string
   */
  public $trueNegativeCount;
  /**
   * Number of examples where both the autorater and humans decided that the
   * model had the better response.
   *
   * @var string
   */
  public $truePositiveCount;

  /**
   * Fraction of cases where the autorater agreed with the human raters.
   *
   * @param float $accuracy
   */
  public function setAccuracy($accuracy)
  {
    $this->accuracy = $accuracy;
  }
  /**
   * @return float
   */
  public function getAccuracy()
  {
    return $this->accuracy;
  }
  /**
   * Percentage of time the autorater decided the baseline model had the better
   * response.
   *
   * @param float $baselineModelWinRate
   */
  public function setBaselineModelWinRate($baselineModelWinRate)
  {
    $this->baselineModelWinRate = $baselineModelWinRate;
  }
  /**
   * @return float
   */
  public function getBaselineModelWinRate()
  {
    return $this->baselineModelWinRate;
  }
  /**
   * A measurement of agreement between the autorater and human raters that
   * takes the likelihood of random agreement into account.
   *
   * @param float $cohensKappa
   */
  public function setCohensKappa($cohensKappa)
  {
    $this->cohensKappa = $cohensKappa;
  }
  /**
   * @return float
   */
  public function getCohensKappa()
  {
    return $this->cohensKappa;
  }
  /**
   * Harmonic mean of precision and recall.
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
   * Number of examples where the autorater chose the baseline model, but humans
   * preferred the model.
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
   * Number of examples where the autorater chose the model, but humans
   * preferred the baseline model.
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
   * Percentage of time humans decided the baseline model had the better
   * response.
   *
   * @param float $humanPreferenceBaselineModelWinRate
   */
  public function setHumanPreferenceBaselineModelWinRate($humanPreferenceBaselineModelWinRate)
  {
    $this->humanPreferenceBaselineModelWinRate = $humanPreferenceBaselineModelWinRate;
  }
  /**
   * @return float
   */
  public function getHumanPreferenceBaselineModelWinRate()
  {
    return $this->humanPreferenceBaselineModelWinRate;
  }
  /**
   * Percentage of time humans decided the model had the better response.
   *
   * @param float $humanPreferenceModelWinRate
   */
  public function setHumanPreferenceModelWinRate($humanPreferenceModelWinRate)
  {
    $this->humanPreferenceModelWinRate = $humanPreferenceModelWinRate;
  }
  /**
   * @return float
   */
  public function getHumanPreferenceModelWinRate()
  {
    return $this->humanPreferenceModelWinRate;
  }
  /**
   * Percentage of time the autorater decided the model had the better response.
   *
   * @param float $modelWinRate
   */
  public function setModelWinRate($modelWinRate)
  {
    $this->modelWinRate = $modelWinRate;
  }
  /**
   * @return float
   */
  public function getModelWinRate()
  {
    return $this->modelWinRate;
  }
  /**
   * Fraction of cases where the autorater and humans thought the model had a
   * better response out of all cases where the autorater thought the model had
   * a better response. True positive divided by all positive.
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
   * Fraction of cases where the autorater and humans thought the model had a
   * better response out of all cases where the humans thought the model had a
   * better response.
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
   * Number of examples where both the autorater and humans decided that the
   * model had the worse response.
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
   * Number of examples where both the autorater and humans decided that the
   * model had the better response.
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
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsPairwiseTextGenerationEvaluationMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsPairwiseTextGenerationEvaluationMetrics');
