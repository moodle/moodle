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

class EnterpriseTopazSidekickFindMeetingTimeCardProto extends \Google\Collection
{
  protected $collection_key = 'skippedInvitees';
  protected $commonAvailableTimeSlotsType = EnterpriseTopazSidekickTimeSlot::class;
  protected $commonAvailableTimeSlotsDataType = 'array';
  protected $inviteesType = EnterpriseTopazSidekickPerson::class;
  protected $inviteesDataType = 'array';
  protected $requesterType = EnterpriseTopazSidekickPerson::class;
  protected $requesterDataType = '';
  protected $scheduledMeetingType = EnterpriseTopazSidekickScheduledMeeting::class;
  protected $scheduledMeetingDataType = '';
  protected $skippedInviteesType = EnterpriseTopazSidekickPerson::class;
  protected $skippedInviteesDataType = 'array';
  protected $timeBoundariesType = EnterpriseTopazSidekickTimeSlot::class;
  protected $timeBoundariesDataType = '';
  /**
   * Timezone ID.
   *
   * @var string
   */
  public $timezoneId;

  /**
   * Slots when all attendees have availability.
   *
   * @param EnterpriseTopazSidekickTimeSlot[] $commonAvailableTimeSlots
   */
  public function setCommonAvailableTimeSlots($commonAvailableTimeSlots)
  {
    $this->commonAvailableTimeSlots = $commonAvailableTimeSlots;
  }
  /**
   * @return EnterpriseTopazSidekickTimeSlot[]
   */
  public function getCommonAvailableTimeSlots()
  {
    return $this->commonAvailableTimeSlots;
  }
  /**
   * Invitees to the event.
   *
   * @param EnterpriseTopazSidekickPerson[] $invitees
   */
  public function setInvitees($invitees)
  {
    $this->invitees = $invitees;
  }
  /**
   * @return EnterpriseTopazSidekickPerson[]
   */
  public function getInvitees()
  {
    return $this->invitees;
  }
  /**
   * Requester.
   *
   * @param EnterpriseTopazSidekickPerson $requester
   */
  public function setRequester(EnterpriseTopazSidekickPerson $requester)
  {
    $this->requester = $requester;
  }
  /**
   * @return EnterpriseTopazSidekickPerson
   */
  public function getRequester()
  {
    return $this->requester;
  }
  /**
   * Details about the scheduled meeting, if one exists.
   *
   * @param EnterpriseTopazSidekickScheduledMeeting $scheduledMeeting
   */
  public function setScheduledMeeting(EnterpriseTopazSidekickScheduledMeeting $scheduledMeeting)
  {
    $this->scheduledMeeting = $scheduledMeeting;
  }
  /**
   * @return EnterpriseTopazSidekickScheduledMeeting
   */
  public function getScheduledMeeting()
  {
    return $this->scheduledMeeting;
  }
  /**
   * Invitees that have been skipped in the computation, most likely because
   * they are groups.
   *
   * @param EnterpriseTopazSidekickPerson[] $skippedInvitees
   */
  public function setSkippedInvitees($skippedInvitees)
  {
    $this->skippedInvitees = $skippedInvitees;
  }
  /**
   * @return EnterpriseTopazSidekickPerson[]
   */
  public function getSkippedInvitees()
  {
    return $this->skippedInvitees;
  }
  /**
   * Min and max timestamp used to find a common available timeslot.
   *
   * @param EnterpriseTopazSidekickTimeSlot $timeBoundaries
   */
  public function setTimeBoundaries(EnterpriseTopazSidekickTimeSlot $timeBoundaries)
  {
    $this->timeBoundaries = $timeBoundaries;
  }
  /**
   * @return EnterpriseTopazSidekickTimeSlot
   */
  public function getTimeBoundaries()
  {
    return $this->timeBoundaries;
  }
  /**
   * Timezone ID.
   *
   * @param string $timezoneId
   */
  public function setTimezoneId($timezoneId)
  {
    $this->timezoneId = $timezoneId;
  }
  /**
   * @return string
   */
  public function getTimezoneId()
  {
    return $this->timezoneId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickFindMeetingTimeCardProto::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickFindMeetingTimeCardProto');
