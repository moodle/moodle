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

class CalendarListEntry extends \Google\Collection
{
  protected $collection_key = 'defaultReminders';
  /**
   * The effective access role that the authenticated user has on the calendar.
   * Read-only. Possible values are: - "freeBusyReader" - Provides read access
   * to free/busy information.  - "reader" - Provides read access to the
   * calendar. Private events will appear to users with reader access, but event
   * details will be hidden.  - "writer" - Provides read and write access to the
   * calendar. Private events will appear to users with writer access, and event
   * details will be visible.  - "owner" - Provides manager access to the
   * calendar. This role has all of the permissions of the writer role with the
   * additional ability to see and modify access levels of other users.
   * Important: the owner role is different from the calendar's data owner. A
   * calendar has a single data owner, but can have multiple users with owner
   * role.
   *
   * @var string
   */
  public $accessRole;
  /**
   * Whether this calendar automatically accepts invitations. Only valid for
   * resource calendars. Read-only.
   *
   * @var bool
   */
  public $autoAcceptInvitations;
  /**
   * The main color of the calendar in the hexadecimal format "#0088aa". This
   * property supersedes the index-based colorId property. To set or change this
   * property, you need to specify colorRgbFormat=true in the parameters of the
   * insert, update and patch methods. Optional.
   *
   * @var string
   */
  public $backgroundColor;
  /**
   * The color of the calendar. This is an ID referring to an entry in the
   * calendar section of the colors definition (see the colors endpoint). This
   * property is superseded by the backgroundColor and foregroundColor
   * properties and can be ignored when using these properties. Optional.
   *
   * @var string
   */
  public $colorId;
  protected $conferencePropertiesType = ConferenceProperties::class;
  protected $conferencePropertiesDataType = '';
  /**
   * The email of the owner of the calendar. Set only for secondary calendars.
   * Read-only.
   *
   * @var string
   */
  public $dataOwner;
  protected $defaultRemindersType = EventReminder::class;
  protected $defaultRemindersDataType = 'array';
  /**
   * Whether this calendar list entry has been deleted from the calendar list.
   * Read-only. Optional. The default is False.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Description of the calendar. Optional. Read-only.
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
   * The foreground color of the calendar in the hexadecimal format "#ffffff".
   * This property supersedes the index-based colorId property. To set or change
   * this property, you need to specify colorRgbFormat=true in the parameters of
   * the insert, update and patch methods. Optional.
   *
   * @var string
   */
  public $foregroundColor;
  /**
   * Whether the calendar has been hidden from the list. Optional. The attribute
   * is only returned when the calendar is hidden, in which case the value is
   * true.
   *
   * @var bool
   */
  public $hidden;
  /**
   * Identifier of the calendar.
   *
   * @var string
   */
  public $id;
  /**
   * Type of the resource ("calendar#calendarListEntry").
   *
   * @var string
   */
  public $kind;
  /**
   * Geographic location of the calendar as free-form text. Optional. Read-only.
   *
   * @var string
   */
  public $location;
  protected $notificationSettingsType = CalendarListEntryNotificationSettings::class;
  protected $notificationSettingsDataType = '';
  /**
   * Whether the calendar is the primary calendar of the authenticated user.
   * Read-only. Optional. The default is False.
   *
   * @var bool
   */
  public $primary;
  /**
   * Whether the calendar content shows up in the calendar UI. Optional. The
   * default is False.
   *
   * @var bool
   */
  public $selected;
  /**
   * Title of the calendar. Read-only.
   *
   * @var string
   */
  public $summary;
  /**
   * The summary that the authenticated user has set for this calendar.
   * Optional.
   *
   * @var string
   */
  public $summaryOverride;
  /**
   * The time zone of the calendar. Optional. Read-only.
   *
   * @var string
   */
  public $timeZone;

