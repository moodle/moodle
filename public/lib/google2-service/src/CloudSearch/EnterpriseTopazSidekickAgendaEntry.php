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

class EnterpriseTopazSidekickAgendaEntry extends \Google\Collection
{
  /**
   * Stale.
   */
  public const CHRONOLOGY_STALE = 'STALE';
  /**
   * All day.
   */
  public const CHRONOLOGY_ALL_DAY = 'ALL_DAY';
  /**
   * Past.
   */
  public const CHRONOLOGY_PAST = 'PAST';
  /**
   * Recently past.
   */
  public const CHRONOLOGY_RECENTLY_PAST = 'RECENTLY_PAST';
  /**
   * Present.
   */
  public const CHRONOLOGY_PRESENT = 'PRESENT';
  /**
   * Near future.
   */
  public const CHRONOLOGY_NEAR_FUTURE = 'NEAR_FUTURE';
  /**
   * Future.
   */
  public const CHRONOLOGY_FUTURE = 'FUTURE';
  /**
   * Awaiting for the user to set the status.
   */
  public const CURRENT_USER_ATTENDING_STATUS_AWAITING = 'AWAITING';
  /**
   * Attending.
   */
  public const CURRENT_USER_ATTENDING_STATUS_YES = 'YES';
  /**
   * Not attending.
   */
  public const CURRENT_USER_ATTENDING_STATUS_NO = 'NO';
  /**
   * Tentatively attending.
   */
  public const CURRENT_USER_ATTENDING_STATUS_MAYBE = 'MAYBE';
  protected $collection_key = 'invitee';
  /**
   * URL of the agenda item.
   *
   * @var string
   */
  public $agendaItemUrl;
  /**
   * The chronology from the present.
   *
   * @var string
   */
  public $chronology;
  protected $creatorType = EnterpriseTopazSidekickPerson::class;
  protected $creatorDataType = '';
  /**
   * Attendance status for the current user making the request. This is a
   * convenience data member in order to avoid figuring out the same by
   * iterating the invitee list above on the caller side.
   *
   * @var string
   */
  public $currentUserAttendingStatus;
  /**
   * Description of the agenda item (i.e., typically, summary in calendar
   * event).
   *
   * @var string
   */
  public $description;
  protected $documentType = EnterpriseTopazSidekickCommonDocument::class;
  protected $documentDataType = 'array';
  /**
   * End date "Friday, August 26" in the user's timezone.
   *
   * @var string
   */
  public $endDate;
  /**
   * End time (HH:mm) in the user's timezone.
   *
   * @var string
   */
  public $endTime;
  /**
   * End time in milliseconds
   *
   * @var string
   */
  public $endTimeMs;
  /**
   * Event id provided by Calendar API.
   *
   * @var string
   */
  public $eventId;
  /**
   * Whether the guests can invite other guests.
   *
   * @var bool
   */
  public $guestsCanInviteOthers;
  /**
   * Whether the guests can modify the event.
   *
   * @var bool
   */
  public $guestsCanModify;
  /**
   * Whether the guests of the event can be seen. If false, the user is going to
   * be reported as the only attendee to the meeting, even though there may be
   * more attendees.
   *
   * @var bool
   */
  public $guestsCanSeeGuests;
  /**
   * Hangout meeting identifier.
   *
   * @var string
   */
  public $hangoutId;
  /**
   * Absolute URL for the Hangout meeting.
   *
   * @var string
   */
  public $hangoutUrl;
  protected $inviteeType = EnterpriseTopazSidekickPerson::class;
  protected $inviteeDataType = 'array';
  /**
   * Whether the entry lasts all day.
   *
   * @var bool
   */
  public $isAllDay;
  /**
   * Last time the event was modified.
   *
   * @var string
   */
  public $lastModificationTimeMs;
  /**
   * Agenda item location.
   *
   * @var string
   */
  public $location;
  /**
   * Whether this should be notified to the user.
   *
   * @var bool
   */
  public $notifyToUser;
  /**
   * Whether guest list is not returned because number of attendees is too
   * large.
   *
   * @var bool
   */
  public $otherAttendeesExcluded;
  /**
   * Whether the requester is the owner of the agenda entry.
   *
   * @var bool
   */
  public $requesterIsOwner;
  /**
   * Whether the details of this entry should be displayed to the user.
   *
   * @var bool
   */
  public $showFullEventDetailsToUse;
  /**
   * Start date "Friday, August 26" in the user's timezone.
   *
   * @var string
   */
  public $startDate;
  /**
   * Start time (HH:mm) in the user's timezone.
   *
   * @var string
   */
  public $startTime;
  /**
   * Start time in milliseconds.
   *
   * @var string
   */
  public $startTimeMs;
  /**
   * User's calendar timezone;
   *
   * @var string
   */
  public $timeZone;
  /**
   * Title of the agenda item.
   *
   * @var string
   */
  public $title;

