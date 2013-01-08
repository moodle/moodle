<?php
/*
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


  /**
   * The "freebusy" collection of methods.
   * Typical usage is:
   *  <code>
   *   $calendarService = new Google_CalendarService(...);
   *   $freebusy = $calendarService->freebusy;
   *  </code>
   */
  class Google_FreebusyServiceResource extends Google_ServiceResource {


    /**
     * Returns free/busy information for a set of calendars. (freebusy.query)
     *
     * @param Google_FreeBusyRequest $postBody
     * @param array $optParams Optional parameters.
     * @return Google_FreeBusyResponse
     */
    public function query(Google_FreeBusyRequest $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('query', array($params));
      if ($this->useObjects()) {
        return new Google_FreeBusyResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "settings" collection of methods.
   * Typical usage is:
   *  <code>
   *   $calendarService = new Google_CalendarService(...);
   *   $settings = $calendarService->settings;
   *  </code>
   */
  class Google_SettingsServiceResource extends Google_ServiceResource {


    /**
     * Returns all user settings for the authenticated user. (settings.list)
     *
     * @param array $optParams Optional parameters.
     * @return Google_Settings
     */
    public function listSettings($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Settings($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns a single user setting. (settings.get)
     *
     * @param string $setting Name of the user setting.
     * @param array $optParams Optional parameters.
     * @return Google_Setting
     */
    public function get($setting, $optParams = array()) {
      $params = array('setting' => $setting);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Setting($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "calendarList" collection of methods.
   * Typical usage is:
   *  <code>
   *   $calendarService = new Google_CalendarService(...);
   *   $calendarList = $calendarService->calendarList;
   *  </code>
   */
  class Google_CalendarListServiceResource extends Google_ServiceResource {


    /**
     * Adds an entry to the user's calendar list. (calendarList.insert)
     *
     * @param Google_CalendarListEntry $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool colorRgbFormat Whether to use the 'frontendColor' and 'backgroundColor' fields to write the calendar colors (RGB). If this feature is used, the index-based 'color' field will be set to the best matching option automatically. Optional. The default is False.
     * @return Google_CalendarListEntry
     */
    public function insert(Google_CalendarListEntry $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_CalendarListEntry($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns an entry on the user's calendar list. (calendarList.get)
     *
     * @param string $calendarId Calendar identifier.
     * @param array $optParams Optional parameters.
     * @return Google_CalendarListEntry
     */
    public function get($calendarId, $optParams = array()) {
      $params = array('calendarId' => $calendarId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_CalendarListEntry($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns entries on the user's calendar list. (calendarList.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string pageToken Token specifying which result page to return. Optional.
     * @opt_param bool showHidden Whether to show hidden entries. Optional. The default is False.
     * @opt_param int maxResults Maximum number of entries returned on one result page. Optional.
     * @opt_param string minAccessRole The minimum access role for the user in the returned entires. Optional. The default is no restriction.
     * @return Google_CalendarList
     */
    public function listCalendarList($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_CalendarList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an entry on the user's calendar list. (calendarList.update)
     *
     * @param string $calendarId Calendar identifier.
     * @param Google_CalendarListEntry $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool colorRgbFormat Whether to use the 'frontendColor' and 'backgroundColor' fields to write the calendar colors (RGB). If this feature is used, the index-based 'color' field will be set to the best matching option automatically. Optional. The default is False.
     * @return Google_CalendarListEntry
     */
    public function update($calendarId, Google_CalendarListEntry $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_CalendarListEntry($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an entry on the user's calendar list. This method supports patch semantics.
     * (calendarList.patch)
     *
     * @param string $calendarId Calendar identifier.
     * @param Google_CalendarListEntry $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool colorRgbFormat Whether to use the 'frontendColor' and 'backgroundColor' fields to write the calendar colors (RGB). If this feature is used, the index-based 'color' field will be set to the best matching option automatically. Optional. The default is False.
     * @return Google_CalendarListEntry
     */
    public function patch($calendarId, Google_CalendarListEntry $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_CalendarListEntry($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes an entry on the user's calendar list. (calendarList.delete)
     *
     * @param string $calendarId Calendar identifier.
     * @param array $optParams Optional parameters.
     */
    public function delete($calendarId, $optParams = array()) {
      $params = array('calendarId' => $calendarId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "calendars" collection of methods.
   * Typical usage is:
   *  <code>
   *   $calendarService = new Google_CalendarService(...);
   *   $calendars = $calendarService->calendars;
   *  </code>
   */
  class Google_CalendarsServiceResource extends Google_ServiceResource {


    /**
     * Creates a secondary calendar. (calendars.insert)
     *
     * @param Google_Calendar $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Calendar
     */
    public function insert(Google_Calendar $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Calendar($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns metadata for a calendar. (calendars.get)
     *
     * @param string $calendarId Calendar identifier.
     * @param array $optParams Optional parameters.
     * @return Google_Calendar
     */
    public function get($calendarId, $optParams = array()) {
      $params = array('calendarId' => $calendarId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Calendar($data);
      } else {
        return $data;
      }
    }
    /**
     * Clears a primary calendar. This operation deletes all data associated with the primary calendar
     * of an account and cannot be undone. (calendars.clear)
     *
     * @param string $calendarId Calendar identifier.
     * @param array $optParams Optional parameters.
     */
    public function clear($calendarId, $optParams = array()) {
      $params = array('calendarId' => $calendarId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('clear', array($params));
      return $data;
    }
    /**
     * Updates metadata for a calendar. (calendars.update)
     *
     * @param string $calendarId Calendar identifier.
     * @param Google_Calendar $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Calendar
     */
    public function update($calendarId, Google_Calendar $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Calendar($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates metadata for a calendar. This method supports patch semantics. (calendars.patch)
     *
     * @param string $calendarId Calendar identifier.
     * @param Google_Calendar $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Calendar
     */
    public function patch($calendarId, Google_Calendar $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Calendar($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes a secondary calendar. (calendars.delete)
     *
     * @param string $calendarId Calendar identifier.
     * @param array $optParams Optional parameters.
     */
    public function delete($calendarId, $optParams = array()) {
      $params = array('calendarId' => $calendarId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "acl" collection of methods.
   * Typical usage is:
   *  <code>
   *   $calendarService = new Google_CalendarService(...);
   *   $acl = $calendarService->acl;
   *  </code>
   */
  class Google_AclServiceResource extends Google_ServiceResource {


    /**
     * Creates an access control rule. (acl.insert)
     *
     * @param string $calendarId Calendar identifier.
     * @param Google_AclRule $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AclRule
     */
    public function insert($calendarId, Google_AclRule $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_AclRule($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns an access control rule. (acl.get)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $ruleId ACL rule identifier.
     * @param array $optParams Optional parameters.
     * @return Google_AclRule
     */
    public function get($calendarId, $ruleId, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'ruleId' => $ruleId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_AclRule($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the rules in the access control list for the calendar. (acl.list)
     *
     * @param string $calendarId Calendar identifier.
     * @param array $optParams Optional parameters.
     * @return Google_Acl
     */
    public function listAcl($calendarId, $optParams = array()) {
      $params = array('calendarId' => $calendarId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Acl($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an access control rule. (acl.update)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $ruleId ACL rule identifier.
     * @param Google_AclRule $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AclRule
     */
    public function update($calendarId, $ruleId, Google_AclRule $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'ruleId' => $ruleId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_AclRule($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an access control rule. This method supports patch semantics. (acl.patch)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $ruleId ACL rule identifier.
     * @param Google_AclRule $postBody
     * @param array $optParams Optional parameters.
     * @return Google_AclRule
     */
    public function patch($calendarId, $ruleId, Google_AclRule $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'ruleId' => $ruleId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_AclRule($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes an access control rule. (acl.delete)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $ruleId ACL rule identifier.
     * @param array $optParams Optional parameters.
     */
    public function delete($calendarId, $ruleId, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'ruleId' => $ruleId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "colors" collection of methods.
   * Typical usage is:
   *  <code>
   *   $calendarService = new Google_CalendarService(...);
   *   $colors = $calendarService->colors;
   *  </code>
   */
  class Google_ColorsServiceResource extends Google_ServiceResource {


    /**
     * Returns the color definitions for calendars and events. (colors.get)
     *
     * @param array $optParams Optional parameters.
     * @return Google_Colors
     */
    public function get($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Colors($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "events" collection of methods.
   * Typical usage is:
   *  <code>
   *   $calendarService = new Google_CalendarService(...);
   *   $events = $calendarService->events;
   *  </code>
   */
  class Google_EventsServiceResource extends Google_ServiceResource {


    /**
     * Creates an event. (events.insert)
     *
     * @param string $calendarId Calendar identifier.
     * @param Google_Event $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool sendNotifications Whether to send notifications about the creation of the new event. Optional. The default is False.
     * @return Google_Event
     */
    public function insert($calendarId, Google_Event $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Event($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns an event. (events.get)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $eventId Event identifier.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string timeZone Time zone used in the response. Optional. The default is the time zone of the calendar.
     * @opt_param bool alwaysIncludeEmail Whether to always include a value in the "email" field for the organizer, creator and attendees, even if no real email is available (i.e. a generated, non-working value will be provided). The use of this option is discouraged and should only be used by clients which cannot handle the absence of an email address value in the mentioned places. Optional. The default is False.
     * @opt_param int maxAttendees The maximum number of attendees to include in the response. If there are more than the specified number of attendees, only the participant is returned. Optional.
     * @return Google_Event
     */
    public function get($calendarId, $eventId, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'eventId' => $eventId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Event($data);
      } else {
        return $data;
      }
    }
    /**
     * Moves an event to another calendar, i.e. changes an event's organizer. (events.move)
     *
     * @param string $calendarId Calendar identifier of the source calendar where the event currently is on.
     * @param string $eventId Event identifier.
     * @param string $destination Calendar identifier of the target calendar where the event is to be moved to.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool sendNotifications Whether to send notifications about the change of the event's organizer. Optional. The default is False.
     * @return Google_Event
     */
    public function move($calendarId, $eventId, $destination, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'eventId' => $eventId, 'destination' => $destination);
      $params = array_merge($params, $optParams);
      $data = $this->__call('move', array($params));
      if ($this->useObjects()) {
        return new Google_Event($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns events on the specified calendar. (events.list)
     *
     * @param string $calendarId Calendar identifier.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string orderBy The order of the events returned in the result. Optional. The default is an unspecified, stable order.
     * @opt_param bool showHiddenInvitations Whether to include hidden invitations in the result. Optional. The default is False.
     * @opt_param bool showDeleted Whether to include deleted events (with 'eventStatus' equals 'cancelled') in the result. Optional. The default is False.
     * @opt_param string iCalUID Specifies iCalendar UID (iCalUID) of events to be included in the response. Optional.
     * @opt_param string updatedMin Lower bound for an event's last modification time (as a RFC 3339 timestamp) to filter by. Optional. The default is not to filter by last modification time.
     * @opt_param bool singleEvents Whether to expand recurring events into instances and only return single one-off events and instances of recurring events, but not the underlying recurring events themselves. Optional. The default is False.
     * @opt_param bool alwaysIncludeEmail Whether to always include a value in the "email" field for the organizer, creator and attendees, even if no real email is available (i.e. a generated, non-working value will be provided). The use of this option is discouraged and should only be used by clients which cannot handle the absence of an email address value in the mentioned places. Optional. The default is False.
     * @opt_param int maxResults Maximum number of events returned on one result page. Optional.
     * @opt_param string q Free text search terms to find events that match these terms in any field, except for extended properties. Optional.
     * @opt_param string pageToken Token specifying which result page to return. Optional.
     * @opt_param string timeMin Lower bound (inclusive) for an event's end time to filter by. Optional. The default is not to filter by end time.
     * @opt_param string timeZone Time zone used in the response. Optional. The default is the time zone of the calendar.
     * @opt_param string timeMax Upper bound (exclusive) for an event's start time to filter by. Optional. The default is not to filter by start time.
     * @opt_param int maxAttendees The maximum number of attendees to include in the response. If there are more than the specified number of attendees, only the participant is returned. Optional.
     * @return Google_Events
     */
    public function listEvents($calendarId, $optParams = array()) {
      $params = array('calendarId' => $calendarId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_Events($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an event. (events.update)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $eventId Event identifier.
     * @param Google_Event $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool alwaysIncludeEmail Whether to always include a value in the "email" field for the organizer, creator and attendees, even if no real email is available (i.e. a generated, non-working value will be provided). The use of this option is discouraged and should only be used by clients which cannot handle the absence of an email address value in the mentioned places. Optional. The default is False.
     * @opt_param bool sendNotifications Whether to send notifications about the event update (e.g. attendee's responses, title changes, etc.). Optional. The default is False.
     * @return Google_Event
     */
    public function update($calendarId, $eventId, Google_Event $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'eventId' => $eventId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Event($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates an event. This method supports patch semantics. (events.patch)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $eventId Event identifier.
     * @param Google_Event $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool alwaysIncludeEmail Whether to always include a value in the "email" field for the organizer, creator and attendees, even if no real email is available (i.e. a generated, non-working value will be provided). The use of this option is discouraged and should only be used by clients which cannot handle the absence of an email address value in the mentioned places. Optional. The default is False.
     * @opt_param bool sendNotifications Whether to send notifications about the event update (e.g. attendee's responses, title changes, etc.). Optional. The default is False.
     * @return Google_Event
     */
    public function patch($calendarId, $eventId, Google_Event $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'eventId' => $eventId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Event($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns instances of the specified recurring event. (events.instances)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $eventId Recurring event identifier.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool showDeleted Whether to include deleted events (with 'eventStatus' equals 'cancelled') in the result. Optional. The default is False.
     * @opt_param bool alwaysIncludeEmail Whether to always include a value in the "email" field for the organizer, creator and attendees, even if no real email is available (i.e. a generated, non-working value will be provided). The use of this option is discouraged and should only be used by clients which cannot handle the absence of an email address value in the mentioned places. Optional. The default is False.
     * @opt_param int maxResults Maximum number of events returned on one result page. Optional.
     * @opt_param string pageToken Token specifying which result page to return. Optional.
     * @opt_param string timeZone Time zone used in the response. Optional. The default is the time zone of the calendar.
     * @opt_param string originalStart The original start time of the instance in the result. Optional.
     * @opt_param int maxAttendees The maximum number of attendees to include in the response. If there are more than the specified number of attendees, only the participant is returned. Optional.
     * @return Google_Events
     */
    public function instances($calendarId, $eventId, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'eventId' => $eventId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('instances', array($params));
      if ($this->useObjects()) {
        return new Google_Events($data);
      } else {
        return $data;
      }
    }
    /**
     * Imports an event. (events.import)
     *
     * @param string $calendarId Calendar identifier.
     * @param Google_Event $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Event
     */
    public function import($calendarId, Google_Event $postBody, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('import', array($params));
      if ($this->useObjects()) {
        return new Google_Event($data);
      } else {
        return $data;
      }
    }
    /**
     * Creates an event based on a simple text string. (events.quickAdd)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $text The text describing the event to be created.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool sendNotifications Whether to send notifications about the creation of the event. Optional. The default is False.
     * @return Google_Event
     */
    public function quickAdd($calendarId, $text, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'text' => $text);
      $params = array_merge($params, $optParams);
      $data = $this->__call('quickAdd', array($params));
      if ($this->useObjects()) {
        return new Google_Event($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes an event. (events.delete)
     *
     * @param string $calendarId Calendar identifier.
     * @param string $eventId Event identifier.
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool sendNotifications Whether to send notifications about the deletion of the event. Optional. The default is False.
     */
    public function delete($calendarId, $eventId, $optParams = array()) {
      $params = array('calendarId' => $calendarId, 'eventId' => $eventId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

/**
 * Service definition for Google_Calendar (v3).
 *
 * <p>
 * Lets you manipulate events and other calendar data.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/apis/calendar/v3/using.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_CalendarService extends Google_Service {
  public $freebusy;
  public $settings;
  public $calendarList;
  public $calendars;
  public $acl;
  public $colors;
  public $events;
  /**
   * Constructs the internal representation of the Calendar service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'calendar/v3/';
    $this->version = 'v3';
    $this->serviceName = 'calendar';

    $client->addService($this->serviceName, $this->version);
    $this->freebusy = new Google_FreebusyServiceResource($this, $this->serviceName, 'freebusy', json_decode('{"methods": {"query": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "request": {"$ref": "FreeBusyRequest"}, "response": {"$ref": "FreeBusyResponse"}, "httpMethod": "POST", "path": "freeBusy", "id": "calendar.freebusy.query"}}}', true));
    $this->settings = new Google_SettingsServiceResource($this, $this->serviceName, 'settings', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "path": "users/me/settings", "response": {"$ref": "Settings"}, "id": "calendar.settings.list", "httpMethod": "GET"}, "get": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "parameters": {"setting": {"required": true, "type": "string", "location": "path"}}, "id": "calendar.settings.get", "httpMethod": "GET", "path": "users/me/settings/{setting}", "response": {"$ref": "Setting"}}}}', true));
    $this->calendarList = new Google_CalendarListServiceResource($this, $this->serviceName, 'calendarList', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"colorRgbFormat": {"type": "boolean", "location": "query"}}, "request": {"$ref": "CalendarListEntry"}, "response": {"$ref": "CalendarListEntry"}, "httpMethod": "POST", "path": "users/me/calendarList", "id": "calendar.calendarList.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "id": "calendar.calendarList.get", "httpMethod": "GET", "path": "users/me/calendarList/{calendarId}", "response": {"$ref": "CalendarListEntry"}}, "list": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "parameters": {"pageToken": {"type": "string", "location": "query"}, "showHidden": {"type": "boolean", "location": "query"}, "maxResults": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "minAccessRole": {"enum": ["freeBusyReader", "owner", "reader", "writer"], "type": "string", "location": "query"}}, "response": {"$ref": "CalendarList"}, "httpMethod": "GET", "path": "users/me/calendarList", "id": "calendar.calendarList.list"}, "update": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"colorRgbFormat": {"type": "boolean", "location": "query"}, "calendarId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "CalendarListEntry"}, "response": {"$ref": "CalendarListEntry"}, "httpMethod": "PUT", "path": "users/me/calendarList/{calendarId}", "id": "calendar.calendarList.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"colorRgbFormat": {"type": "boolean", "location": "query"}, "calendarId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "CalendarListEntry"}, "response": {"$ref": "CalendarListEntry"}, "httpMethod": "PATCH", "path": "users/me/calendarList/{calendarId}", "id": "calendar.calendarList.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/calendar"], "path": "users/me/calendarList/{calendarId}", "id": "calendar.calendarList.delete", "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->calendars = new Google_CalendarsServiceResource($this, $this->serviceName, 'calendars', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/calendar"], "request": {"$ref": "Calendar"}, "response": {"$ref": "Calendar"}, "httpMethod": "POST", "path": "calendars", "id": "calendar.calendars.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "id": "calendar.calendars.get", "httpMethod": "GET", "path": "calendars/{calendarId}", "response": {"$ref": "Calendar"}}, "clear": {"scopes": ["https://www.googleapis.com/auth/calendar"], "path": "calendars/{calendarId}/clear", "id": "calendar.calendars.clear", "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "POST"}, "update": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Calendar"}, "response": {"$ref": "Calendar"}, "httpMethod": "PUT", "path": "calendars/{calendarId}", "id": "calendar.calendars.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Calendar"}, "response": {"$ref": "Calendar"}, "httpMethod": "PATCH", "path": "calendars/{calendarId}", "id": "calendar.calendars.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/calendar"], "path": "calendars/{calendarId}", "id": "calendar.calendars.delete", "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->acl = new Google_AclServiceResource($this, $this->serviceName, 'acl', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "AclRule"}, "response": {"$ref": "AclRule"}, "httpMethod": "POST", "path": "calendars/{calendarId}/acl", "id": "calendar.acl.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}, "ruleId": {"required": true, "type": "string", "location": "path"}}, "id": "calendar.acl.get", "httpMethod": "GET", "path": "calendars/{calendarId}/acl/{ruleId}", "response": {"$ref": "AclRule"}}, "list": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "id": "calendar.acl.list", "httpMethod": "GET", "path": "calendars/{calendarId}/acl", "response": {"$ref": "Acl"}}, "update": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}, "ruleId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "AclRule"}, "response": {"$ref": "AclRule"}, "httpMethod": "PUT", "path": "calendars/{calendarId}/acl/{ruleId}", "id": "calendar.acl.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}, "ruleId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "AclRule"}, "response": {"$ref": "AclRule"}, "httpMethod": "PATCH", "path": "calendars/{calendarId}/acl/{ruleId}", "id": "calendar.acl.patch"}, "delete": {"scopes": ["https://www.googleapis.com/auth/calendar"], "path": "calendars/{calendarId}/acl/{ruleId}", "id": "calendar.acl.delete", "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}, "ruleId": {"required": true, "type": "string", "location": "path"}}, "httpMethod": "DELETE"}}}', true));
    $this->colors = new Google_ColorsServiceResource($this, $this->serviceName, 'colors', json_decode('{"methods": {"get": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "path": "colors", "response": {"$ref": "Colors"}, "id": "calendar.colors.get", "httpMethod": "GET"}}}', true));
    $this->events = new Google_EventsServiceResource($this, $this->serviceName, 'events', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}, "sendNotifications": {"type": "boolean", "location": "query"}}, "request": {"$ref": "Event"}, "response": {"$ref": "Event"}, "httpMethod": "POST", "path": "calendars/{calendarId}/events", "id": "calendar.events.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "parameters": {"eventId": {"required": true, "type": "string", "location": "path"}, "timeZone": {"type": "string", "location": "query"}, "calendarId": {"required": true, "type": "string", "location": "path"}, "alwaysIncludeEmail": {"type": "boolean", "location": "query"}, "maxAttendees": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}}, "id": "calendar.events.get", "httpMethod": "GET", "path": "calendars/{calendarId}/events/{eventId}", "response": {"$ref": "Event"}}, "move": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"eventId": {"required": true, "type": "string", "location": "path"}, "destination": {"required": true, "type": "string", "location": "query"}, "calendarId": {"required": true, "type": "string", "location": "path"}, "sendNotifications": {"type": "boolean", "location": "query"}}, "id": "calendar.events.move", "httpMethod": "POST", "path": "calendars/{calendarId}/events/{eventId}/move", "response": {"$ref": "Event"}}, "list": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "parameters": {"orderBy": {"enum": ["startTime", "updated"], "type": "string", "location": "query"}, "showHiddenInvitations": {"type": "boolean", "location": "query"}, "showDeleted": {"type": "boolean", "location": "query"}, "iCalUID": {"type": "string", "location": "query"}, "updatedMin": {"type": "string", "location": "query", "format": "date-time"}, "singleEvents": {"type": "boolean", "location": "query"}, "alwaysIncludeEmail": {"type": "boolean", "location": "query"}, "maxResults": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "q": {"type": "string", "location": "query"}, "pageToken": {"type": "string", "location": "query"}, "calendarId": {"required": true, "type": "string", "location": "path"}, "timeMin": {"type": "string", "location": "query", "format": "date-time"}, "timeZone": {"type": "string", "location": "query"}, "timeMax": {"type": "string", "location": "query", "format": "date-time"}, "maxAttendees": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}}, "id": "calendar.events.list", "httpMethod": "GET", "path": "calendars/{calendarId}/events", "response": {"$ref": "Events"}}, "update": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"eventId": {"required": true, "type": "string", "location": "path"}, "calendarId": {"required": true, "type": "string", "location": "path"}, "alwaysIncludeEmail": {"type": "boolean", "location": "query"}, "sendNotifications": {"type": "boolean", "location": "query"}}, "request": {"$ref": "Event"}, "response": {"$ref": "Event"}, "httpMethod": "PUT", "path": "calendars/{calendarId}/events/{eventId}", "id": "calendar.events.update"}, "patch": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"eventId": {"required": true, "type": "string", "location": "path"}, "calendarId": {"required": true, "type": "string", "location": "path"}, "alwaysIncludeEmail": {"type": "boolean", "location": "query"}, "sendNotifications": {"type": "boolean", "location": "query"}}, "request": {"$ref": "Event"}, "response": {"$ref": "Event"}, "httpMethod": "PATCH", "path": "calendars/{calendarId}/events/{eventId}", "id": "calendar.events.patch"}, "instances": {"scopes": ["https://www.googleapis.com/auth/calendar", "https://www.googleapis.com/auth/calendar.readonly"], "parameters": {"eventId": {"required": true, "type": "string", "location": "path"}, "showDeleted": {"type": "boolean", "location": "query"}, "alwaysIncludeEmail": {"type": "boolean", "location": "query"}, "maxResults": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}, "pageToken": {"type": "string", "location": "query"}, "calendarId": {"required": true, "type": "string", "location": "path"}, "timeZone": {"type": "string", "location": "query"}, "originalStart": {"type": "string", "location": "query"}, "maxAttendees": {"minimum": "1", "type": "integer", "location": "query", "format": "int32"}}, "id": "calendar.events.instances", "httpMethod": "GET", "path": "calendars/{calendarId}/events/{eventId}/instances", "response": {"$ref": "Events"}}, "import": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"calendarId": {"required": true, "type": "string", "location": "path"}}, "request": {"$ref": "Event"}, "response": {"$ref": "Event"}, "httpMethod": "POST", "path": "calendars/{calendarId}/events/import", "id": "calendar.events.import"}, "quickAdd": {"scopes": ["https://www.googleapis.com/auth/calendar"], "parameters": {"text": {"required": true, "type": "string", "location": "query"}, "calendarId": {"required": true, "type": "string", "location": "path"}, "sendNotifications": {"type": "boolean", "location": "query"}}, "id": "calendar.events.quickAdd", "httpMethod": "POST", "path": "calendars/{calendarId}/events/quickAdd", "response": {"$ref": "Event"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/calendar"], "path": "calendars/{calendarId}/events/{eventId}", "id": "calendar.events.delete", "parameters": {"eventId": {"required": true, "type": "string", "location": "path"}, "calendarId": {"required": true, "type": "string", "location": "path"}, "sendNotifications": {"type": "boolean", "location": "query"}}, "httpMethod": "DELETE"}}}', true));

  }
}

class Google_Acl extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_AclRule';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(Google_AclRule) */ $items) {
    $this->assertIsArray($items, 'Google_AclRule', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_AclRule extends Google_Model {
  protected $__scopeType = 'Google_AclRuleScope';
  protected $__scopeDataType = '';
  public $scope;
  public $kind;
  public $etag;
  public $role;
  public $id;
  public function setScope(Google_AclRuleScope $scope) {
    $this->scope = $scope;
  }
  public function getScope() {
    return $this->scope;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setRole($role) {
    $this->role = $role;
  }
  public function getRole() {
    return $this->role;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_AclRuleScope extends Google_Model {
  public $type;
  public $value;
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setValue($value) {
    $this->value = $value;
  }
  public function getValue() {
    return $this->value;
  }
}

class Google_Calendar extends Google_Model {
  public $kind;
  public $description;
  public $summary;
  public $etag;
  public $location;
  public $timeZone;
  public $id;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setSummary($summary) {
    $this->summary = $summary;
  }
  public function getSummary() {
    return $this->summary;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setTimeZone($timeZone) {
    $this->timeZone = $timeZone;
  }
  public function getTimeZone() {
    return $this->timeZone;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_CalendarList extends Google_Model {
  public $nextPageToken;
  protected $__itemsType = 'Google_CalendarListEntry';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setItems(/* array(Google_CalendarListEntry) */ $items) {
    $this->assertIsArray($items, 'Google_CalendarListEntry', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_CalendarListEntry extends Google_Model {
  public $kind;
  public $foregroundColor;
  protected $__defaultRemindersType = 'Google_EventReminder';
  protected $__defaultRemindersDataType = 'array';
  public $defaultReminders;
  public $description;
  public $colorId;
  public $selected;
  public $summary;
  public $etag;
  public $location;
  public $backgroundColor;
  public $summaryOverride;
  public $timeZone;
  public $hidden;
  public $accessRole;
  public $id;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setForegroundColor($foregroundColor) {
    $this->foregroundColor = $foregroundColor;
  }
  public function getForegroundColor() {
    return $this->foregroundColor;
  }
  public function setDefaultReminders(/* array(Google_EventReminder) */ $defaultReminders) {
    $this->assertIsArray($defaultReminders, 'Google_EventReminder', __METHOD__);
    $this->defaultReminders = $defaultReminders;
  }
  public function getDefaultReminders() {
    return $this->defaultReminders;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setColorId($colorId) {
    $this->colorId = $colorId;
  }
  public function getColorId() {
    return $this->colorId;
  }
  public function setSelected($selected) {
    $this->selected = $selected;
  }
  public function getSelected() {
    return $this->selected;
  }
  public function setSummary($summary) {
    $this->summary = $summary;
  }
  public function getSummary() {
    return $this->summary;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setBackgroundColor($backgroundColor) {
    $this->backgroundColor = $backgroundColor;
  }
  public function getBackgroundColor() {
    return $this->backgroundColor;
  }
  public function setSummaryOverride($summaryOverride) {
    $this->summaryOverride = $summaryOverride;
  }
  public function getSummaryOverride() {
    return $this->summaryOverride;
  }
  public function setTimeZone($timeZone) {
    $this->timeZone = $timeZone;
  }
  public function getTimeZone() {
    return $this->timeZone;
  }
  public function setHidden($hidden) {
    $this->hidden = $hidden;
  }
  public function getHidden() {
    return $this->hidden;
  }
  public function setAccessRole($accessRole) {
    $this->accessRole = $accessRole;
  }
  public function getAccessRole() {
    return $this->accessRole;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_ColorDefinition extends Google_Model {
  public $foreground;
  public $background;
  public function setForeground($foreground) {
    $this->foreground = $foreground;
  }
  public function getForeground() {
    return $this->foreground;
  }
  public function setBackground($background) {
    $this->background = $background;
  }
  public function getBackground() {
    return $this->background;
  }
}

class Google_Colors extends Google_Model {
  protected $__calendarType = 'Google_ColorDefinition';
  protected $__calendarDataType = 'map';
  public $calendar;
  public $updated;
  protected $__eventType = 'Google_ColorDefinition';
  protected $__eventDataType = 'map';
  public $event;
  public $kind;
  public function setCalendar(Google_ColorDefinition $calendar) {
    $this->calendar = $calendar;
  }
  public function getCalendar() {
    return $this->calendar;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setEvent(Google_ColorDefinition $event) {
    $this->event = $event;
  }
  public function getEvent() {
    return $this->event;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_Error extends Google_Model {
  public $domain;
  public $reason;
  public function setDomain($domain) {
    $this->domain = $domain;
  }
  public function getDomain() {
    return $this->domain;
  }
  public function setReason($reason) {
    $this->reason = $reason;
  }
  public function getReason() {
    return $this->reason;
  }
}

class Google_Event extends Google_Model {
  protected $__creatorType = 'Google_EventCreator';
  protected $__creatorDataType = '';
  public $creator;
  protected $__organizerType = 'Google_EventOrganizer';
  protected $__organizerDataType = '';
  public $organizer;
  public $summary;
  public $id;
  protected $__attendeesType = 'Google_EventAttendee';
  protected $__attendeesDataType = 'array';
  public $attendees;
  public $htmlLink;
  public $recurrence;
  protected $__startType = 'Google_EventDateTime';
  protected $__startDataType = '';
  public $start;
  public $etag;
  public $location;
  public $recurringEventId;
  protected $__gadgetType = 'Google_EventGadget';
  protected $__gadgetDataType = '';
  public $gadget;
  public $status;
  public $updated;
  public $description;
  public $iCalUID;
  protected $__extendedPropertiesType = 'Google_EventExtendedProperties';
  protected $__extendedPropertiesDataType = '';
  public $extendedProperties;
  public $endTimeUnspecified;
  public $sequence;
  public $visibility;
  public $guestsCanModify;
  protected $__endType = 'Google_EventDateTime';
  protected $__endDataType = '';
  public $end;
  public $attendeesOmitted;
  public $kind;
  public $locked;
  public $created;
  public $colorId;
  public $anyoneCanAddSelf;
  protected $__remindersType = 'Google_EventReminders';
  protected $__remindersDataType = '';
  public $reminders;
  public $guestsCanSeeOtherGuests;
  protected $__originalStartTimeType = 'Google_EventDateTime';
  protected $__originalStartTimeDataType = '';
  public $originalStartTime;
  public $guestsCanInviteOthers;
  public $transparency;
  public $privateCopy;
  public function setCreator(Google_EventCreator $creator) {
    $this->creator = $creator;
  }
  public function getCreator() {
    return $this->creator;
  }
  public function setOrganizer(Google_EventOrganizer $organizer) {
    $this->organizer = $organizer;
  }
  public function getOrganizer() {
    return $this->organizer;
  }
  public function setSummary($summary) {
    $this->summary = $summary;
  }
  public function getSummary() {
    return $this->summary;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setAttendees(/* array(Google_EventAttendee) */ $attendees) {
    $this->assertIsArray($attendees, 'Google_EventAttendee', __METHOD__);
    $this->attendees = $attendees;
  }
  public function getAttendees() {
    return $this->attendees;
  }
  public function setHtmlLink($htmlLink) {
    $this->htmlLink = $htmlLink;
  }
  public function getHtmlLink() {
    return $this->htmlLink;
  }
  public function setRecurrence(/* array(Google_string) */ $recurrence) {
    $this->assertIsArray($recurrence, 'Google_string', __METHOD__);
    $this->recurrence = $recurrence;
  }
  public function getRecurrence() {
    return $this->recurrence;
  }
  public function setStart(Google_EventDateTime $start) {
    $this->start = $start;
  }
  public function getStart() {
    return $this->start;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setRecurringEventId($recurringEventId) {
    $this->recurringEventId = $recurringEventId;
  }
  public function getRecurringEventId() {
    return $this->recurringEventId;
  }
  public function setGadget(Google_EventGadget $gadget) {
    $this->gadget = $gadget;
  }
  public function getGadget() {
    return $this->gadget;
  }
  public function setStatus($status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setICalUID($iCalUID) {
    $this->iCalUID = $iCalUID;
  }
  public function getICalUID() {
    return $this->iCalUID;
  }
  public function setExtendedProperties(Google_EventExtendedProperties $extendedProperties) {
    $this->extendedProperties = $extendedProperties;
  }
  public function getExtendedProperties() {
    return $this->extendedProperties;
  }
  public function setEndTimeUnspecified($endTimeUnspecified) {
    $this->endTimeUnspecified = $endTimeUnspecified;
  }
  public function getEndTimeUnspecified() {
    return $this->endTimeUnspecified;
  }
  public function setSequence($sequence) {
    $this->sequence = $sequence;
  }
  public function getSequence() {
    return $this->sequence;
  }
  public function setVisibility($visibility) {
    $this->visibility = $visibility;
  }
  public function getVisibility() {
    return $this->visibility;
  }
  public function setGuestsCanModify($guestsCanModify) {
    $this->guestsCanModify = $guestsCanModify;
  }
  public function getGuestsCanModify() {
    return $this->guestsCanModify;
  }
  public function setEnd(Google_EventDateTime $end) {
    $this->end = $end;
  }
  public function getEnd() {
    return $this->end;
  }
  public function setAttendeesOmitted($attendeesOmitted) {
    $this->attendeesOmitted = $attendeesOmitted;
  }
  public function getAttendeesOmitted() {
    return $this->attendeesOmitted;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setLocked($locked) {
    $this->locked = $locked;
  }
  public function getLocked() {
    return $this->locked;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setColorId($colorId) {
    $this->colorId = $colorId;
  }
  public function getColorId() {
    return $this->colorId;
  }
  public function setAnyoneCanAddSelf($anyoneCanAddSelf) {
    $this->anyoneCanAddSelf = $anyoneCanAddSelf;
  }
  public function getAnyoneCanAddSelf() {
    return $this->anyoneCanAddSelf;
  }
  public function setReminders(Google_EventReminders $reminders) {
    $this->reminders = $reminders;
  }
  public function getReminders() {
    return $this->reminders;
  }
  public function setGuestsCanSeeOtherGuests($guestsCanSeeOtherGuests) {
    $this->guestsCanSeeOtherGuests = $guestsCanSeeOtherGuests;
  }
  public function getGuestsCanSeeOtherGuests() {
    return $this->guestsCanSeeOtherGuests;
  }
  public function setOriginalStartTime(Google_EventDateTime $originalStartTime) {
    $this->originalStartTime = $originalStartTime;
  }
  public function getOriginalStartTime() {
    return $this->originalStartTime;
  }
  public function setGuestsCanInviteOthers($guestsCanInviteOthers) {
    $this->guestsCanInviteOthers = $guestsCanInviteOthers;
  }
  public function getGuestsCanInviteOthers() {
    return $this->guestsCanInviteOthers;
  }
  public function setTransparency($transparency) {
    $this->transparency = $transparency;
  }
  public function getTransparency() {
    return $this->transparency;
  }
  public function setPrivateCopy($privateCopy) {
    $this->privateCopy = $privateCopy;
  }
  public function getPrivateCopy() {
    return $this->privateCopy;
  }
}

class Google_EventAttendee extends Google_Model {
  public $comment;
  public $displayName;
  public $responseStatus;
  public $self;
  public $id;
  public $additionalGuests;
  public $resource;
  public $organizer;
  public $optional;
  public $email;
  public function setComment($comment) {
    $this->comment = $comment;
  }
  public function getComment() {
    return $this->comment;
  }
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setResponseStatus($responseStatus) {
    $this->responseStatus = $responseStatus;
  }
  public function getResponseStatus() {
    return $this->responseStatus;
  }
  public function setSelf($self) {
    $this->self = $self;
  }
  public function getSelf() {
    return $this->self;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setAdditionalGuests($additionalGuests) {
    $this->additionalGuests = $additionalGuests;
  }
  public function getAdditionalGuests() {
    return $this->additionalGuests;
  }
  public function setResource($resource) {
    $this->resource = $resource;
  }
  public function getResource() {
    return $this->resource;
  }
  public function setOrganizer($organizer) {
    $this->organizer = $organizer;
  }
  public function getOrganizer() {
    return $this->organizer;
  }
  public function setOptional($optional) {
    $this->optional = $optional;
  }
  public function getOptional() {
    return $this->optional;
  }
  public function setEmail($email) {
    $this->email = $email;
  }
  public function getEmail() {
    return $this->email;
  }
}

class Google_EventCreator extends Google_Model {
  public $self;
  public $displayName;
  public $email;
  public $id;
  public function setSelf($self) {
    $this->self = $self;
  }
  public function getSelf() {
    return $this->self;
  }
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setEmail($email) {
    $this->email = $email;
  }
  public function getEmail() {
    return $this->email;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_EventDateTime extends Google_Model {
  public $date;
  public $timeZone;
  public $dateTime;
  public function setDate($date) {
    $this->date = $date;
  }
  public function getDate() {
    return $this->date;
  }
  public function setTimeZone($timeZone) {
    $this->timeZone = $timeZone;
  }
  public function getTimeZone() {
    return $this->timeZone;
  }
  public function setDateTime($dateTime) {
    $this->dateTime = $dateTime;
  }
  public function getDateTime() {
    return $this->dateTime;
  }
}

class Google_EventExtendedProperties extends Google_Model {
  public $shared;
  public $private;
  public function setShared($shared) {
    $this->shared = $shared;
  }
  public function getShared() {
    return $this->shared;
  }
  public function setPrivate($private) {
    $this->private = $private;
  }
  public function getPrivate() {
    return $this->private;
  }
}

class Google_EventGadget extends Google_Model {
  public $preferences;
  public $title;
  public $height;
  public $width;
  public $link;
  public $type;
  public $display;
  public $iconLink;
  public function setPreferences($preferences) {
    $this->preferences = $preferences;
  }
  public function getPreferences() {
    return $this->preferences;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setHeight($height) {
    $this->height = $height;
  }
  public function getHeight() {
    return $this->height;
  }
  public function setWidth($width) {
    $this->width = $width;
  }
  public function getWidth() {
    return $this->width;
  }
  public function setLink($link) {
    $this->link = $link;
  }
  public function getLink() {
    return $this->link;
  }
  public function setType($type) {
    $this->type = $type;
  }
  public function getType() {
    return $this->type;
  }
  public function setDisplay($display) {
    $this->display = $display;
  }
  public function getDisplay() {
    return $this->display;
  }
  public function setIconLink($iconLink) {
    $this->iconLink = $iconLink;
  }
  public function getIconLink() {
    return $this->iconLink;
  }
}

class Google_EventOrganizer extends Google_Model {
  public $self;
  public $displayName;
  public $email;
  public $id;
  public function setSelf($self) {
    $this->self = $self;
  }
  public function getSelf() {
    return $this->self;
  }
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setEmail($email) {
    $this->email = $email;
  }
  public function getEmail() {
    return $this->email;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_EventReminder extends Google_Model {
  public $minutes;
  public $method;
  public function setMinutes($minutes) {
    $this->minutes = $minutes;
  }
  public function getMinutes() {
    return $this->minutes;
  }
  public function setMethod($method) {
    $this->method = $method;
  }
  public function getMethod() {
    return $this->method;
  }
}

class Google_EventReminders extends Google_Model {
  protected $__overridesType = 'Google_EventReminder';
  protected $__overridesDataType = 'array';
  public $overrides;
  public $useDefault;
  public function setOverrides(/* array(Google_EventReminder) */ $overrides) {
    $this->assertIsArray($overrides, 'Google_EventReminder', __METHOD__);
    $this->overrides = $overrides;
  }
  public function getOverrides() {
    return $this->overrides;
  }
  public function setUseDefault($useDefault) {
    $this->useDefault = $useDefault;
  }
  public function getUseDefault() {
    return $this->useDefault;
  }
}

class Google_Events extends Google_Model {
  public $nextPageToken;
  public $kind;
  protected $__defaultRemindersType = 'Google_EventReminder';
  protected $__defaultRemindersDataType = 'array';
  public $defaultReminders;
  public $description;
  protected $__itemsType = 'Google_Event';
  protected $__itemsDataType = 'array';
  public $items;
  public $updated;
  public $summary;
  public $etag;
  public $timeZone;
  public $accessRole;
  public function setNextPageToken($nextPageToken) {
    $this->nextPageToken = $nextPageToken;
  }
  public function getNextPageToken() {
    return $this->nextPageToken;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDefaultReminders(/* array(Google_EventReminder) */ $defaultReminders) {
    $this->assertIsArray($defaultReminders, 'Google_EventReminder', __METHOD__);
    $this->defaultReminders = $defaultReminders;
  }
  public function getDefaultReminders() {
    return $this->defaultReminders;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setItems(/* array(Google_Event) */ $items) {
    $this->assertIsArray($items, 'Google_Event', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setUpdated($updated) {
    $this->updated = $updated;
  }
  public function getUpdated() {
    return $this->updated;
  }
  public function setSummary($summary) {
    $this->summary = $summary;
  }
  public function getSummary() {
    return $this->summary;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setTimeZone($timeZone) {
    $this->timeZone = $timeZone;
  }
  public function getTimeZone() {
    return $this->timeZone;
  }
  public function setAccessRole($accessRole) {
    $this->accessRole = $accessRole;
  }
  public function getAccessRole() {
    return $this->accessRole;
  }
}

class Google_FreeBusyCalendar extends Google_Model {
  protected $__busyType = 'Google_TimePeriod';
  protected $__busyDataType = 'array';
  public $busy;
  protected $__errorsType = 'Google_Error';
  protected $__errorsDataType = 'array';
  public $errors;
  public function setBusy(/* array(Google_TimePeriod) */ $busy) {
    $this->assertIsArray($busy, 'Google_TimePeriod', __METHOD__);
    $this->busy = $busy;
  }
  public function getBusy() {
    return $this->busy;
  }
  public function setErrors(/* array(Google_Error) */ $errors) {
    $this->assertIsArray($errors, 'Google_Error', __METHOD__);
    $this->errors = $errors;
  }
  public function getErrors() {
    return $this->errors;
  }
}

class Google_FreeBusyGroup extends Google_Model {
  protected $__errorsType = 'Google_Error';
  protected $__errorsDataType = 'array';
  public $errors;
  public $calendars;
  public function setErrors(/* array(Google_Error) */ $errors) {
    $this->assertIsArray($errors, 'Google_Error', __METHOD__);
    $this->errors = $errors;
  }
  public function getErrors() {
    return $this->errors;
  }
  public function setCalendars(/* array(Google_string) */ $calendars) {
    $this->assertIsArray($calendars, 'Google_string', __METHOD__);
    $this->calendars = $calendars;
  }
  public function getCalendars() {
    return $this->calendars;
  }
}

class Google_FreeBusyRequest extends Google_Model {
  public $calendarExpansionMax;
  public $groupExpansionMax;
  public $timeMax;
  protected $__itemsType = 'Google_FreeBusyRequestItem';
  protected $__itemsDataType = 'array';
  public $items;
  public $timeMin;
  public $timeZone;
  public function setCalendarExpansionMax($calendarExpansionMax) {
    $this->calendarExpansionMax = $calendarExpansionMax;
  }
  public function getCalendarExpansionMax() {
    return $this->calendarExpansionMax;
  }
  public function setGroupExpansionMax($groupExpansionMax) {
    $this->groupExpansionMax = $groupExpansionMax;
  }
  public function getGroupExpansionMax() {
    return $this->groupExpansionMax;
  }
  public function setTimeMax($timeMax) {
    $this->timeMax = $timeMax;
  }
  public function getTimeMax() {
    return $this->timeMax;
  }
  public function setItems(/* array(Google_FreeBusyRequestItem) */ $items) {
    $this->assertIsArray($items, 'Google_FreeBusyRequestItem', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setTimeMin($timeMin) {
    $this->timeMin = $timeMin;
  }
  public function getTimeMin() {
    return $this->timeMin;
  }
  public function setTimeZone($timeZone) {
    $this->timeZone = $timeZone;
  }
  public function getTimeZone() {
    return $this->timeZone;
  }
}

class Google_FreeBusyRequestItem extends Google_Model {
  public $id;
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_FreeBusyResponse extends Google_Model {
  public $timeMax;
  public $kind;
  protected $__calendarsType = 'Google_FreeBusyCalendar';
  protected $__calendarsDataType = 'map';
  public $calendars;
  public $timeMin;
  protected $__groupsType = 'Google_FreeBusyGroup';
  protected $__groupsDataType = 'map';
  public $groups;
  public function setTimeMax($timeMax) {
    $this->timeMax = $timeMax;
  }
  public function getTimeMax() {
    return $this->timeMax;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setCalendars(Google_FreeBusyCalendar $calendars) {
    $this->calendars = $calendars;
  }
  public function getCalendars() {
    return $this->calendars;
  }
  public function setTimeMin($timeMin) {
    $this->timeMin = $timeMin;
  }
  public function getTimeMin() {
    return $this->timeMin;
  }
  public function setGroups(Google_FreeBusyGroup $groups) {
    $this->groups = $groups;
  }
  public function getGroups() {
    return $this->groups;
  }
}

class Google_Setting extends Google_Model {
  public $kind;
  public $etag;
  public $id;
  public $value;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setValue($value) {
    $this->value = $value;
  }
  public function getValue() {
    return $this->value;
  }
}

class Google_Settings extends Google_Model {
  protected $__itemsType = 'Google_Setting';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public $etag;
  public function setItems(/* array(Google_Setting) */ $items) {
    $this->assertIsArray($items, 'Google_Setting', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_TimePeriod extends Google_Model {
  public $start;
  public $end;
  public function setStart($start) {
    $this->start = $start;
  }
  public function getStart() {
    return $this->start;
  }
  public function setEnd($end) {
    $this->end = $end;
  }
  public function getEnd() {
    return $this->end;
  }
}
