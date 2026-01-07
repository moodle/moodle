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

namespace Google\Service\HangoutsChat;

class CalendarEventLinkData extends \Google\Model
{
  /**
   * The [Calendar identifier](https://developers.google.com/workspace/calendar/
   * api/v3/reference/calendars) of the linked Calendar.
   *
   * @var string
   */
  public $calendarId;
  /**
   * The [Event identifier](https://developers.google.com/workspace/calendar/api
   * /v3/reference/events) of the linked Calendar event.
   *
   * @var string
   */
  public $eventId;

  /**
   * The [Calendar identifier](https://developers.google.com/workspace/calendar/
   * api/v3/reference/calendars) of the linked Calendar.
   *
   * @param string $calendarId
   */
  public function setCalendarId($calendarId)
  {
    $this->calendarId = $calendarId;
  }
  /**
   * @return string
   */
  public function getCalendarId()
  {
    return $this->calendarId;
  }
  /**
   * The [Event identifier](https://developers.google.com/workspace/calendar/api
   * /v3/reference/events) of the linked Calendar event.
   *
   * @param string $eventId
   */
  public function setEventId($eventId)
  {
    $this->eventId = $eventId;
  }
  /**
   * @return string
   */
  public function getEventId()
  {
    return $this->eventId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarEventLinkData::class, 'Google_Service_HangoutsChat_CalendarEventLinkData');
