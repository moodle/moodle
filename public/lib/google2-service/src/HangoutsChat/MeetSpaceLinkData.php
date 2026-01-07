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

class MeetSpaceLinkData extends \Google\Model
{
  /**
   * Default value for the enum. Don't use.
   */
  public const HUDDLE_STATUS_HUDDLE_STATUS_UNSPECIFIED = 'HUDDLE_STATUS_UNSPECIFIED';
  /**
   * The huddle has started.
   */
  public const HUDDLE_STATUS_STARTED = 'STARTED';
  /**
   * The huddle has ended. In this case the Meet space URI and identifiers will
   * no longer be valid.
   */
  public const HUDDLE_STATUS_ENDED = 'ENDED';
  /**
   * The huddle has been missed. In this case the Meet space URI and identifiers
   * will no longer be valid.
   */
  public const HUDDLE_STATUS_MISSED = 'MISSED';
  /**
   * Default value for the enum. Don't use.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The Meet space is a meeting.
   */
  public const TYPE_MEETING = 'MEETING';
  /**
   * The Meet space is a huddle.
   */
  public const TYPE_HUDDLE = 'HUDDLE';
  /**
   * Optional. Output only. If the Meet is a Huddle, indicates the status of the
   * huddle. Otherwise, this is unset.
   *
   * @var string
   */
  public $huddleStatus;
  /**
   * Meeting code of the linked Meet space.
   *
   * @var string
   */
  public $meetingCode;
  /**
   * Indicates the type of the Meet space.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Output only. If the Meet is a Huddle, indicates the status of the
   * huddle. Otherwise, this is unset.
   *
   * Accepted values: HUDDLE_STATUS_UNSPECIFIED, STARTED, ENDED, MISSED
   *
   * @param self::HUDDLE_STATUS_* $huddleStatus
   */
  public function setHuddleStatus($huddleStatus)
  {
    $this->huddleStatus = $huddleStatus;
  }
  /**
   * @return self::HUDDLE_STATUS_*
   */
  public function getHuddleStatus()
  {
    return $this->huddleStatus;
  }
  /**
   * Meeting code of the linked Meet space.
   *
   * @param string $meetingCode
   */
  public function setMeetingCode($meetingCode)
  {
    $this->meetingCode = $meetingCode;
  }
  /**
   * @return string
   */
  public function getMeetingCode()
  {
    return $this->meetingCode;
  }
  /**
   * Indicates the type of the Meet space.
   *
   * Accepted values: TYPE_UNSPECIFIED, MEETING, HUDDLE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MeetSpaceLinkData::class, 'Google_Service_HangoutsChat_MeetSpaceLinkData');
