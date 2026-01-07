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

namespace Google\Service\AdMob;

class ReportRowMetricValue extends \Google\Model
{
  /**
   * Double precision (approximate) decimal values. Rates are from 0 to 1.
   *
   * @var 
   */
  public $doubleValue;
  /**
   * Metric integer value.
   *
   * @var string
   */
  public $integerValue;
  /**
   * Amount in micros. One million is equivalent to one unit. Currency value is
   * in the unit (USD, EUR or other) specified by the request. For example,
   * $6.50 whould be represented as 6500000 micros.
   *
   * @var string
   */
  public $microsValue;

  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * Metric integer value.
   *
   * @param string $integerValue
   */
  public function setIntegerValue($integerValue)
  {
    $this->integerValue = $integerValue;
  }
  /**
   * @return string
   */
  public function getIntegerValue()
  {
    return $this->integerValue;
  }
  /**
   * Amount in micros. One million is equivalent to one unit. Currency value is
   * in the unit (USD, EUR or other) specified by the request. For example,
   * $6.50 whould be represented as 6500000 micros.
   *
   * @param string $microsValue
   */
  public function setMicrosValue($microsValue)
  {
    $this->microsValue = $microsValue;
  }
  /**
   * @return string
   */
  public function getMicrosValue()
  {
    return $this->microsValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportRowMetricValue::class, 'Google_Service_AdMob_ReportRowMetricValue');
