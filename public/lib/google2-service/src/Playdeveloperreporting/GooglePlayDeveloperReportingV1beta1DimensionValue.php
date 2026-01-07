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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1DimensionValue extends \Google\Model
{
  /**
   * Name of the dimension.
   *
   * @var string
   */
  public $dimension;
  /**
   * Actual value, represented as an int64.
   *
   * @var string
   */
  public $int64Value;
  /**
   * Actual value, represented as a string.
   *
   * @var string
   */
  public $stringValue;
  /**
   * Optional. Human-friendly label for the value, always in English. For
   * example, 'Spain' for the 'ES' country code. Whereas the dimension value is
   * stable, this value label is subject to change. Do not assume that the
   * (value, value_label) relationship is stable. For example, the ISO country
   * code 'MK' changed its name recently to 'North Macedonia'.
   *
   * @var string
   */
  public $valueLabel;

  /**
   * Name of the dimension.
   *
   * @param string $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return string
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * Actual value, represented as an int64.
   *
   * @param string $int64Value
   */
  public function setInt64Value($int64Value)
  {
    $this->int64Value = $int64Value;
  }
  /**
   * @return string
   */
  public function getInt64Value()
  {
    return $this->int64Value;
  }
  /**
   * Actual value, represented as a string.
   *
   * @param string $stringValue
   */
  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }
  /**
   * @return string
   */
  public function getStringValue()
  {
    return $this->stringValue;
  }
  /**
   * Optional. Human-friendly label for the value, always in English. For
   * example, 'Spain' for the 'ES' country code. Whereas the dimension value is
   * stable, this value label is subject to change. Do not assume that the
   * (value, value_label) relationship is stable. For example, the ISO country
   * code 'MK' changed its name recently to 'North Macedonia'.
   *
   * @param string $valueLabel
   */
  public function setValueLabel($valueLabel)
  {
    $this->valueLabel = $valueLabel;
  }
  /**
   * @return string
   */
  public function getValueLabel()
  {
    return $this->valueLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1DimensionValue::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1DimensionValue');
