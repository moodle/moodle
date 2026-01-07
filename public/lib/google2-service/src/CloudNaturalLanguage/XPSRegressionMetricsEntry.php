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

class XPSRegressionMetricsEntry extends \Google\Model
{
  /**
   * The observed value for a row in the dataset.
   *
   * @var float
   */
  public $predictedValue;
  /**
   * The actual target value for a row in the dataset.
   *
   * @var float
   */
  public $trueValue;

  /**
   * The observed value for a row in the dataset.
   *
   * @param float $predictedValue
   */
  public function setPredictedValue($predictedValue)
  {
    $this->predictedValue = $predictedValue;
  }
  /**
   * @return float
   */
  public function getPredictedValue()
  {
    return $this->predictedValue;
  }
  /**
   * The actual target value for a row in the dataset.
   *
   * @param float $trueValue
   */
  public function setTrueValue($trueValue)
  {
    $this->trueValue = $trueValue;
  }
  /**
   * @return float
   */
  public function getTrueValue()
  {
    return $this->trueValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSRegressionMetricsEntry::class, 'Google_Service_CloudNaturalLanguage_XPSRegressionMetricsEntry');