  /**
   * The effective access role that the authenticated user has on the calendar.
   * Read-only. Possible values are: - "freeBusyReader" - Provides read access
   * to free/busy information.  - "reader" - Provides read access to the
   * calendar. Private events will appear to users with reader access, but event
   * details will be hidden.  - "writer" - Provides read and write access to the
   * calendar. Private events will appear to users with writer access, and event
   * details will be visible.  - "owner" - Provides manager access to the
   * calendar. This role has all of the permissions of the writer role with the
   * additional ability to see and modify access levels of other users.
   * Important: the owner role is different from the calendar's data owner. A
   * calendar has a single data owner, but can have multiple users with owner
   * role.
   *
   * @param string $accessRole
   */
  public function setAccessRole($accessRole)
  {
    $this->accessRole = $accessRole;
  }
  /**
   * @return string
   */
  public function getAccessRole()
  {
    return $this->accessRole;
  }
  /**
   * Whether this calendar automatically accepts invitations. Only valid for
   * resource calendars. Read-only.
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
   * The main color of the calendar in the hexadecimal format "#0088aa". This
   * property supersedes the index-based colorId property. To set or change this
   * property, you need to specify colorRgbFormat=true in the parameters of the
   * insert, update and patch methods. Optional.
   *
   * @param string $backgroundColor
   */
  public function setBackgroundColor($backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return string
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * The color of the calendar. This is an ID referring to an entry in the
   * calendar section of the colors definition (see the colors endpoint). This
   * property is superseded by the backgroundColor and foregroundColor
   * properties and can be ignored when using these properties. Optional.
   *
   * @param string $colorId
   */
  public function setColorId($colorId)
  {
    $this->colorId = $colorId;
  }
  /**
   * @return string
   */
  public function getColorId()
  {
    return $this->colorId;
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
   * The default reminders that the authenticated user has for this calendar.
   *
   * @param EventReminder[] $defaultReminders
   */
  public function setDefaultReminders($defaultReminders)
  {
    $this->defaultReminders = $defaultReminders;
  }
  /**
   * @return EventReminder[]
   */
  public function getDefaultReminders()
  {
    return $this->defaultReminders;
  }
  /**
   * Whether this calendar list entry has been deleted from the calendar list.
   * Read-only. Optional. The default is False.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Description of the calendar. Optional. Read-only.
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
   * The foreground color of the calendar in the hexadecimal format "#ffffff".
   * This property supersedes the index-based colorId property. To set or change
   * this property, you need to specify colorRgbFormat=true in the parameters of
   * the insert, update and patch methods. Optional.
   *
   * @param string $foregroundColor
   */
  public function setForegroundColor($foregroundColor)
  {
    $this->foregroundColor = $foregroundColor;
  }
  /**
   * @return string
   */
  public function getForegroundColor()
  {
    return $this->foregroundColor;
  }
  /**
   * Whether the calendar has been hidden from the list. Optional. The attribute
   * is only returned when the calendar is hidden, in which case the value is
   * true.
   *
   * @param bool $hidden
   */
  public function setHidden($hidden)
  {
    $this->hidden = $hidden;
  }
  /**
   * @return bool
   */
  public function getHidden()
  {
    return $this->hidden;
  }
  /**
   * Identifier of the calendar.
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
   * Type of the resource ("calendar#calendarListEntry").
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
   * Geographic location of the calendar as free-form text. Optional. Read-only.
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
   * The notifications that the authenticated user is receiving for this
   * calendar.
   *
   * @param CalendarListEntryNotificationSettings $notificationSettings
   */
  public function setNotificationSettings(CalendarListEntryNotificationSettings $notificationSettings)
  {
    $this->notificationSettings = $notificationSettings;
  }
  /**
   * @return CalendarListEntryNotificationSettings
   */
  public function getNotificationSettings()
  {
    return $this->notificationSettings;
  }
  /**
   * Whether the calendar is the primary calendar of the authenticated user.
   * Read-only. Optional. The default is False.
   *
   * @param bool $primary
   */
  public function setPrimary($primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return bool
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * Whether the calendar content shows up in the calendar UI. Optional. The
   * default is False.
   *
   * @param bool $selected
   */
  public function setSelected($selected)
  {
    $this->selected = $selected;
  }
  /**
   * @return bool
   */
  public function getSelected()
  {
    return $this->selected;
  }
  /**
   * Title of the calendar. Read-only.
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
   * The summary that the authenticated user has set for this calendar.
   * Optional.
   *
   * @param string $summaryOverride
   */
  public function setSummaryOverride($summaryOverride)
  {
    $this->summaryOverride = $summaryOverride;
  }
  /**
   * @return string
   */
  public function getSummaryOverride()
  {
    return $this->summaryOverride;
  }
  /**
   * The time zone of the calendar. Optional. Read-only.
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
class_alias(CalendarListEntry::class, 'Google_Service_Calendar_CalendarListEntry');
