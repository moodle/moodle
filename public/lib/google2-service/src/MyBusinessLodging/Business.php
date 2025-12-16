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

class Business extends \Google\Model
{
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const BUSINESS_CENTER_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const BUSINESS_CENTER_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const BUSINESS_CENTER_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const BUSINESS_CENTER_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MEETING_ROOMS_COUNT_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MEETING_ROOMS_COUNT_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MEETING_ROOMS_COUNT_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MEETING_ROOMS_COUNT_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Default unspecified exception. Use this only if a more specific exception
   * does not match.
   */
  public const MEETING_ROOMS_EXCEPTION_EXCEPTION_UNSPECIFIED = 'EXCEPTION_UNSPECIFIED';
  /**
   * Amenity or service is unavailable due to ongoing work orders.
   */
  public const MEETING_ROOMS_EXCEPTION_UNDER_CONSTRUCTION = 'UNDER_CONSTRUCTION';
  /**
   * Amenity or service availability is seasonal.
   */
  public const MEETING_ROOMS_EXCEPTION_DEPENDENT_ON_SEASON = 'DEPENDENT_ON_SEASON';
  /**
   * Amenity or service availability depends on the day of the week.
   */
  public const MEETING_ROOMS_EXCEPTION_DEPENDENT_ON_DAY_OF_WEEK = 'DEPENDENT_ON_DAY_OF_WEEK';
  /**
   * Business center. A designated room at the hotel with one or more desks and
   * equipped with guest-use computers, printers, fax machines and/or
   * photocopiers. May or may not be open 24/7. May or may not require a key to
   * access. Not a meeting room or conference room.
   *
   * @var bool
   */
  public $businessCenter;
  /**
   * Business center exception.
   *
   * @var string
   */
  public $businessCenterException;
  /**
   * Meeting rooms. Rooms at the hotel designated for business-related
   * gatherings. Rooms are usually equipped with tables or desks, office chairs
   * and audio/visual facilities to allow for presentations and conference
   * calls. Also known as conference rooms.
   *
   * @var bool
   */
  public $meetingRooms;
  /**
   * Meeting rooms count. The number of meeting rooms at the property.
   *
   * @var int
   */
  public $meetingRoomsCount;
  /**
   * Meeting rooms count exception.
   *
   * @var string
   */
  public $meetingRoomsCountException;
  /**
   * Meeting rooms exception.
   *
   * @var string
   */
  public $meetingRoomsException;

  /**
   * Business center. A designated room at the hotel with one or more desks and
   * equipped with guest-use computers, printers, fax machines and/or
   * photocopiers. May or may not be open 24/7. May or may not require a key to
   * access. Not a meeting room or conference room.
   *
   * @param bool $businessCenter
   */
  public function setBusinessCenter($businessCenter)
  {
    $this->businessCenter = $businessCenter;
  }
  /**
   * @return bool
   */
  public function getBusinessCenter()
  {
    return $this->businessCenter;
  }
  /**
   * Business center exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::BUSINESS_CENTER_EXCEPTION_* $businessCenterException
   */
  public function setBusinessCenterException($businessCenterException)
  {
    $this->businessCenterException = $businessCenterException;
  }
  /**
   * @return self::BUSINESS_CENTER_EXCEPTION_*
   */
  public function getBusinessCenterException()
  {
    return $this->businessCenterException;
  }
  /**
   * Meeting rooms. Rooms at the hotel designated for business-related
   * gatherings. Rooms are usually equipped with tables or desks, office chairs
   * and audio/visual facilities to allow for presentations and conference
   * calls. Also known as conference rooms.
   *
   * @param bool $meetingRooms
   */
  public function setMeetingRooms($meetingRooms)
  {
    $this->meetingRooms = $meetingRooms;
  }
  /**
   * @return bool
   */
  public function getMeetingRooms()
  {
    return $this->meetingRooms;
  }
  /**
   * Meeting rooms count. The number of meeting rooms at the property.
   *
   * @param int $meetingRoomsCount
   */
  public function setMeetingRoomsCount($meetingRoomsCount)
  {
    $this->meetingRoomsCount = $meetingRoomsCount;
  }
  /**
   * @return int
   */
  public function getMeetingRoomsCount()
  {
    return $this->meetingRoomsCount;
  }
  /**
   * Meeting rooms count exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MEETING_ROOMS_COUNT_EXCEPTION_* $meetingRoomsCountException
   */
  public function setMeetingRoomsCountException($meetingRoomsCountException)
  {
    $this->meetingRoomsCountException = $meetingRoomsCountException;
  }
  /**
   * @return self::MEETING_ROOMS_COUNT_EXCEPTION_*
   */
  public function getMeetingRoomsCountException()
  {
    return $this->meetingRoomsCountException;
  }
  /**
   * Meeting rooms exception.
   *
   * Accepted values: EXCEPTION_UNSPECIFIED, UNDER_CONSTRUCTION,
   * DEPENDENT_ON_SEASON, DEPENDENT_ON_DAY_OF_WEEK
   *
   * @param self::MEETING_ROOMS_EXCEPTION_* $meetingRoomsException
   */
  public function setMeetingRoomsException($meetingRoomsException)
  {
    $this->meetingRoomsException = $meetingRoomsException;
  }
  /**
   * @return self::MEETING_ROOMS_EXCEPTION_*
   */
  public function getMeetingRoomsException()
  {
    return $this->meetingRoomsException;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Business::class, 'Google_Service_MyBusinessLodging_Business');
