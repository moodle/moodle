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

class Policies extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ALL_INCLUSIVE_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ALL_INCLUSIVE_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ALL_INCLUSIVE_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ALL_INCLUSIVE_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ALL_INCLUSIVE_ONLY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ALL_INCLUSIVE_ONLY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ALL_INCLUSIVE_ONLY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ALL_INCLUSIVE_ONLY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CHECKIN_TIME_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CHECKIN_TIME_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CHECKIN_TIME_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CHECKIN_TIME_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CHECKOUT_TIME_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CHECKOUT_TIME_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CHECKOUT_TIME_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CHECKOUT_TIME_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const KIDS_STAY_FREE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const KIDS_STAY_FREE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const KIDS_STAY_FREE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const KIDS_STAY_FREE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MAX_CHILD_AGE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MAX_CHILD_AGE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MAX_CHILD_AGE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MAX_CHILD_AGE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MAX_KIDS_STAY_FREE_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MAX_KIDS_STAY_FREE_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MAX_KIDS_STAY_FREE_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MAX_KIDS_STAY_FREE_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const SMOKE_FREE_PROPERTY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const SMOKE_FREE_PROPERTY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const SMOKE_FREE_PROPERTY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const SMOKE_FREE_PROPERTY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * All inclusive available. The hotel offers a rate option that includes the
   * cost of the room, meals, activities, and other amenities that might
   * otherwise be charged separately.
   *
   * @var bool
   */
  public $allInclusiveAvailable;
  /**
   * All inclusive available exception.
   *
   * @var string
   */
  public $allInclusiveAvailableException;
  /**
   * All inclusive only. The only rate option offered by the hotel is a rate
   * that includes the cost of the room, meals, activities and other amenities
   * that might otherwise be charged separately.
   *
   * @var bool
   */
  public $allInclusiveOnly;
  /**
   * All inclusive only exception.
   *
   * @var string
   */
  public $allInclusiveOnlyException;
  protected $checkinTimeType = TimeOfDay::class;
  protected $checkinTimeDataType = '';
  /**
   * Check-in time exception.
   *
   * @var string
   */
  public $checkinTimeException;
  protected $checkoutTimeType = TimeOfDay::class;
  protected $checkoutTimeDataType = '';
  /**
   * Check-out time exception.
   *
   * @var string
   */
  public $checkoutTimeException;
  /**
   * Kids stay free. The children of guests are allowed to stay in the
   * room/suite of a parent or adult without an additional fee. The policy may
   * or may not stipulate a limit of the child's age or the overall number of
   * children allowed.
   *
   * @var bool
   */
  public $kidsStayFree;
  /**
   * Kids stay free exception.
   *
   * @var string
   */
  public $kidsStayFreeException;
  /**
   * Max child age. The hotel allows children up to a certain age to stay in the
   * room/suite of a parent or adult without an additional fee.
   *
   * @var int
   */
  public $maxChildAge;
  /**
   * Max child age exception.
   *
   * @var string
   */
  public $maxChildAgeException;
  /**
   * Max kids stay free count. The hotel allows a specific, defined number of
   * children to stay in the room/suite of a parent or adult without an
   * additional fee.
   *
   * @var int
   */
  public $maxKidsStayFreeCount;
  /**
   * Max kids stay free count exception.
   *
   * @var string
   */
  public $maxKidsStayFreeCountException;
  protected $paymentOptionsType = PaymentOptions::class;
  protected $paymentOptionsDataType = '';
  /**
   * Smoke free property. Smoking is not allowed inside the building, on
   * balconies, or in outside spaces. Hotels that offer a designated area for
   * guests to smoke are not considered smoke-free properties.
   *
   * @var bool
   */
  public $smokeFreeProperty;
  /**
   * Smoke free property exception.
   *
   * @var string
   */
  public $smokeFreePropertyException;

  /**
   * All inclusive available. The hotel offers a rate option that includes the
   * cost of the room, meals, activities, and other amenities that might
   * otherwise be charged separately.
   *
   * @param bool $allInclusiveAvailable
   */
  public function setAllInclusiveAvailable($allInclusiveAvailable)
  {
    $this->allInclusiveAvailable = $allInclusiveAvailable;
  }
  /**
   * @return bool
   */
  public function getAllInclusiveAvailable()
  {
    return $this->allInclusiveAvailable;
  }
  /**
   * All inclusive available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ALL_INCLUSIVE_AVAILABLE_EXCEPTION_* $allInclusiveAvailableException
   */
  public function setAllInclusiveAvailableException($allInclusiveAvailableException)
  {
    $this->allInclusiveAvailableException = $allInclusiveAvailableException;
  }
  /**
   * @return self::ALL_INCLUSIVE_AVAILABLE_EXCEPTION_*
   */
  public function getAllInclusiveAvailableException()
  {
    return $this->allInclusiveAvailableException;
  }
  /**
   * All inclusive only. The only rate option offered by the hotel is a rate
   * that includes the cost of the room, meals, activities and other amenities
   * that might otherwise be charged separately.
   *
   * @param bool $allInclusiveOnly
   */
  public function setAllInclusiveOnly($allInclusiveOnly)
  {
    $this->allInclusiveOnly = $allInclusiveOnly;
  }
  /**
   * @return bool
   */
  public function getAllInclusiveOnly()
  {
    return $this->allInclusiveOnly;
  }
  /**
   * All inclusive only exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ALL_INCLUSIVE_ONLY_EXCEPTION_* $allInclusiveOnlyException
   */
  public function setAllInclusiveOnlyException($allInclusiveOnlyException)
  {
    $this->allInclusiveOnlyException = $allInclusiveOnlyException;
  }
  /**
   * @return self::ALL_INCLUSIVE_ONLY_EXCEPTION_*
   */
  public function getAllInclusiveOnlyException()
  {
    return $this->allInclusiveOnlyException;
  }
  /**
   * Check-in time. The time of the day at which the hotel begins providing
   * guests access to their unit at the beginning of their stay.
   *
   * @param TimeOfDay $checkinTime
   */
  public function setCheckinTime(TimeOfDay $checkinTime)
  {
    $this->checkinTime = $checkinTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getCheckinTime()
  {
    return $this->checkinTime;
  }
  /**
   * Check-in time exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CHECKIN_TIME_EXCEPTION_* $checkinTimeException
   */
  public function setCheckinTimeException($checkinTimeException)
  {
    $this->checkinTimeException = $checkinTimeException;
  }
  /**
   * @return self::CHECKIN_TIME_EXCEPTION_*
   */
  public function getCheckinTimeException()
  {
    return $this->checkinTimeException;
  }
  /**
   * Check-out time. The time of the day on the last day of a guest's reserved
   * stay at which the guest must vacate their room and settle their bill. Some
   * hotels may offer late or early check out for a fee.
   *
   * @param TimeOfDay $checkoutTime
   */
  public function setCheckoutTime(TimeOfDay $checkoutTime)
  {
    $this->checkoutTime = $checkoutTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getCheckoutTime()
  {
    return $this->checkoutTime;
  }
  /**
   * Check-out time exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CHECKOUT_TIME_EXCEPTION_* $checkoutTimeException
   */
  public function setCheckoutTimeException($checkoutTimeException)
  {
    $this->checkoutTimeException = $checkoutTimeException;
  }
  /**
   * @return self::CHECKOUT_TIME_EXCEPTION_*
   */
  public function getCheckoutTimeException()
  {
    return $this->checkoutTimeException;
  }
  /**
   * Kids stay free. The children of guests are allowed to stay in the
   * room/suite of a parent or adult without an additional fee. The policy may
   * or may not stipulate a limit of the child's age or the overall number of
   * children allowed.
   *
   * @param bool $kidsStayFree
   */
  public function setKidsStayFree($kidsStayFree)
  {
    $this->kidsStayFree = $kidsStayFree;
  }
  /**
   * @return bool
   */
  public function getKidsStayFree()
  {
    return $this->kidsStayFree;
  }
  /**
   * Kids stay free exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::KIDS_STAY_FREE_EXCEPTION_* $kidsStayFreeException
   */
  public function setKidsStayFreeException($kidsStayFreeException)
  {
    $this->kidsStayFreeException = $kidsStayFreeException;
  }
  /**
   * @return self::KIDS_STAY_FREE_EXCEPTION_*
   */
  public function getKidsStayFreeException()
  {
    return $this->kidsStayFreeException;
  }
  /**
   * Max child age. The hotel allows children up to a certain age to stay in the
   * room/suite of a parent or adult without an additional fee.
   *
   * @param int $maxChildAge
   */
  public function setMaxChildAge($maxChildAge)
  {
    $this->maxChildAge = $maxChildAge;
  }
  /**
   * @return int
   */
  public function getMaxChildAge()
  {
    return $this->maxChildAge;
  }
  /**
   * Max child age exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MAX_CHILD_AGE_EXCEPTION_* $maxChildAgeException
   */
  public function setMaxChildAgeException($maxChildAgeException)
  {
    $this->maxChildAgeException = $maxChildAgeException;
  }
  /**
   * @return self::MAX_CHILD_AGE_EXCEPTION_*
   */
  public function getMaxChildAgeException()
  {
    return $this->maxChildAgeException;
  }
  /**
   * Max kids stay free count. The hotel allows a specific, defined number of
   * children to stay in the room/suite of a parent or adult without an
   * additional fee.
   *
   * @param int $maxKidsStayFreeCount
   */
  public function setMaxKidsStayFreeCount($maxKidsStayFreeCount)
  {
    $this->maxKidsStayFreeCount = $maxKidsStayFreeCount;
  }
  /**
   * @return int
   */
  public function getMaxKidsStayFreeCount()
  {
    return $this->maxKidsStayFreeCount;
  }
  /**
   * Max kids stay free count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MAX_KIDS_STAY_FREE_COUNT_EXCEPTION_* $maxKidsStayFreeCountException
   */
  public function setMaxKidsStayFreeCountException($maxKidsStayFreeCountException)
  {
    $this->maxKidsStayFreeCountException = $maxKidsStayFreeCountException;
  }
  /**
   * @return self::MAX_KIDS_STAY_FREE_COUNT_EXCEPTION_*
   */
  public function getMaxKidsStayFreeCountException()
  {
    return $this->maxKidsStayFreeCountException;
  }
  /**
   * Forms of payment accepted at the property.
   *
   * @param PaymentOptions $paymentOptions
   */
  public function setPaymentOptions(PaymentOptions $paymentOptions)
  {
    $this->paymentOptions = $paymentOptions;
  }
  /**
   * @return PaymentOptions
   */
  public function getPaymentOptions()
  {
    return $this->paymentOptions;
  }
  /**
   * Smoke free property. Smoking is not allowed inside the building, on
   * balconies, or in outside spaces. Hotels that offer a designated area for
   * guests to smoke are not considered smoke-free properties.
   *
   * @param bool $smokeFreeProperty
   */
  public function setSmokeFreeProperty($smokeFreeProperty)
  {
    $this->smokeFreeProperty = $smokeFreeProperty;
  }
  /**
   * @return bool
   */
  public function getSmokeFreeProperty()
  {
    return $this->smokeFreeProperty;
  }
  /**
   * Smoke free property exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::SMOKE_FREE_PROPERTY_EXCEPTION_* $smokeFreePropertyException
   */
  public function setSmokeFreePropertyException($smokeFreePropertyException)
  {
    $this->smokeFreePropertyException = $smokeFreePropertyException;
  }
  /**
   * @return self::SMOKE_FREE_PROPERTY_EXCEPTION_*
   */
  public function getSmokeFreePropertyException()
  {
    return $this->smokeFreePropertyException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policies::class, 'Google_Service_MyBusinessLodging_Policies');
