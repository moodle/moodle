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

namespace Google\Service\AuthorizedBuyersMarketplace;

class DayPartTargeting extends \Google\Collection
{
  /**
   * Default value. This field is unused.
   */
  public const TIME_ZONE_TYPE_TIME_ZONE_TYPE_UNSPECIFIED = 'TIME_ZONE_TYPE_UNSPECIFIED';
  /**
   * The publisher's time zone
   */
  public const TIME_ZONE_TYPE_SELLER = 'SELLER';
  /**
   * The user's time zone
   */
  public const TIME_ZONE_TYPE_USER = 'USER';
  protected $collection_key = 'dayParts';
  protected $dayPartsType = DayPart::class;
  protected $dayPartsDataType = 'array';
  /**
   * The time zone type of the day parts
   *
   * @var string
   */
  public $timeZoneType;

  /**
   * The targeted weekdays and times
   *
   * @param DayPart[] $dayParts
   */
  public function setDayParts($dayParts)
  {
    $this->dayParts = $dayParts;
  }
  /**
   * @return DayPart[]
   */
  public function getDayParts()
  {
    return $this->dayParts;
  }
  /**
   * The time zone type of the day parts
   *
   * Accepted values: TIME_ZONE_TYPE_UNSPECIFIED, SELLER, USER
   *
   * @param self::TIME_ZONE_TYPE_* $timeZoneType
   */
  public function setTimeZoneType($timeZoneType)
  {
    $this->timeZoneType = $timeZoneType;
  }
  /**
   * @return self::TIME_ZONE_TYPE_*
   */
  public function getTimeZoneType()
  {
    return $this->timeZoneType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DayPartTargeting::class, 'Google_Service_AuthorizedBuyersMarketplace_DayPartTargeting');
