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

class Calendar extends \Google\Model
{
  /**
   * Whether this calendar automatically accepts invitations. Only valid for
   * resource calendars.
   *
   * @var bool
   */
  public $autoAcceptInvitations;
  protected $conferencePropertiesType = ConferenceProperties::class;
  protected $conferencePropertiesDataType = '';
  /**
   * The email of the owner of the calendar. Set only for secondary calendars.
   * Read-only.
   *
   * @var string
   */
  public $dataOwner;
  /**
   * Description of the calendar. Optional.
   *
   * @var string
   */
  public $description;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Identifier of the calendar. To retrieve IDs call the calendarList.list()
   * method.
   *
   * @var string
   */
  public $id;
  /**
   * Type of the resource ("calendar#calendar").
   *
   * @var string
   */
  public $kind;
  /**
   * Geographic location of the calendar as free-form text. Optional.
   *
   * @var string
   */
  public $location;
  /**
   * Title of the calendar.
   *
   * @var string
   */
  public $summary;
  /**
   * The time zone of the calendar. (Formatted as an IANA Time Zone Database
   * name, e.g. "Europe/Zurich".) Optional.
   *
   * @var string
   */
  public $timeZone;

  /**
   * Whether this calendar automatically accepts invitations. Only valid for
   * resource calendars.
   *
   * @param bool $autoAcceptInvitations
   */
  public function setAutoAcceptInvitations($autoAcceptInvitations)
  {
    $this->autoAcceptInvitations = $autoAcceptInvitations;
  }
  /**
   * @return bool
   */
  public function getAutoAcceptInvitations()
  {
    return $this->autoAcceptInvitations;
  }
  /**
   * Conferencing properties for this calendar, for example what types of
   * conferences are allowed.
   *
   * @param ConferenceProperties $conferenceProperties
   */
  public function setConferenceProperties(ConferenceProperties $conferenceProperties)
  {
    $this->conferenceProperties = $conferenceProperties;
  }
  /**
   * @return ConferenceProperties
   */
  public function getConferenceProperties()
  {
    return $this->conferenceProperties;
  }
  /**
   * The email of the owner of the calendar. Set only for secondary calendars.
   * Read-only.
   *
   * @param string $dataOwner
   */
  public function setDataOwner($dataOwner)
  {
    $this->dataOwner = $dataOwner;
  }
  /**
   * @return string
   */
  public function getDataOwner()
  {
    return $this->dataOwner;
  }
  /**
   * Description of the calendar. Optional.
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
   * ETag of the resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Identifier of the calendar. To retrieve IDs call the calendarList.list()
   * method.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Type of the resource ("calendar#calendar").
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Geographic location of the calendar as free-form text. Optional.
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
   * Title of the calendar.
   *
   * @param string $summary
   */
  public function setSummary($summary)
  {
    $this->summary = $summary;
  }
  /**
   * @return string
   */
  public function getSummary()
  {
    return $this->summary;
  }
  /**
   * The time zone of the calendar. (Formatted as an IANA Time Zone Database
   * name, e.g. "Europe/Zurich".) Optional.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Calendar::class, 'Google_Service_Calendar_Calendar');
