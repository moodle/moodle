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

namespace Google\Service\Bigquery;

class AggregateClassificationMetrics extends \Google\Model
{
  /**
   * Accuracy is the fraction of predictions given the correct label. For
   * multiclass this is a micro-averaged metric.
   *
   * @var 
   */
  public $accuracy;
  /**
   * The F1 score is an average of recall and precision. For multiclass this is
   * a macro-averaged metric.
   *
   * @var 
   */
  public $f1Score;
  /**
   * Logarithmic Loss. For multiclass this is a macro-averaged metric.
   *
   * @var 
   */
  public $logLoss;
  /**
   * Precision is the fraction of actual positive predictions that had positive
   * actual labels. For multiclass this is a macro-averaged metric treating each
   * class as a binary classifier.
   *
   * @var 
   */
  public $precision;
  /**
   * Recall is the fraction of actual positive labels that were given a positive
   * prediction. For multiclass this is a macro-averaged metric.
   *
   * @var 
   */
  public $recall;
  /**
   * Area Under a ROC Curve. For multiclass this is a macro-averaged metric.
   *
   * @var 
   */
  public $rocAuc;
  /**
   * Threshold at which the metrics are computed. For binary classification
   * models this is the positive class threshold. For multi-class classification
   * models this is the confidence threshold.
   *
   * @var 
   */
  public $threshold;

  public function setAccuracy($accuracy)
  {
    $this->accuracy = $accuracy;
  }
  public function getAccuracy()
  {
    return $this->accuracy;
  }
  public function setF1Score($f1Score)
  {
    $this->f1Score = $f1Score;
  }
  public function getF1Score()
  {
    return $this->f1Score;
  }
  public function setLogLoss($logLoss)
  {
    $this->logLoss = $logLoss;
  }
  public function getLogLoss()
  {
    return $this->logLoss;
  }
  public function setPrecision($precision)
  {
    $this->precision = $precision;
  }
  public function getPrecision()
  {
    return $this->precision;
  }
  public function setRecall($recall)
  {
    $this->recall = $recall;
  }
  public function getRecall()
  {
    return $this->recall;
  }
  public function setRocAuc($rocAuc)
  {
    $this->rocAuc = $rocAuc;
  }
  public function getRocAuc()
  {
    return $this->rocAuc;
  }
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  public function getThreshold()
  {
    return $this->threshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregateClassificationMetrics::class, 'Google_Service_Bigquery_AggregateClassificationMetrics');
