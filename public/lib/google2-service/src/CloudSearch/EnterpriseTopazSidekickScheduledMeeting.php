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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickScheduledMeeting extends \Google\Model
{
  /**
   * The meeting location.
   *
   * @var string
   */
  public $meetingLocation;
  protected $meetingTimeType = EnterpriseTopazSidekickTimeSlot::class;
  protected $meetingTimeDataType = '';
  /**
   * The meeting title.
   *
   * @var string
   */
  public $meetingTitle;

  /**
   * The meeting location.
   *
   * @param string $meetingLocation
   */
  public function setMeetingLocation($meetingLocation)
  {
    $this->meetingLocation = $meetingLocation;
  }
  /**
   * @return string
   */
  public function getMeetingLocation()
  {
    return $this->meetingLocation;
  }
  /**
   * The meeting time slot.
   *
   * @param EnterpriseTopazSidekickTimeSlot $meetingTime
   */
  public function setMeetingTime(EnterpriseTopazSidekickTimeSlot $meetingTime)
  {
    $this->meetingTime = $meetingTime;
  }
  /**
   * @return EnterpriseTopazSidekickTimeSlot
   */
  public function getMeetingTime()
  {
    return $this->meetingTime;
  }
  /**
   * The meeting title.
   *
   * @param string $meetingTitle
   */
  public function setMeetingTitle($meetingTitle)
  {
    $this->meetingTitle = $meetingTitle;
  }
  /**
   * @return string
   */
  public function getMeetingTitle()
  {
    return $this->meetingTitle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickScheduledMeeting::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickScheduledMeeting');
