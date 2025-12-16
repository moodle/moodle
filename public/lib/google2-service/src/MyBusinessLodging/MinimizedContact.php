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

class MinimizedContact extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const CONTACTLESS_CHECKIN_CHECKOUT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const CONTACTLESS_CHECKIN_CHECKOUT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const CONTACTLESS_CHECKIN_CHECKOUT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const CONTACTLESS_CHECKIN_CHECKOUT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const DIGITAL_GUEST_ROOM_KEYS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const DIGITAL_GUEST_ROOM_KEYS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const DIGITAL_GUEST_ROOM_KEYS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const DIGITAL_GUEST_ROOM_KEYS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const HOUSEKEEPING_SCHEDULED_REQUEST_ONLY_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const HOUSEKEEPING_SCHEDULED_REQUEST_ONLY_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const HOUSEKEEPING_SCHEDULED_REQUEST_ONLY_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const HOUSEKEEPING_SCHEDULED_REQUEST_ONLY_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const NO_HIGH_TOUCH_ITEMS_COMMON_AREAS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const NO_HIGH_TOUCH_ITEMS_COMMON_AREAS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const NO_HIGH_TOUCH_ITEMS_COMMON_AREAS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const NO_HIGH_TOUCH_ITEMS_COMMON_AREAS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const NO_HIGH_TOUCH_ITEMS_GUEST_ROOMS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const NO_HIGH_TOUCH_ITEMS_GUEST_ROOMS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const NO_HIGH_TOUCH_ITEMS_GUEST_ROOMS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const NO_HIGH_TOUCH_ITEMS_GUEST_ROOMS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const PLASTIC_KEYCARDS_DISINFECTED_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const PLASTIC_KEYCARDS_DISINFECTED_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const PLASTIC_KEYCARDS_DISINFECTED_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const PLASTIC_KEYCARDS_DISINFECTED_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const ROOM_BOOKINGS_BUFFER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const ROOM_BOOKINGS_BUFFER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const ROOM_BOOKINGS_BUFFER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const ROOM_BOOKINGS_BUFFER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * No-contact check-in and check-out.
   *
   * @var bool
   */
  public $contactlessCheckinCheckout;
  /**
   * Contactless check-in check-out exception.
   *
   * @var string
   */
  public $contactlessCheckinCheckoutException;
  /**
   * Keyless mobile entry to guest rooms.
   *
   * @var bool
   */
  public $digitalGuestRoomKeys;
  /**
   * Digital guest room keys exception.
   *
   * @var string
   */
  public $digitalGuestRoomKeysException;
  /**
   * Housekeeping scheduled by request only.
   *
   * @var bool
   */
  public $housekeepingScheduledRequestOnly;
  /**
   * Housekeeping scheduled request only exception.
   *
   * @var string
   */
  public $housekeepingScheduledRequestOnlyException;
  /**
   * High-touch items, such as magazines, removed from common areas.
   *
   * @var bool
   */
  public $noHighTouchItemsCommonAreas;
  /**
   * No high touch items common areas exception.
   *
   * @var string
   */
  public $noHighTouchItemsCommonAreasException;
  /**
   * High-touch items, such as decorative pillows, removed from guest rooms.
   *
   * @var bool
   */
  public $noHighTouchItemsGuestRooms;
  /**
   * No high touch items guest rooms exception.
   *
   * @var string
   */
  public $noHighTouchItemsGuestRoomsException;
  /**
   * Plastic key cards are disinfected or discarded.
   *
   * @var bool
   */
  public $plasticKeycardsDisinfected;
  /**
   * Plastic keycards disinfected exception.
   *
   * @var string
   */
  public $plasticKeycardsDisinfectedException;
  /**
   * Buffer maintained between room bookings.
   *
   * @var bool
   */
  public $roomBookingsBuffer;
  /**
   * Room bookings buffer exception.
   *
   * @var string
   */
  public $roomBookingsBufferException;

  /**
   * No-contact check-in and check-out.
   *
   * @param bool $contactlessCheckinCheckout
   */
  public function setContactlessCheckinCheckout($contactlessCheckinCheckout)
  {
    $this->contactlessCheckinCheckout = $contactlessCheckinCheckout;
  }
  /**
   * @return bool
   */
  public function getContactlessCheckinCheckout()
  {
    return $this->contactlessCheckinCheckout;
  }
  /**
   * Contactless check-in check-out exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::CONTACTLESS_CHECKIN_CHECKOUT_EXCEPTION_* $contactlessCheckinCheckoutException
   */
  public function setContactlessCheckinCheckoutException($contactlessCheckinCheckoutException)
  {
    $this->contactlessCheckinCheckoutException = $contactlessCheckinCheckoutException;
  }
  /**
   * @return self::CONTACTLESS_CHECKIN_CHECKOUT_EXCEPTION_*
   */
  public function getContactlessCheckinCheckoutException()
  {
    return $this->contactlessCheckinCheckoutException;
  }
  /**
   * Keyless mobile entry to guest rooms.
   *
   * @param bool $digitalGuestRoomKeys
   */
  public function setDigitalGuestRoomKeys($digitalGuestRoomKeys)
  {
    $this->digitalGuestRoomKeys = $digitalGuestRoomKeys;
  }
  /**
   * @return bool
   */
  public function getDigitalGuestRoomKeys()
  {
    return $this->digitalGuestRoomKeys;
  }
  /**
   * Digital guest room keys exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::DIGITAL_GUEST_ROOM_KEYS_EXCEPTION_* $digitalGuestRoomKeysException
   */
  public function setDigitalGuestRoomKeysException($digitalGuestRoomKeysException)
  {
    $this->digitalGuestRoomKeysException = $digitalGuestRoomKeysException;
  }
  /**
   * @return self::DIGITAL_GUEST_ROOM_KEYS_EXCEPTION_*
   */
  public function getDigitalGuestRoomKeysException()
  {
    return $this->digitalGuestRoomKeysException;
  }
  /**
   * Housekeeping scheduled by request only.
   *
   * @param bool $housekeepingScheduledRequestOnly
   */
  public function setHousekeepingScheduledRequestOnly($housekeepingScheduledRequestOnly)
  {
    $this->housekeepingScheduledRequestOnly = $housekeepingScheduledRequestOnly;
  }
  /**
   * @return bool
   */
  public function getHousekeepingScheduledRequestOnly()
  {
    return $this->housekeepingScheduledRequestOnly;
  }
  /**
   * Housekeeping scheduled request only exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::HOUSEKEEPING_SCHEDULED_REQUEST_ONLY_EXCEPTION_* $housekeepingScheduledRequestOnlyException
   */
  public function setHousekeepingScheduledRequestOnlyException($housekeepingScheduledRequestOnlyException)
  {
    $this->housekeepingScheduledRequestOnlyException = $housekeepingScheduledRequestOnlyException;
  }
  /**
   * @return self::HOUSEKEEPING_SCHEDULED_REQUEST_ONLY_EXCEPTION_*
   */
  public function getHousekeepingScheduledRequestOnlyException()
  {
    return $this->housekeepingScheduledRequestOnlyException;
  }
  /**
   * High-touch items, such as magazines, removed from common areas.
   *
   * @param bool $noHighTouchItemsCommonAreas
   */
  public function setNoHighTouchItemsCommonAreas($noHighTouchItemsCommonAreas)
  {
    $this->noHighTouchItemsCommonAreas = $noHighTouchItemsCommonAreas;
  }
  /**
   * @return bool
   */
  public function getNoHighTouchItemsCommonAreas()
  {
    return $this->noHighTouchItemsCommonAreas;
  }
  /**
   * No high touch items common areas exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::NO_HIGH_TOUCH_ITEMS_COMMON_AREAS_EXCEPTION_* $noHighTouchItemsCommonAreasException
   */
  public function setNoHighTouchItemsCommonAreasException($noHighTouchItemsCommonAreasException)
  {
    $this->noHighTouchItemsCommonAreasException = $noHighTouchItemsCommonAreasException;
  }
  /**
   * @return self::NO_HIGH_TOUCH_ITEMS_COMMON_AREAS_EXCEPTION_*
   */
  public function getNoHighTouchItemsCommonAreasException()
  {
    return $this->noHighTouchItemsCommonAreasException;
  }
  /**
   * High-touch items, such as decorative pillows, removed from guest rooms.
   *
   * @param bool $noHighTouchItemsGuestRooms
   */
  public function setNoHighTouchItemsGuestRooms($noHighTouchItemsGuestRooms)
  {
    $this->noHighTouchItemsGuestRooms = $noHighTouchItemsGuestRooms;
  }
  /**
   * @return bool
   */
  public function getNoHighTouchItemsGuestRooms()
  {
    return $this->noHighTouchItemsGuestRooms;
  }
  /**
   * No high touch items guest rooms exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::NO_HIGH_TOUCH_ITEMS_GUEST_ROOMS_EXCEPTION_* $noHighTouchItemsGuestRoomsException
   */
  public function setNoHighTouchItemsGuestRoomsException($noHighTouchItemsGuestRoomsException)
  {
    $this->noHighTouchItemsGuestRoomsException = $noHighTouchItemsGuestRoomsException;
  }
  /**
   * @return self::NO_HIGH_TOUCH_ITEMS_GUEST_ROOMS_EXCEPTION_*
   */
  public function getNoHighTouchItemsGuestRoomsException()
  {
    return $this->noHighTouchItemsGuestRoomsException;
  }
  /**
   * Plastic key cards are disinfected or discarded.
   *
   * @param bool $plasticKeycardsDisinfected
   */
  public function setPlasticKeycardsDisinfected($plasticKeycardsDisinfected)
  {
    $this->plasticKeycardsDisinfected = $plasticKeycardsDisinfected;
  }
  /**
   * @return bool
   */
  public function getPlasticKeycardsDisinfected()
  {
    return $this->plasticKeycardsDisinfected;
  }
  /**
   * Plastic keycards disinfected exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::PLASTIC_KEYCARDS_DISINFECTED_EXCEPTION_* $plasticKeycardsDisinfectedException
   */
  public function setPlasticKeycardsDisinfectedException($plasticKeycardsDisinfectedException)
  {
    $this->plasticKeycardsDisinfectedException = $plasticKeycardsDisinfectedException;
  }
  /**
   * @return self::PLASTIC_KEYCARDS_DISINFECTED_EXCEPTION_*
   */
  public function getPlasticKeycardsDisinfectedException()
  {
    return $this->plasticKeycardsDisinfectedException;
  }
  /**
   * Buffer maintained between room bookings.
   *
   * @param bool $roomBookingsBuffer
   */
  public function setRoomBookingsBuffer($roomBookingsBuffer)
  {
    $this->roomBookingsBuffer = $roomBookingsBuffer;
  }
  /**
   * @return bool
   */
  public function getRoomBookingsBuffer()
  {
    return $this->roomBookingsBuffer;
  }
  /**
   * Room bookings buffer exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::ROOM_BOOKINGS_BUFFER_EXCEPTION_* $roomBookingsBufferException
   */
  public function setRoomBookingsBufferException($roomBookingsBufferException)
  {
    $this->roomBookingsBufferException = $roomBookingsBufferException;
  }
  /**
   * @return self::ROOM_BOOKINGS_BUFFER_EXCEPTION_*
   */
  public function getRoomBookingsBufferException()
  {
    return $this->roomBookingsBufferException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MinimizedContact::class, 'Google_Service_MyBusinessLodging_MinimizedContact');
