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

namespace Google\Service\AdExchangeBuyerII;

class MetricValue extends \Google\Model
{
  /**
   * The expected value of the metric.
   *
   * @var string
   */
  public $value;
  /**
   * The variance (for example, square of the standard deviation) of the metric
   * value. If value is exact, variance is 0. Can be used to calculate margin of
   * error as a percentage of value, using the following formula, where Z is the
   * standard constant that depends on the preferred size of the confidence
   * interval (for example, for 90% confidence interval, use Z = 1.645):
   * marginOfError = 100 * Z * sqrt(variance) / value
   *
   * @var string
   */
  public $variance;

  /**
   * The expected value of the metric.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * The variance (for example, square of the standard deviation) of the metric
   * value. If value is exact, variance is 0. Can be used to calculate margin of
   * error as a percentage of value, using the following formula, where Z is the
   * standard constant that depends on the preferred size of the confidence
   * interval (for example, for 90% confidence interval, use Z = 1.645):
   * marginOfError = 100 * Z * sqrt(variance) / value
   *
   * @param string $variance
   */
  public function setVariance($variance)
  {
    $this->variance = $variance;
  }
  /**
   * @return string
   */
  public function getVariance()
  {
    return $this->variance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricValue::class, 'Google_Service_AdExchangeBuyerII_MetricValue');
