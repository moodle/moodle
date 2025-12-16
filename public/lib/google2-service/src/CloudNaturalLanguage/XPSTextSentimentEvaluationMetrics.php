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

class XPSTextSentimentEvaluationMetrics extends \Google\Model
{
  protected $confusionMatrixType = XPSConfusionMatrix::class;
  protected $confusionMatrixDataType = '';
  /**
   * Output only. The harmonic mean of recall and precision.
   *
   * @var float
   */
  public $f1Score;
  /**
   * Output only. Linear weighted kappa. Only set for the overall model
   * evaluation, not for evaluation of a single annotation spec.
   *
   * @var float
   */
  public $linearKappa;
  /**
   * Output only. Mean absolute error. Only set for the overall model
   * evaluation, not for evaluation of a single annotation spec.
   *
   * @var float
   */
  public $meanAbsoluteError;
  /**
   * Output only. Mean squared error. Only set for the overall model evaluation,
   * not for evaluation of a single annotation spec.
   *
   * @var float
   */
  public $meanSquaredError;
  /**
   * Output only. Precision.
   *
   * @var float
   */
  public $precision;
  /**
   * Output only. Quadratic weighted kappa. Only set for the overall model
   * evaluation, not for evaluation of a single annotation spec.
   *
   * @var float
   */
  public $quadraticKappa;
  /**
   * Output only. Recall.
   *
   * @var float
   */
  public $recall;

  /**
   * Output only. Confusion matrix of the evaluation. Only set for the overall
   * model evaluation, not for evaluation of a single annotation spec.
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
   * Output only. The harmonic mean of recall and precision.
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
   * Output only. Linear weighted kappa. Only set for the overall model
   * evaluation, not for evaluation of a single annotation spec.
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
   * Output only. Mean absolute error. Only set for the overall model
   * evaluation, not for evaluation of a single annotation spec.
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
   * Output only. Mean squared error. Only set for the overall model evaluation,
   * not for evaluation of a single annotation spec.
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
   * Output only. Precision.
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
   * Output only. Quadratic weighted kappa. Only set for the overall model
   * evaluation, not for evaluation of a single annotation spec.
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
   * Output only. Recall.
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
class_alias(XPSTextSentimentEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSTextSentimentEvaluationMetrics');
