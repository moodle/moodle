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

namespace Google\Service\AnalyticsData;

class MetricHeader extends \Google\Model
{
  /**
   * Unspecified type.
   */
  public const TYPE_METRIC_TYPE_UNSPECIFIED = 'METRIC_TYPE_UNSPECIFIED';
  /**
   * Integer type.
   */
  public const TYPE_TYPE_INTEGER = 'TYPE_INTEGER';
  /**
   * Floating point type.
   */
  public const TYPE_TYPE_FLOAT = 'TYPE_FLOAT';
  /**
   * A duration of seconds; a special floating point type.
   */
  public const TYPE_TYPE_SECONDS = 'TYPE_SECONDS';
  /**
   * A duration in milliseconds; a special floating point type.
   */
  public const TYPE_TYPE_MILLISECONDS = 'TYPE_MILLISECONDS';
  /**
   * A duration in minutes; a special floating point type.
   */
  public const TYPE_TYPE_MINUTES = 'TYPE_MINUTES';
  /**
   * A duration in hours; a special floating point type.
   */
  public const TYPE_TYPE_HOURS = 'TYPE_HOURS';
  /**
   * A custom metric of standard type; a special floating point type.
   */
  public const TYPE_TYPE_STANDARD = 'TYPE_STANDARD';
  /**
   * An amount of money; a special floating point type.
   */
  public const TYPE_TYPE_CURRENCY = 'TYPE_CURRENCY';
  /**
   * A length in feet; a special floating point type.
   */
  public const TYPE_TYPE_FEET = 'TYPE_FEET';
  /**
   * A length in miles; a special floating point type.
   */
  public const TYPE_TYPE_MILES = 'TYPE_MILES';
  /**
   * A length in meters; a special floating point type.
   */
  public const TYPE_TYPE_METERS = 'TYPE_METERS';
  /**
   * A length in kilometers; a special floating point type.
   */
  public const TYPE_TYPE_KILOMETERS = 'TYPE_KILOMETERS';
  /**
   * The metric's name.
   *
   * @var string
   */
  public $name;
  /**
   * The metric's data type.
   *
   * @var string
   */
  public $type;

  /**
   * The metric's name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The metric's data type.
   *
   * Accepted values: METRIC_TYPE_UNSPECIFIED, TYPE_INTEGER, TYPE_FLOAT,
   * TYPE_SECONDS, TYPE_MILLISECONDS, TYPE_MINUTES, TYPE_HOURS, TYPE_STANDARD,
   * TYPE_CURRENCY, TYPE_FEET, TYPE_MILES, TYPE_METERS, TYPE_KILOMETERS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricHeader::class, 'Google_Service_AnalyticsData_MetricHeader');
