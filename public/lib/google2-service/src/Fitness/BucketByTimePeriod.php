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

namespace Google\Service\Fitness;

class BucketByTimePeriod extends \Google\Model
{
  public const TYPE_day = 'day';
  public const TYPE_week = 'week';
  public const TYPE_month = 'month';
  /**
   * org.joda.timezone.DateTimeZone
   *
   * @var string
   */
  public $timeZoneId;
  /**
   * @var string
   */
  public $type;
  /**
   * @var int
   */
  public $value;

  /**
   * org.joda.timezone.DateTimeZone
   *
   * @param string $timeZoneId
   */
  public function setTimeZoneId($timeZoneId)
  {
    $this->timeZoneId = $timeZoneId;
  }
  /**
   * @return string
   */
  public function getTimeZoneId()
  {
    return $this->timeZoneId;
  }
  /**
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
  /**
   * @param int $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return int
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketByTimePeriod::class, 'Google_Service_Fitness_BucketByTimePeriod');
