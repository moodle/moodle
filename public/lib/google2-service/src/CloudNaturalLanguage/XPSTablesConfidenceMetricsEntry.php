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

class XPSTablesConfidenceMetricsEntry extends \Google\Model
{
  /**
   * The confidence threshold value used to compute the metrics.
   *
   * @var 
   */
  public $confidenceThreshold;
  /**
   * The harmonic mean of recall and precision. (2 * precision * recall) /
   * (precision + recall)
   *
   * @var 
   */
  public $f1Score;
  /**
   * False negative count.
   *
   * @var string
   */
  public $falseNegativeCount;
  /**
   * False positive count.
   *
   * @var string
   */
  public $falsePositiveCount;
  /**
   * FPR = #false positives / (#false positives + #true negatives)
   *
   * @var 
   */
  public $falsePositiveRate;
  /**
   * Precision = #true positives / (#true positives + #false positives).
   *
   * @var 
   */
  public $precision;
  /**
   * Recall = #true positives / (#true positives + #false negatives).
   *
   * @var 
   */
  public $recall;
  /**
   * True negative count.
   *
   * @var string
   */
  public $trueNegativeCount;
  /**
   * True positive count.
   *
   * @var string
   */
  public $truePositiveCount;
  /**
   * TPR = #true positives / (#true positives + #false negatvies)
   *
   * @var 
   */
  public $truePositiveRate;

  public function setConfidenceThreshold($confidenceThreshold)
  {
    $this->confidenceThreshold = $confidenceThreshold;
  }
  public function getConfidenceThreshold()
  {
    return $this->confidenceThreshold;
  }
  public function setF1Score($f1Score)
  {
    $this->f1Score = $f1Score;
  }
  public function getF1Score()
  {
    return $this->f1Score;
  }
  /**
   * False negative count.
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
   * False positive count.
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
  public function setFalsePositiveRate($falsePositiveRate)
  {
    $this->falsePositiveRate = $falsePositiveRate;
  }
  public function getFalsePositiveRate()
  {
    return $this->falsePositiveRate;
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
  /**
   * True negative count.
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
   * True positive count.
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
  public function setTruePositiveRate($truePositiveRate)
  {
    $this->truePositiveRate = $truePositiveRate;
  }
  public function getTruePositiveRate()
  {
    return $this->truePositiveRate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTablesConfidenceMetricsEntry::class, 'Google_Service_CloudNaturalLanguage_XPSTablesConfidenceMetricsEntry');
