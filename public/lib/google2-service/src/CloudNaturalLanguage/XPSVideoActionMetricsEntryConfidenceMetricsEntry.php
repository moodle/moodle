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

class XPSVideoActionMetricsEntryConfidenceMetricsEntry extends \Google\Model
{
  /**
   * Output only. The confidence threshold value used to compute the metrics.
   *
   * @var float
   */
  public $confidenceThreshold;
  /**
   * Output only. The harmonic mean of recall and precision.
   *
   * @var float
   */
  public $f1Score;
  /**
   * Output only. Precision for the given confidence threshold.
   *
   * @var float
   */
  public $precision;
  /**
   * Output only. Recall for the given confidence threshold.
   *
   * @var float
   */
  public $recall;

  /**
   * Output only. The confidence threshold value used to compute the metrics.
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
   * Output only. Precision for the given confidence threshold.
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
   * Output only. Recall for the given confidence threshold.
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
class_alias(XPSVideoActionMetricsEntryConfidenceMetricsEntry::class, 'Google_Service_CloudNaturalLanguage_XPSVideoActionMetricsEntryConfidenceMetricsEntry');
