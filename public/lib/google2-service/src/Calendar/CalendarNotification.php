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

namespace Google\Service\Calendar;

class CalendarNotification extends \Google\Model
{
  /**
   * The method used to deliver the notification. The possible value is: -
   * "email" - Notifications are sent via email.   Required when adding a
   * notification.
   *
   * @var string
   */
  public $method;
  /**
   * The type of notification. Possible values are: - "eventCreation" -
   * Notification sent when a new event is put on the calendar.  - "eventChange"
   * - Notification sent when an event is changed.  - "eventCancellation" -
   * Notification sent when an event is cancelled.  - "eventResponse" -
   * Notification sent when an attendee responds to the event invitation.  -
   * "agenda" - An agenda with the events of the day (sent out in the morning).
   * Required when adding a notification.
   *
   * @var string
   */
  public $type;

  /**
   * The method used to deliver the notification. The possible value is: -
   * "email" - Notifications are sent via email.   Required when adding a
   * notification.
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * The type of notification. Possible values are: - "eventCreation" -
   * Notification sent when a new event is put on the calendar.  - "eventChange"
   * - Notification sent when an event is changed.  - "eventCancellation" -
   * Notification sent when an event is cancelled.  - "eventResponse" -
   * Notification sent when an attendee responds to the event invitation.  -
   * "agenda" - An agenda with the events of the day (sent out in the morning).
   * Required when adding a notification.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarNotification::class, 'Google_Service_Calendar_CalendarNotification');