  /**
   * URL of the agenda item.
   *
   * @param string $agendaItemUrl
   */
  public function setAgendaItemUrl($agendaItemUrl)
  {
    $this->agendaItemUrl = $agendaItemUrl;
  }
  /**
   * @return string
   */
  public function getAgendaItemUrl()
  {
    return $this->agendaItemUrl;
  }
  /**
   * The chronology from the present.
   *
   * Accepted values: STALE, ALL_DAY, PAST, RECENTLY_PAST, PRESENT, NEAR_FUTURE,
   * FUTURE
   *
   * @param self::CHRONOLOGY_* $chronology
   */
  public function setChronology($chronology)
  {
    $this->chronology = $chronology;
  }
  /**
   * @return self::CHRONOLOGY_*
   */
  public function getChronology()
  {
    return $this->chronology;
  }
  /**
   * Person who created the event.
   *
   * @param EnterpriseTopazSidekickPerson $creator
   */
  public function setCreator(EnterpriseTopazSidekickPerson $creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return EnterpriseTopazSidekickPerson
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Attendance status for the current user making the request. This is a
   * convenience data member in order to avoid figuring out the same by
   * iterating the invitee list above on the caller side.
   *
   * Accepted values: AWAITING, YES, NO, MAYBE
   *
   * @param self::CURRENT_USER_ATTENDING_STATUS_* $currentUserAttendingStatus
   */
  public function setCurrentUserAttendingStatus($currentUserAttendingStatus)
  {
    $this->currentUserAttendingStatus = $currentUserAttendingStatus;
  }
  /**
   * @return self::CURRENT_USER_ATTENDING_STATUS_*
   */
  public function getCurrentUserAttendingStatus()
  {
    return $this->currentUserAttendingStatus;
  }
  /**
   * Description of the agenda item (i.e., typically, summary in calendar
   * event).
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Items related to the current AgendaEntry. E.g., related drive/mail/groups
   * documents.
   *
   * @param EnterpriseTopazSidekickCommonDocument[] $document
   */
  public function setDocument($document)
  {
    $this->document = $document;
  }
  /**
   * @return EnterpriseTopazSidekickCommonDocument[]
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * End date "Friday, August 26" in the user's timezone.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * End time (HH:mm) in the user's timezone.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * End time in milliseconds
   *
   * @param string $endTimeMs
   */
  public function setEndTimeMs($endTimeMs)
  {
    $this->endTimeMs = $endTimeMs;
  }
  /**
   * @return string
   */
  public function getEndTimeMs()
  {
    return $this->endTimeMs;
  }
  /**
   * Event id provided by Calendar API.
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
  /**
   * Whether the guests can invite other guests.
   *
   * @param bool $guestsCanInviteOthers
   */
  public function setGuestsCanInviteOthers($guestsCanInviteOthers)
  {
    $this->guestsCanInviteOthers = $guestsCanInviteOthers;
  }
  /**
   * @return bool
   */
  public function getGuestsCanInviteOthers()
  {
    return $this->guestsCanInviteOthers;
  }
  /**
   * Whether the guests can modify the event.
   *
   * @param bool $guestsCanModify
   */
  public function setGuestsCanModify($guestsCanModify)
  {
    $this->guestsCanModify = $guestsCanModify;
  }
  /**
   * @return bool
   */
  public function getGuestsCanModify()
  {
    return $this->guestsCanModify;
  }
  /**
   * Whether the guests of the event can be seen. If false, the user is going to
   * be reported as the only attendee to the meeting, even though there may be
   * more attendees.
   *
   * @param bool $guestsCanSeeGuests
   */
  public function setGuestsCanSeeGuests($guestsCanSeeGuests)
  {
    $this->guestsCanSeeGuests = $guestsCanSeeGuests;
  }
  /**
   * @return bool
   */
  public function getGuestsCanSeeGuests()
  {
    return $this->guestsCanSeeGuests;
  }
  /**
   * Hangout meeting identifier.
   *
   * @param string $hangoutId
   */
  public function setHangoutId($hangoutId)
  {
    $this->hangoutId = $hangoutId;
  }
  /**
   * @return string
   */
  public function getHangoutId()
  {
    return $this->hangoutId;
  }
  /**
   * Absolute URL for the Hangout meeting.
   *
   * @param string $hangoutUrl
   */
  public function setHangoutUrl($hangoutUrl)
  {
    $this->hangoutUrl = $hangoutUrl;
  }
  /**
   * @return string
   */
  public function getHangoutUrl()
  {
    return $this->hangoutUrl;
  }
  /**
   * People attending the meeting.
   *
   * @param EnterpriseTopazSidekickPerson[] $invitee
   */
  public function setInvitee($invitee)
  {
    $this->invitee = $invitee;
  }
  /**
   * @return EnterpriseTopazSidekickPerson[]
   */
  public function getInvitee()
  {
    return $this->invitee;
  }
  /**
   * Whether the entry lasts all day.
   *
   * @param bool $isAllDay
   */
  public function setIsAllDay($isAllDay)
  {
    $this->isAllDay = $isAllDay;
  }
  /**
   * @return bool
   */
  public function getIsAllDay()
  {
    return $this->isAllDay;
  }
  /**
   * Last time the event was modified.
   *
   * @param string $lastModificationTimeMs
   */
  public function setLastModificationTimeMs($lastModificationTimeMs)
  {
    $this->lastModificationTimeMs = $lastModificationTimeMs;
  }
  /**
   * @return string
   */
  public function getLastModificationTimeMs()
  {
    return $this->lastModificationTimeMs;
  }
  /**
   * Agenda item location.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Whether this should be notified to the user.
   *
   * @param bool $notifyToUser
   */
  public function setNotifyToUser($notifyToUser)
  {
    $this->notifyToUser = $notifyToUser;
  }
  /**
   * @return bool
   */
  public function getNotifyToUser()
  {
    return $this->notifyToUser;
  }
  /**
   * Whether guest list is not returned because number of attendees is too
   * large.
   *
   * @param bool $otherAttendeesExcluded
   */
  public function setOtherAttendeesExcluded($otherAttendeesExcluded)
  {
    $this->otherAttendeesExcluded = $otherAttendeesExcluded;
  }
  /**
   * @return bool
   */
  public function getOtherAttendeesExcluded()
  {
    return $this->otherAttendeesExcluded;
  }
  /**
   * Whether the requester is the owner of the agenda entry.
   *
   * @param bool $requesterIsOwner
   */
  public function setRequesterIsOwner($requesterIsOwner)
  {
    $this->requesterIsOwner = $requesterIsOwner;
  }
  /**
   * @return bool
   */
  public function getRequesterIsOwner()
  {
    return $this->requesterIsOwner;
  }
  /**
   * Whether the details of this entry should be displayed to the user.
   *
   * @param bool $showFullEventDetailsToUse
   */
  public function setShowFullEventDetailsToUse($showFullEventDetailsToUse)
  {
    $this->showFullEventDetailsToUse = $showFullEventDetailsToUse;
  }
  /**
   * @return bool
   */
  public function getShowFullEventDetailsToUse()
  {
    return $this->showFullEventDetailsToUse;
  }
  /**
   * Start date "Friday, August 26" in the user's timezone.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Start time (HH:mm) in the user's timezone.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Start time in milliseconds.
   *
   * @param string $startTimeMs
   */
  public function setStartTimeMs($startTimeMs)
  {
    $this->startTimeMs = $startTimeMs;
  }
  /**
   * @return string
   */
  public function getStartTimeMs()
  {
    return $this->startTimeMs;
  }
  /**
   * User's calendar timezone;
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Title of the agenda item.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickAgendaEntry::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickAgendaEntry');
