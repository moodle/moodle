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

namespace Google\Service\Monitoring;

class ValueDescriptor extends \Google\Model
{
  /**
   * Do not use this default value.
   */
  public const METRIC_KIND_METRIC_KIND_UNSPECIFIED = 'METRIC_KIND_UNSPECIFIED';
  /**
   * An instantaneous measurement of a value.
   */
  public const METRIC_KIND_GAUGE = 'GAUGE';
  /**
   * The change in a value during a time interval.
   */
  public const METRIC_KIND_DELTA = 'DELTA';
  /**
   * A value accumulated over a time interval. Cumulative measurements in a time
   * series should have the same start time and increasing end times, until an
   * event resets the cumulative value to zero and sets a new start time for the
   * following points.
   */
  public const METRIC_KIND_CUMULATIVE = 'CUMULATIVE';
  /**
   * Do not use this default value.
   */
  public const VALUE_TYPE_VALUE_TYPE_UNSPECIFIED = 'VALUE_TYPE_UNSPECIFIED';
  /**
   * The value is a boolean. This value type can be used only if the metric kind
   * is GAUGE.
   */
  public const VALUE_TYPE_BOOL = 'BOOL';
  /**
   * The value is a signed 64-bit integer.
   */
  public const VALUE_TYPE_INT64 = 'INT64';
  /**
   * The value is a double precision floating point number.
   */
  public const VALUE_TYPE_DOUBLE = 'DOUBLE';
  /**
   * The value is a text string. This value type can be used only if the metric
   * kind is GAUGE.
   */
  public const VALUE_TYPE_STRING = 'STRING';
  /**
   * The value is a Distribution.
   */
  public const VALUE_TYPE_DISTRIBUTION = 'DISTRIBUTION';
  /**
   * The value is money.
   */
  public const VALUE_TYPE_MONEY = 'MONEY';
  /**
   * The value key.
   *
   * @var string
   */
  public $key;
  /**
   * The value stream kind.
   *
   * @var string
   */
  public $metricKind;
  /**
   * The unit in which time_series point values are reported. unit follows the
   * UCUM format for units as seen in https://unitsofmeasure.org/ucum.html. unit
   * is only valid if value_type is INTEGER, DOUBLE, DISTRIBUTION.
   *
   * @var string
   */
  public $unit;
  /**
   * The value type.
   *
   * @var string
   */
  public $valueType;

  /**
   * The value key.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
  /**
   * The value stream kind.
   *
   * Accepted values: METRIC_KIND_UNSPECIFIED, GAUGE, DELTA, CUMULATIVE
   *
   * @param self::METRIC_KIND_* $metricKind
   */
  public function setMetricKind($metricKind)
  {
    $this->metricKind = $metricKind;
  }
  /**
   * @return self::METRIC_KIND_*
   */
  public function getMetricKind()
  {
    return $this->metricKind;
  }
  /**
   * The unit in which time_series point values are reported. unit follows the
   * UCUM format for units as seen in https://unitsofmeasure.org/ucum.html. unit
   * is only valid if value_type is INTEGER, DOUBLE, DISTRIBUTION.
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
  /**
   * The value type.
   *
   * Accepted values: VALUE_TYPE_UNSPECIFIED, BOOL, INT64, DOUBLE, STRING,
   * DISTRIBUTION, MONEY
   *
   * @param self::VALUE_TYPE_* $valueType
   */
  public function setValueType($valueType)
  {
    $this->valueType = $valueType;
  }
  /**
   * @return self::VALUE_TYPE_*
   */
  public function getValueType()
  {
    return $this->valueType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValueDescriptor::class, 'Google_Service_Monitoring_ValueDescriptor');
