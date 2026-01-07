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

class Events extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * The user's access role for this calendar. Read-only. Possible values are: -
   * "none" - The user has no access.  - "freeBusyReader" - The user has read
   * access to free/busy information.  - "reader" - The user has read access to
   * the calendar. Private events will appear to users with reader access, but
   * event details will be hidden.  - "writer" - The user has read and write
   * access to the calendar. Private events will appear to users with writer
   * access, and event details will be visible.  - "owner" - The user has
   * manager access to the calendar. This role has all of the permissions of the
   * writer role with the additional ability to see and modify access levels of
   * other users. Important: the owner role is different from the calendar's
   * data owner. A calendar has a single data owner, but can have multiple users
   * with owner role.
   *
   * @var string
   */
  public $accessRole;
  protected $defaultRemindersType = EventReminder::class;
  protected $defaultRemindersDataType = 'array';
  /**
   * Description of the calendar. Read-only.
   *
   * @var string
   */
  public $description;
  /**
   * ETag of the collection.
   *
   * @var string
   */
  public $etag;
  protected $itemsType = Event::class;
  protected $itemsDataType = 'array';
  /**
   * Type of the collection ("calendar#events").
   *
   * @var string
   */
  public $kind;
  /**
   * Token used to access the next page of this result. Omitted if no further
   * results are available, in which case nextSyncToken is provided.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Token used at a later point in time to retrieve only the entries that have
   * changed since this result was returned. Omitted if further results are
   * available, in which case nextPageToken is provided.
   *
   * @var string
   */
  public $nextSyncToken;
  /**
   * Title of the calendar. Read-only.
   *
   * @var string
   */
  public $summary;
  /**
   * The time zone of the calendar. Read-only.
   *
   * @var string
   */
  public $timeZone;
  /**
   * Last modification time of the calendar (as a RFC3339 timestamp). Read-only.
   *
   * @var string
   */
  public $updated;

  /**
   * The user's access role for this calendar. Read-only. Possible values are: -
   * "none" - The user has no access.  - "freeBusyReader" - The user has read
   * access to free/busy information.  - "reader" - The user has read access to
   * the calendar. Private events will appear to users with reader access, but
   * event details will be hidden.  - "writer" - The user has read and write
   * access to the calendar. Private events will appear to users with writer
   * access, and event details will be visible.  - "owner" - The user has
   * manager access to the calendar. This role has all of the permissions of the
   * writer role with the additional ability to see and modify access levels of
   * other users. Important: the owner role is different from the calendar's
   * data owner. A calendar has a single data owner, but can have multiple users
   * with owner role.
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
   * The default reminders on the calendar for the authenticated user. These
   * reminders apply to all events on this calendar that do not explicitly
   * override them (i.e. do not have reminders.useDefault set to True).
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
   * Description of the calendar. Read-only.
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
   * ETag of the collection.
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
   * List of events on the calendar.
   *
   * @param Event[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Event[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Type of the collection ("calendar#events").
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
   * Token used to access the next page of this result. Omitted if no further
   * results are available, in which case nextSyncToken is provided.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Token used at a later point in time to retrieve only the entries that have
   * changed since this result was returned. Omitted if further results are
   * available, in which case nextPageToken is provided.
   *
   * @param string $nextSyncToken
   */
  public function setNextSyncToken($nextSyncToken)
  {
    $this->nextSyncToken = $nextSyncToken;
  }
  /**
   * @return string
   */
  public function getNextSyncToken()
  {
    return $this->nextSyncToken;
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
   * The time zone of the calendar. Read-only.
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
   * Last modification time of the calendar (as a RFC3339 timestamp). Read-only.
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Events::class, 'Google_Service_Calendar_Events');
