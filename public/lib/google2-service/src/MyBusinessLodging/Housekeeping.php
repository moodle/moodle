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

class Housekeeping extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DAILY_HOUSEKEEPING_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DAILY_HOUSEKEEPING_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DAILY_HOUSEKEEPING_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DAILY_HOUSEKEEPING_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HOUSEKEEPING_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HOUSEKEEPING_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HOUSEKEEPING_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HOUSEKEEPING_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const TURNDOWN_SERVICE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const TURNDOWN_SERVICE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const TURNDOWN_SERVICE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const TURNDOWN_SERVICE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Daily housekeeping. Guest units are cleaned by hotel staff daily during
   * guest's stay.
   *
   * @var bool
   */
  public $dailyHousekeeping;
  /**
   * Daily housekeeping exception.
   *
   * @var string
   */
  public $dailyHousekeepingException;
  /**
   * Housekeeping available. Guest units are cleaned by hotel staff during
   * guest's stay. Schedule may vary from daily, weekly, or specific days of the
   * week.
   *
   * @var bool
   */
  public $housekeepingAvailable;
  /**
   * Housekeeping available exception.
   *
   * @var string
   */
  public $housekeepingAvailableException;
  /**
   * Turndown service. Hotel staff enters guest units to prepare the bed for
   * sleep use. May or may not include some light housekeeping. May or may not
   * include an evening snack or candy. Also known as evening service.
   *
   * @var bool
   */
  public $turndownService;
  /**
   * Turndown service exception.
   *
   * @var string
   */
  public $turndownServiceException;

  /**
   * Daily housekeeping. Guest units are cleaned by hotel staff daily during
   * guest's stay.
   *
   * @param bool $dailyHousekeeping
   */
  public function setDailyHousekeeping($dailyHousekeeping)
  {
    $this->dailyHousekeeping = $dailyHousekeeping;
  }
  /**
   * @return bool
   */
  public function getDailyHousekeeping()
  {
    return $this->dailyHousekeeping;
  }
  /**
   * Daily housekeeping exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DAILY_HOUSEKEEPING_EXCEPTION_* $dailyHousekeepingException
   */
  public function setDailyHousekeepingException($dailyHousekeepingException)
  {
    $this->dailyHousekeepingException = $dailyHousekeepingException;
  }
  /**
   * @return self::DAILY_HOUSEKEEPING_EXCEPTION_*
   */
  public function getDailyHousekeepingException()
  {
    return $this->dailyHousekeepingException;
  }
  /**
   * Housekeeping available. Guest units are cleaned by hotel staff during
   * guest's stay. Schedule may vary from daily, weekly, or specific days of the
   * week.
   *
   * @param bool $housekeepingAvailable
   */
  public function setHousekeepingAvailable($housekeepingAvailable)
  {
    $this->housekeepingAvailable = $housekeepingAvailable;
  }
  /**
   * @return bool
   */
  public function getHousekeepingAvailable()
  {
    return $this->housekeepingAvailable;
  }
  /**
   * Housekeeping available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HOUSEKEEPING_AVAILABLE_EXCEPTION_* $housekeepingAvailableException
   */
  public function setHousekeepingAvailableException($housekeepingAvailableException)
  {
    $this->housekeepingAvailableException = $housekeepingAvailableException;
  }
  /**
   * @return self::HOUSEKEEPING_AVAILABLE_EXCEPTION_*
   */
  public function getHousekeepingAvailableException()
  {
    return $this->housekeepingAvailableException;
  }
  /**
   * Turndown service. Hotel staff enters guest units to prepare the bed for
   * sleep use. May or may not include some light housekeeping. May or may not
   * include an evening snack or candy. Also known as evening service.
   *
   * @param bool $turndownService
   */
  public function setTurndownService($turndownService)
  {
    $this->turndownService = $turndownService;
  }
  /**
   * @return bool
   */
  public function getTurndownService()
  {
    return $this->turndownService;
  }
  /**
   * Turndown service exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::TURNDOWN_SERVICE_EXCEPTION_* $turndownServiceException
   */
  public function setTurndownServiceException($turndownServiceException)
  {
    $this->turndownServiceException = $turndownServiceException;
  }
  /**
   * @return self::TURNDOWN_SERVICE_EXCEPTION_*
   */
  public function getTurndownServiceException()
  {
    return $this->turndownServiceException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Housekeeping::class, 'Google_Service_MyBusinessLodging_Housekeeping');
