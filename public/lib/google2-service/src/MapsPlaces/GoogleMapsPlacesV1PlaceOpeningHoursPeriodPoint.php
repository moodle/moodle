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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint extends \Google\Model
{
  protected $dateType = GoogleTypeDate::class;
  protected $dateDataType = '';
  /**
   * A day of the week, as an integer in the range 0-6. 0 is Sunday, 1 is
   * Monday, etc.
   *
   * @var int
   */
  public $day;
  /**
   * The hour in 24 hour format. Ranges from 0 to 23.
   *
   * @var int
   */
  public $hour;
  /**
   * The minute. Ranges from 0 to 59.
   *
   * @var int
   */
  public $minute;
  /**
   * Whether or not this endpoint was truncated. Truncation occurs when the real
   * hours are outside the times we are willing to return hours between, so we
   * truncate the hours back to these boundaries. This ensures that at most 24 *
   * 7 hours from midnight of the day of the request are returned.
   *
   * @var bool
   */
  public $truncated;

  /**
   * Date in the local timezone for the place.
   *
   * @param GoogleTypeDate $date
   */
  public function setDate(GoogleTypeDate $date)
  {
    $this->date = $date;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * A day of the week, as an integer in the range 0-6. 0 is Sunday, 1 is
   * Monday, etc.
   *
   * @param int $day
   */
  public function setDay($day)
  {
    $this->day = $day;
  }
  /**
   * @return int
   */
  public function getDay()
  {
    return $this->day;
  }
  /**
   * The hour in 24 hour format. Ranges from 0 to 23.
   *
   * @param int $hour
   */
  public function setHour($hour)
  {
    $this->hour = $hour;
  }
  /**
   * @return int
   */
  public function getHour()
  {
    return $this->hour;
  }
  /**
   * The minute. Ranges from 0 to 59.
   *
   * @param int $minute
   */
  public function setMinute($minute)
  {
    $this->minute = $minute;
  }
  /**
   * @return int
   */
  public function getMinute()
  {
    return $this->minute;
  }
  /**
   * Whether or not this endpoint was truncated. Truncation occurs when the real
   * hours are outside the times we are willing to return hours between, so we
   * truncate the hours back to these boundaries. This ensures that at most 24 *
   * 7 hours from midnight of the day of the request are returned.
   *
   * @param bool $truncated
   */
  public function setTruncated($truncated)
  {
    $this->truncated = $truncated;
  }
  /**
   * @return bool
   */
  public function getTruncated()
  {
    return $this->truncated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceOpeningHoursPeriodPoint');
