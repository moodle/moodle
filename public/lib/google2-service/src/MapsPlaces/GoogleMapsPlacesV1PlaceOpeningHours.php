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

class GoogleMapsPlacesV1PlaceOpeningHours extends \Google\Collection
{
  /**
   * Default value when secondary hour type is not specified.
   */
  public const SECONDARY_HOURS_TYPE_SECONDARY_HOURS_TYPE_UNSPECIFIED = 'SECONDARY_HOURS_TYPE_UNSPECIFIED';
  /**
   * The drive-through hour for banks, restaurants, or pharmacies.
   */
  public const SECONDARY_HOURS_TYPE_DRIVE_THROUGH = 'DRIVE_THROUGH';
  /**
   * The happy hour.
   */
  public const SECONDARY_HOURS_TYPE_HAPPY_HOUR = 'HAPPY_HOUR';
  /**
   * The delivery hour.
   */
  public const SECONDARY_HOURS_TYPE_DELIVERY = 'DELIVERY';
  /**
   * The takeout hour.
   */
  public const SECONDARY_HOURS_TYPE_TAKEOUT = 'TAKEOUT';
  /**
   * The kitchen hour.
   */
  public const SECONDARY_HOURS_TYPE_KITCHEN = 'KITCHEN';
  /**
   * The breakfast hour.
   */
  public const SECONDARY_HOURS_TYPE_BREAKFAST = 'BREAKFAST';
  /**
   * The lunch hour.
   */
  public const SECONDARY_HOURS_TYPE_LUNCH = 'LUNCH';
  /**
   * The dinner hour.
   */
  public const SECONDARY_HOURS_TYPE_DINNER = 'DINNER';
  /**
   * The brunch hour.
   */
  public const SECONDARY_HOURS_TYPE_BRUNCH = 'BRUNCH';
  /**
   * The pickup hour.
   */
  public const SECONDARY_HOURS_TYPE_PICKUP = 'PICKUP';
  /**
   * The access hours for storage places.
   */
  public const SECONDARY_HOURS_TYPE_ACCESS = 'ACCESS';
  /**
   * The special hours for seniors.
   */
  public const SECONDARY_HOURS_TYPE_SENIOR_HOURS = 'SENIOR_HOURS';
  /**
   * The online service hours.
   */
  public const SECONDARY_HOURS_TYPE_ONLINE_SERVICE_HOURS = 'ONLINE_SERVICE_HOURS';
  protected $collection_key = 'weekdayDescriptions';
  /**
   * The next time the current opening hours period ends up to 7 days in the
   * future. This field is only populated if the opening hours period is active
   * at the time of serving the request.
   *
   * @var string
   */
  public $nextCloseTime;
  /**
   * The next time the current opening hours period starts up to 7 days in the
   * future. This field is only populated if the opening hours period is not
   * active at the time of serving the request.
   *
   * @var string
   */
  public $nextOpenTime;
  /**
   * Whether the opening hours period is currently active. For regular opening
   * hours and current opening hours, this field means whether the place is
   * open. For secondary opening hours and current secondary opening hours, this
   * field means whether the secondary hours of this place is active.
   *
   * @var bool
   */
  public $openNow;
  protected $periodsType = GoogleMapsPlacesV1PlaceOpeningHoursPeriod::class;
  protected $periodsDataType = 'array';
  /**
   * A type string used to identify the type of secondary hours.
   *
   * @var string
   */
  public $secondaryHoursType;
  protected $specialDaysType = GoogleMapsPlacesV1PlaceOpeningHoursSpecialDay::class;
  protected $specialDaysDataType = 'array';
  /**
   * Localized strings describing the opening hours of this place, one string
   * for each day of the week. NOTE: The order of the days and the start of the
   * week is determined by the locale (language and region). The ordering of the
   * `periods` array is independent of the ordering of the
   * `weekday_descriptions` array. Do not assume they will begin on the same
   * day. Will be empty if the hours are unknown or could not be converted to
   * localized text. Example: "Sun: 18:00–06:00"
   *
   * @var string[]
   */
  public $weekdayDescriptions;

  /**
   * The next time the current opening hours period ends up to 7 days in the
   * future. This field is only populated if the opening hours period is active
   * at the time of serving the request.
   *
   * @param string $nextCloseTime
   */
  public function setNextCloseTime($nextCloseTime)
  {
    $this->nextCloseTime = $nextCloseTime;
  }
  /**
   * @return string
   */
  public function getNextCloseTime()
  {
    return $this->nextCloseTime;
  }
  /**
   * The next time the current opening hours period starts up to 7 days in the
   * future. This field is only populated if the opening hours period is not
   * active at the time of serving the request.
   *
   * @param string $nextOpenTime
   */
  public function setNextOpenTime($nextOpenTime)
  {
    $this->nextOpenTime = $nextOpenTime;
  }
  /**
   * @return string
   */
  public function getNextOpenTime()
  {
    return $this->nextOpenTime;
  }
  /**
   * Whether the opening hours period is currently active. For regular opening
   * hours and current opening hours, this field means whether the place is
   * open. For secondary opening hours and current secondary opening hours, this
   * field means whether the secondary hours of this place is active.
   *
   * @param bool $openNow
   */
  public function setOpenNow($openNow)
  {
    $this->openNow = $openNow;
  }
  /**
   * @return bool
   */
  public function getOpenNow()
  {
    return $this->openNow;
  }
  /**
   * The periods that this place is open during the week. The periods are in
   * chronological order, in the place-local timezone. An empty (but not absent)
   * value indicates a place that is never open, e.g. because it is closed
   * temporarily for renovations. The starting day of `periods` is NOT fixed and
   * should not be assumed to be Sunday. The API determines the start day based
   * on a variety of factors. For example, for a 24/7 business, the first period
   * may begin on the day of the request. For other businesses, it might be the
   * first day of the week that they are open. NOTE: The ordering of the
   * `periods` array is independent of the ordering of the
   * `weekday_descriptions` array. Do not assume they will begin on the same
   * day.
   *
   * @param GoogleMapsPlacesV1PlaceOpeningHoursPeriod[] $periods
   */
  public function setPeriods($periods)
  {
    $this->periods = $periods;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceOpeningHoursPeriod[]
   */
  public function getPeriods()
  {
    return $this->periods;
  }
  /**
   * A type string used to identify the type of secondary hours.
   *
   * Accepted values: SECONDARY_HOURS_TYPE_UNSPECIFIED, DRIVE_THROUGH,
   * HAPPY_HOUR, DELIVERY, TAKEOUT, KITCHEN, BREAKFAST, LUNCH, DINNER, BRUNCH,
   * PICKUP, ACCESS, SENIOR_HOURS, ONLINE_SERVICE_HOURS
   *
   * @param self::SECONDARY_HOURS_TYPE_* $secondaryHoursType
   */
  public function setSecondaryHoursType($secondaryHoursType)
  {
    $this->secondaryHoursType = $secondaryHoursType;
  }
  /**
   * @return self::SECONDARY_HOURS_TYPE_*
   */
  public function getSecondaryHoursType()
  {
    return $this->secondaryHoursType;
  }
  /**
   * Structured information for special days that fall within the period that
   * the returned opening hours cover. Special days are days that could impact
   * the business hours of a place, e.g. Christmas day. Set for
   * current_opening_hours and current_secondary_opening_hours if there are
   * exceptional hours.
   *
   * @param GoogleMapsPlacesV1PlaceOpeningHoursSpecialDay[] $specialDays
   */
  public function setSpecialDays($specialDays)
  {
    $this->specialDays = $specialDays;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceOpeningHoursSpecialDay[]
   */
  public function getSpecialDays()
  {
    return $this->specialDays;
  }
  /**
   * Localized strings describing the opening hours of this place, one string
   * for each day of the week. NOTE: The order of the days and the start of the
   * week is determined by the locale (language and region). The ordering of the
   * `periods` array is independent of the ordering of the
   * `weekday_descriptions` array. Do not assume they will begin on the same
   * day. Will be empty if the hours are unknown or could not be converted to
   * localized text. Example: "Sun: 18:00–06:00"
   *
   * @param string[] $weekdayDescriptions
   */
  public function setWeekdayDescriptions($weekdayDescriptions)
  {
    $this->weekdayDescriptions = $weekdayDescriptions;
  }
  /**
   * @return string[]
   */
  public function getWeekdayDescriptions()
  {
    return $this->weekdayDescriptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceOpeningHours::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceOpeningHours');
