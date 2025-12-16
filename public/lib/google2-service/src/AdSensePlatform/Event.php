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

namespace Google\Service\AdSensePlatform;

class Event extends \Google\Model
{
  /**
   * Do not use. You must set an event type explicitly.
   */
  public const EVENT_TYPE_EVENT_TYPE_UNSPECIFIED = 'EVENT_TYPE_UNSPECIFIED';
  /**
   * Log in via platform.
   */
  public const EVENT_TYPE_LOG_IN_VIA_PLATFORM = 'LOG_IN_VIA_PLATFORM';
  /**
   * Sign up via platform.
   */
  public const EVENT_TYPE_SIGN_UP_VIA_PLATFORM = 'SIGN_UP_VIA_PLATFORM';
  protected $eventInfoType = EventInfo::class;
  protected $eventInfoDataType = '';
  /**
   * Required. Event timestamp.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Required. Event type.
   *
   * @var string
   */
  public $eventType;

  /**
   * Required. Information associated with the event.
   *
   * @param EventInfo $eventInfo
   */
  public function setEventInfo(EventInfo $eventInfo)
  {
    $this->eventInfo = $eventInfo;
  }
  /**
   * @return EventInfo
   */
  public function getEventInfo()
  {
    return $this->eventInfo;
  }
  /**
   * Required. Event timestamp.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Required. Event type.
   *
   * Accepted values: EVENT_TYPE_UNSPECIFIED, LOG_IN_VIA_PLATFORM,
   * SIGN_UP_VIA_PLATFORM
   *
   * @param self::EVENT_TYPE_* $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return self::EVENT_TYPE_*
   */
  public function getEventType()
  {
    return $this->eventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Event::class, 'Google_Service_AdSensePlatform_Event');
