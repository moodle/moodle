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

namespace Google\Service\MyBusinessLodging;

class Property extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BUILT_YEAR_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BUILT_YEAR_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BUILT_YEAR_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BUILT_YEAR_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FLOORS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FLOORS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FLOORS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FLOORS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const LAST_RENOVATED_YEAR_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const LAST_RENOVATED_YEAR_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const LAST_RENOVATED_YEAR_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const LAST_RENOVATED_YEAR_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ROOMS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ROOMS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ROOMS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ROOMS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Built year. The year that construction of the property was completed.
   *
   * @var int
   */
  public $builtYear;
  /**
   * Built year exception.
   *
   * @var string
   */
  public $builtYearException;
  /**
   * Floors count. The number of stories the building has from the ground floor
   * to the top floor that are accessible to guests.
   *
   * @var int
   */
  public $floorsCount;
  /**
   * Floors count exception.
   *
   * @var string
   */
  public $floorsCountException;
  /**
   * Last renovated year. The year when the most recent renovation of the
   * property was completed. Renovation may include all or any combination of
   * the following: the units, the public spaces, the exterior, or the interior.
   *
   * @var int
   */
  public $lastRenovatedYear;
  /**
   * Last renovated year exception.
   *
   * @var string
   */
  public $lastRenovatedYearException;
  /**
   * Rooms count. The total number of rooms and suites bookable by guests for an
   * overnight stay. Does not include event space, public spaces, conference
   * rooms, fitness rooms, business centers, spa, salon, restaurants/bars, or
   * shops.
   *
   * @var int
   */
  public $roomsCount;
  /**
   * Rooms count exception.
   *
   * @var string
   */
  public $roomsCountException;

  /**
   * Built year. The year that construction of the property was completed.
   *
   * @param int $builtYear
   */
  public function setBuiltYear($builtYear)
  {
    $this->builtYear = $builtYear;
  }
  /**
   * @return int
   */
  public function getBuiltYear()
  {
    return $this->builtYear;
  }
  /**
   * Built year exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BUILT_YEAR_EXCEPTION_* $builtYearException
   */
  public function setBuiltYearException($builtYearException)
  {
    $this->builtYearException = $builtYearException;
  }
  /**
   * @return self::BUILT_YEAR_EXCEPTION_*
   */
  public function getBuiltYearException()
  {
    return $this->builtYearException;
  }
  /**
   * Floors count. The number of stories the building has from the ground floor
   * to the top floor that are accessible to guests.
   *
   * @param int $floorsCount
   */
  public function setFloorsCount($floorsCount)
  {
    $this->floorsCount = $floorsCount;
  }
  /**
   * @return int
   */
  public function getFloorsCount()
  {
    return $this->floorsCount;
  }
  /**
   * Floors count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FLOORS_COUNT_EXCEPTION_* $floorsCountException
   */
  public function setFloorsCountException($floorsCountException)
  {
    $this->floorsCountException = $floorsCountException;
  }
  /**
   * @return self::FLOORS_COUNT_EXCEPTION_*
   */
  public function getFloorsCountException()
  {
    return $this->floorsCountException;
  }
  /**
   * Last renovated year. The year when the most recent renovation of the
   * property was completed. Renovation may include all or any combination of
   * the following: the units, the public spaces, the exterior, or the interior.
   *
   * @param int $lastRenovatedYear
   */
  public function setLastRenovatedYear($lastRenovatedYear)
  {
    $this->lastRenovatedYear = $lastRenovatedYear;
  }
  /**
   * @return int
   */
  public function getLastRenovatedYear()
  {
    return $this->lastRenovatedYear;
  }
  /**
   * Last renovated year exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::LAST_RENOVATED_YEAR_EXCEPTION_* $lastRenovatedYearException
   */
  public function setLastRenovatedYearException($lastRenovatedYearException)
  {
    $this->lastRenovatedYearException = $lastRenovatedYearException;
  }
  /**
   * @return self::LAST_RENOVATED_YEAR_EXCEPTION_*
   */
  public function getLastRenovatedYearException()
  {
    return $this->lastRenovatedYearException;
  }
  /**
   * Rooms count. The total number of rooms and suites bookable by guests for an
   * overnight stay. Does not include event space, public spaces, conference
   * rooms, fitness rooms, business centers, spa, salon, restaurants/bars, or
   * shops.
   *
   * @param int $roomsCount
   */
  public function setRoomsCount($roomsCount)
  {
    $this->roomsCount = $roomsCount;
  }
  /**
   * @return int
   */
  public function getRoomsCount()
  {
    return $this->roomsCount;
  }
  /**
   * Rooms count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ROOMS_COUNT_EXCEPTION_* $roomsCountException
   */
  public function setRoomsCountException($roomsCountException)
  {
    $this->roomsCountException = $roomsCountException;
  }
  /**
   * @return self::ROOMS_COUNT_EXCEPTION_*
   */
  public function getRoomsCountException()
  {
    return $this->roomsCountException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Property::class, 'Google_Service_MyBusinessLodging_Property');
