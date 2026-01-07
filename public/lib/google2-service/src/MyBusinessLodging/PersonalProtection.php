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

class PersonalProtection extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const COMMON_AREAS_OFFER_SANITIZING_ITEMS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const COMMON_AREAS_OFFER_SANITIZING_ITEMS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const COMMON_AREAS_OFFER_SANITIZING_ITEMS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const COMMON_AREAS_OFFER_SANITIZING_ITEMS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const FACE_MASK_REQUIRED_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const FACE_MASK_REQUIRED_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const FACE_MASK_REQUIRED_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const FACE_MASK_REQUIRED_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const GUEST_ROOM_HYGIENE_KITS_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const GUEST_ROOM_HYGIENE_KITS_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const GUEST_ROOM_HYGIENE_KITS_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const GUEST_ROOM_HYGIENE_KITS_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PROTECTIVE_EQUIPMENT_AVAILABLE_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PROTECTIVE_EQUIPMENT_AVAILABLE_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PROTECTIVE_EQUIPMENT_AVAILABLE_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PROTECTIVE_EQUIPMENT_AVAILABLE_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Hand-sanitizer and/or sanitizing wipes are offered in common areas.
   *
   * @var bool
   */
  public $commonAreasOfferSanitizingItems;
  /**
   * Common areas offer sanitizing items exception.
   *
   * @var string
   */
  public $commonAreasOfferSanitizingItemsException;
  /**
   * Masks required on the property.
   *
   * @var bool
   */
  public $faceMaskRequired;
  /**
   * Face mask required exception.
   *
   * @var string
   */
  public $faceMaskRequiredException;
  /**
   * In-room hygiene kits with masks, hand sanitizer, and/or antibacterial
   * wipes.
   *
   * @var bool
   */
  public $guestRoomHygieneKitsAvailable;
  /**
   * Guest room hygiene kits available exception.
   *
   * @var string
   */
  public $guestRoomHygieneKitsAvailableException;
  /**
   * Masks and/or gloves available for guests.
   *
   * @var bool
   */
  public $protectiveEquipmentAvailable;
  /**
   * Protective equipment available exception.
   *
   * @var string
   */
  public $protectiveEquipmentAvailableException;

  /**
   * Hand-sanitizer and/or sanitizing wipes are offered in common areas.
   *
   * @param bool $commonAreasOfferSanitizingItems
   */
  public function setCommonAreasOfferSanitizingItems($commonAreasOfferSanitizingItems)
  {
    $this->commonAreasOfferSanitizingItems = $commonAreasOfferSanitizingItems;
  }
  /**
   * @return bool
   */
  public function getCommonAreasOfferSanitizingItems()
  {
    return $this->commonAreasOfferSanitizingItems;
  }
  /**
   * Common areas offer sanitizing items exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::COMMON_AREAS_OFFER_SANITIZING_ITEMS_EXCEPTION_* $commonAreasOfferSanitizingItemsException
   */
  public function setCommonAreasOfferSanitizingItemsException($commonAreasOfferSanitizingItemsException)
  {
    $this->commonAreasOfferSanitizingItemsException = $commonAreasOfferSanitizingItemsException;
  }
  /**
   * @return self::COMMON_AREAS_OFFER_SANITIZING_ITEMS_EXCEPTION_*
   */
  public function getCommonAreasOfferSanitizingItemsException()
  {
    return $this->commonAreasOfferSanitizingItemsException;
  }
  /**
   * Masks required on the property.
   *
   * @param bool $faceMaskRequired
   */
  public function setFaceMaskRequired($faceMaskRequired)
  {
    $this->faceMaskRequired = $faceMaskRequired;
  }
  /**
   * @return bool
   */
  public function getFaceMaskRequired()
  {
    return $this->faceMaskRequired;
  }
  /**
   * Face mask required exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::FACE_MASK_REQUIRED_EXCEPTION_* $faceMaskRequiredException
   */
  public function setFaceMaskRequiredException($faceMaskRequiredException)
  {
    $this->faceMaskRequiredException = $faceMaskRequiredException;
  }
  /**
   * @return self::FACE_MASK_REQUIRED_EXCEPTION_*
   */
  public function getFaceMaskRequiredException()
  {
    return $this->faceMaskRequiredException;
  }
  /**
   * In-room hygiene kits with masks, hand sanitizer, and/or antibacterial
   * wipes.
   *
   * @param bool $guestRoomHygieneKitsAvailable
   */
  public function setGuestRoomHygieneKitsAvailable($guestRoomHygieneKitsAvailable)
  {
    $this->guestRoomHygieneKitsAvailable = $guestRoomHygieneKitsAvailable;
  }
  /**
   * @return bool
   */
  public function getGuestRoomHygieneKitsAvailable()
  {
    return $this->guestRoomHygieneKitsAvailable;
  }
  /**
   * Guest room hygiene kits available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::GUEST_ROOM_HYGIENE_KITS_AVAILABLE_EXCEPTION_* $guestRoomHygieneKitsAvailableException
   */
  public function setGuestRoomHygieneKitsAvailableException($guestRoomHygieneKitsAvailableException)
  {
    $this->guestRoomHygieneKitsAvailableException = $guestRoomHygieneKitsAvailableException;
  }
  /**
   * @return self::GUEST_ROOM_HYGIENE_KITS_AVAILABLE_EXCEPTION_*
   */
  public function getGuestRoomHygieneKitsAvailableException()
  {
    return $this->guestRoomHygieneKitsAvailableException;
  }
  /**
   * Masks and/or gloves available for guests.
   *
   * @param bool $protectiveEquipmentAvailable
   */
  public function setProtectiveEquipmentAvailable($protectiveEquipmentAvailable)
  {
    $this->protectiveEquipmentAvailable = $protectiveEquipmentAvailable;
  }
  /**
   * @return bool
   */
  public function getProtectiveEquipmentAvailable()
  {
    return $this->protectiveEquipmentAvailable;
  }
  /**
   * Protective equipment available exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PROTECTIVE_EQUIPMENT_AVAILABLE_EXCEPTION_* $protectiveEquipmentAvailableException
   */
  public function setProtectiveEquipmentAvailableException($protectiveEquipmentAvailableException)
  {
    $this->protectiveEquipmentAvailableException = $protectiveEquipmentAvailableException;
  }
  /**
   * @return self::PROTECTIVE_EQUIPMENT_AVAILABLE_EXCEPTION_*
   */
  public function getProtectiveEquipmentAvailableException()
  {
    return $this->protectiveEquipmentAvailableException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersonalProtection::class, 'Google_Service_MyBusinessLodging_PersonalProtection');
