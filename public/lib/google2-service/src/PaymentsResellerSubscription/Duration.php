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

namespace Google\Service\PaymentsResellerSubscription;

class Duration extends \Google\Model
{
  /**
   * Default value, reserved as an invalid or an unexpected value.
   */
  public const UNIT_UNIT_UNSPECIFIED = 'UNIT_UNSPECIFIED';
  /**
   * Unit of a calendar month.
   */
  public const UNIT_MONTH = 'MONTH';
  /**
   * Unit of a day.
   */
  public const UNIT_DAY = 'DAY';
  /**
   * Unit of an hour. It is used for testing.
   */
  public const UNIT_HOUR = 'HOUR';
  /**
   * number of duration units to be included.
   *
   * @var int
   */
  public $count;
  /**
   * The unit used for the duration
   *
   * @var string
   */
  public $unit;

  /**
   * number of duration units to be included.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * The unit used for the duration
   *
   * Accepted values: UNIT_UNSPECIFIED, MONTH, DAY, HOUR
   *
   * @param self::UNIT_* $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return self::UNIT_*
   */
  public function getUnit()
  {
    return $this->unit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Duration::class, 'Google_Service_PaymentsResellerSubscription_Duration');
