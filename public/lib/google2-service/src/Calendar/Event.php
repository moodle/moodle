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

class Event extends \Google\Collection
{
  protected $collection_key = 'recurrence';
  /**
   * Whether anyone can invite themselves to the event (deprecated). Optional.
   * The default is False.
   *
   * @var bool
   */
  public $anyoneCanAddSelf;
  protected $attachmentsType = EventAttachment::class;
  protected $attachmentsDataType = 'array';
  protected $attendeesType = EventAttendee::class;
  protected $attendeesDataType = 'array';
  /**
   * Whether attendees may have been omitted from the event's representation.
   * When retrieving an event, this may be due to a restriction specified by the
   * maxAttendee query parameter. When updating an event, this can be used to
   * only update the participant's response. Optional. The default is False.
   *
   * @var bool
   */
  public $attendeesOmitted;
  protected $birthdayPropertiesType = EventBirthdayProperties::class;
  protected $birthdayPropertiesDataType = '';
  /**
   * The color of the event. This is an ID referring to an entry in the event
   * section of the colors definition (see the  colors endpoint). Optional.
   *
   * @var string
   */
  public $colorId;
  protected $conferenceDataType = ConferenceData::class;
  protected $conferenceDataDataType = '';
  /**
   * Creation time of the event (as a RFC3339 timestamp). Read-only.
   *
   * @var string
   */
  public $created;
  protected $creatorType = EventCreator::class;
  protected $creatorDataType = '';
  /**
   * Description of the event. Can contain HTML. Optional.
   *
   * @var string
   */
  public $description;
  protected $endType = EventDateTime::class;
  protected $endDataType = '';
  /**
   * Whether the end time is actually unspecified. An end time is still provided
   * for compatibility reasons, even if this attribute is set to True. The
   * default is False.
   *
   * @var bool
   */
  public $endTimeUnspecified;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Specific type of the event. This cannot be modified after the event is
   * created. Possible values are: - "birthday" - A special all-day event with
   * an annual recurrence.  - "default" - A regular event or not further
   * specified.  - "focusTime" - A focus-time event.  - "fromGmail" - An event
   * from Gmail. This type of event cannot be created.  - "outOfOffice" - An
   * out-of-office event.  - "workingLocation" - A working location event.
   *
   * @var string
   */
  public $eventType;
  protected $extendedPropertiesType = EventExtendedProperties::class;
  protected $extendedPropertiesDataType = '';
  protected $focusTimePropertiesType = EventFocusTimeProperties::class;
  protected $focusTimePropertiesDataType = '';
  protected $gadgetType = EventGadget::class;
  protected $gadgetDataType = '';
  /**
   * Whether attendees other than the organizer can invite others to the event.
   * Optional. The default is True.
   *
   * @var bool
   */
  public $guestsCanInviteOthers;
  /**
   * Whether attendees other than the organizer can modify the event. Optional.
   * The default is False.
   *
   * @var bool
   */
  public $guestsCanModify;
  /**
   * Whether attendees other than the organizer can see who the event's
   * attendees are. Optional. The default is True.
   *
   * @var bool
   */
  public $guestsCanSeeOtherGuests;
  /**
   * An absolute link to the Google Hangout associated with this event. Read-
   * only.
   *
   * @var string
   */
  public $hangoutLink;
  /**
   * An absolute link to this event in the Google Calendar Web UI. Read-only.
   *
   * @var string
   */
  public $htmlLink;
  /**
   * Event unique identifier as defined in RFC5545. It is used to uniquely
   * identify events accross calendaring systems and must be supplied when
   * importing events via the import method. Note that the iCalUID and the id
   * are not identical and only one of them should be supplied at event creation
   * time. One difference in their semantics is that in recurring events, all
   * occurrences of one event have different ids while they all share the same
   * iCalUIDs. To retrieve an event using its iCalUID, call the events.list
   * method using the iCalUID parameter. To retrieve an event using its id, call
   * the events.get method.
   *
   * @var string
   */
  public $iCalUID;
  /**
   * Opaque identifier of the event. When creating new single or recurring
   * events, you can specify their IDs. Provided IDs must follow these rules: -
   * characters allowed in the ID are those used in base32hex encoding, i.e.
   * lowercase letters a-v and digits 0-9, see section 3.1.2 in RFC2938  - the
   * length of the ID must be between 5 and 1024 characters  - the ID must be
   * unique per calendar  Due to the globally distributed nature of the system,
   * we cannot guarantee that ID collisions will be detected at event creation
   * time. To minimize the risk of collisions we recommend using an established
   * UUID algorithm such as one described in RFC4122. If you do not specify an
   * ID, it will be automatically generated by the server. Note that the icalUID
   * and the id are not identical and only one of them should be supplied at
   * event creation time. One difference in their semantics is that in recurring
   * events, all occurrences of one event have different ids while they all
   * share the same icalUIDs.
   *
   * @var string
   */
  public $id;
  /**
   * Type of the resource ("calendar#event").
   *
   * @var string
   */
  public $kind;
  /**
   * Geographic location of the event as free-form text. Optional.
   *
   * @var string
   */
  public $location;
  /**
   * Whether this is a locked event copy where no changes can be made to the
   * main event fields "summary", "description", "location", "start", "end" or
   * "recurrence". The default is False. Read-Only.
   *
   * @var bool
   */
  public $locked;
  protected $organizerType = EventOrganizer::class;
  protected $organizerDataType = '';
  protected $originalStartTimeType = EventDateTime::class;
  protected $originalStartTimeDataType = '';
  protected $outOfOfficePropertiesType = EventOutOfOfficeProperties::class;
  protected $outOfOfficePropertiesDataType = '';
  /**
   * If set to True, Event propagation is disabled. Note that it is not the same
   * thing as Private event properties. Optional. Immutable. The default is
   * False.
   *
   * @var bool
   */
  public $privateCopy;
  /**
   * List of RRULE, EXRULE, RDATE and EXDATE lines for a recurring event, as
   * specified in RFC5545. Note that DTSTART and DTEND lines are not allowed in
   * this field; event start and end times are specified in the start and end
   * fields. This field is omitted for single events or instances of recurring
   * events.
   *
   * @var string[]
   */
  public $recurrence;
  /**
   * For an instance of a recurring event, this is the id of the recurring event
   * to which this instance belongs. Immutable.
   *
   * @var string
   */
  public $recurringEventId;
  protected $remindersType = EventReminders::class;
  protected $remindersDataType = '';
  /**
   * Sequence number as per iCalendar.
   *
   * @var int
   */
  public $sequence;
  protected $sourceType = EventSource::class;
  protected $sourceDataType = '';
  protected $startType = EventDateTime::class;
  protected $startDataType = '';
  /**
   * Status of the event. Optional. Possible values are: - "confirmed" - The
   * event is confirmed. This is the default status.  - "tentative" - The event
   * is tentatively confirmed.  - "cancelled" - The event is cancelled
   * (deleted). The list method returns cancelled events only on incremental
   * sync (when syncToken or updatedMin are specified) or if the showDeleted
   * flag is set to true. The get method always returns them. A cancelled status
   * represents two different states depending on the event type:   - Cancelled
   * exceptions of an uncancelled recurring event indicate that this instance
   * should no longer be presented to the user. Clients should store these
   * events for the lifetime of the parent recurring event. Cancelled exceptions
   * are only guaranteed to have values for the id, recurringEventId and
   * originalStartTime fields populated. The other fields might be empty.   -
   * All other cancelled events represent deleted events. Clients should remove
   * their locally synced copies. Such cancelled events will eventually
   * disappear, so do not rely on them being available indefinitely. Deleted
   * events are only guaranteed to have the id field populated.   On the
   * organizer's calendar, cancelled events continue to expose event details
   * (summary, location, etc.) so that they can be restored (undeleted).
   * Similarly, the events to which the user was invited and that they manually
   * removed continue to provide details. However, incremental sync requests
   * with showDeleted set to false will not return these details. If an event
   * changes its organizer (for example via the move operation) and the original
   * organizer is not on the attendee list, it will leave behind a cancelled
   * event where only the id field is guaranteed to be populated.
   *
   * @var string
   */
  public $status;
  /**
   * Title of the event.
   *
   * @var string
   */
  public $summary;
  /**
   * Whether the event blocks time on the calendar. Optional. Possible values
   * are: - "opaque" - Default value. The event does block time on the calendar.
   * This is equivalent to setting Show me as to Busy in the Calendar UI.  -
   * "transparent" - The event does not block time on the calendar. This is
   * equivalent to setting Show me as to Available in the Calendar UI.
   *
   * @var string
   */
  public $transparency;
  /**
   * Last modification time of the main event data (as a RFC3339 timestamp).
   * Updating event reminders will not cause this to change. Read-only.
   *
   * @var string
   */
  public $updated;
  /**
   * Visibility of the event. Optional. Possible values are: - "default" - Uses
   * the default visibility for events on the calendar. This is the default
   * value.  - "public" - The event is public and event details are visible to
   * all readers of the calendar.  - "private" - The event is private and only
   * event attendees may view event details.  - "confidential" - The event is
   * private. This value is provided for compatibility reasons.
   *
   * @var string
   */
  public $visibility;
  protected $workingLocationPropertiesType = EventWorkingLocationProperties::class;
  protected $workingLocationPropertiesDataType = '';

  /**
   * Whether anyone can invite themselves to the event (deprecated). Optional.
   * The default is False.
   *
   * @param bool $anyoneCanAddSelf
   */
  public function setAnyoneCanAddSelf($anyoneCanAddSelf)
  {
    $this->anyoneCanAddSelf = $anyoneCanAddSelf;
  }
  /**
   * @return bool
   */
  public function getAnyoneCanAddSelf()
  {
    return $this->anyoneCanAddSelf;
  }
  /**
   * File attachments for the event. In order to modify attachments the
   * supportsAttachments request parameter should be set to true. There can be
   * at most 25 attachments per event,
   *
   * @param EventAttachment[] $attachments
   */
  public function setAttachments($attachments)
  {
    $this->attachments = $attachments;
  }
  /**
   * @return EventAttachment[]
   */
  public function getAttachments()
  {
    return $this->attachments;
  }
  /**
   * The attendees of the event. See the Events with attendees guide for more
   * information on scheduling events with other calendar users. Service
   * accounts need to use domain-wide delegation of authority to populate the
   * attendee list.
   *
   * @param EventAttendee[] $attendees
   */
  public function setAttendees($attendees)
  {
    $this->attendees = $attendees;
  }
  /**
   * @return EventAttendee[]
   */
  public function getAttendees()
  {
    return $this->attendees;
  }
  /**
   * Whether attendees may have been omitted from the event's representation.
   * When retrieving an event, this may be due to a restriction specified by the
   * maxAttendee query parameter. When updating an event, this can be used to
   * only update the participant's response. Optional. The default is False.
   *
   * @param bool $attendeesOmitted
   */
  public function setAttendeesOmitted($attendeesOmitted)
  {
    $this->attendeesOmitted = $attendeesOmitted;
  }
  /**
   * @return bool
   */
  public function getAttendeesOmitted()
  {
    return $this->attendeesOmitted;
  }
  /**
   * Birthday or special event data. Used if eventType is "birthday". Immutable.
   *
   * @param EventBirthdayProperties $birthdayProperties
   */
  public function setBirthdayProperties(EventBirthdayProperties $birthdayProperties)
  {
    $this->birthdayProperties = $birthdayProperties;
  }
  /**
   * @return EventBirthdayProperties
   */
  public function getBirthdayProperties()
  {
    return $this->birthdayProperties;
  }
  /**
   * The color of the event. This is an ID referring to an entry in the event
   * section of the colors definition (see the  colors endpoint). Optional.
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
   * The conference-related information, such as details of a Google Meet
   * conference. To create new conference details use the createRequest field.
   * To persist your changes, remember to set the conferenceDataVersion request
   * parameter to 1 for all event modification requests.
   *
   * @param ConferenceData $conferenceData
   */
  public function setConferenceData(ConferenceData $conferenceData)
  {
    $this->conferenceData = $conferenceData;
  }
  /**
   * @return ConferenceData
   */
  public function getConferenceData()
  {
    return $this->conferenceData;
  }
  /**
   * Creation time of the event (as a RFC3339 timestamp). Read-only.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * The creator of the event. Read-only.
   *
   * @param EventCreator $creator
   */
  public function setCreator(EventCreator $creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return EventCreator
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Description of the event. Can contain HTML. Optional.
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
   * The (exclusive) end time of the event. For a recurring event, this is the
   * end time of the first instance.
   *
   * @param EventDateTime $end
   */
  public function setEnd(EventDateTime $end)
  {
    $this->end = $end;
  }
  /**
   * @return EventDateTime
   */
  public function getEnd()
  {
    return $this->end;
  }
  /**
   * Whether the end time is actually unspecified. An end time is still provided
   * for compatibility reasons, even if this attribute is set to True. The
   * default is False.
   *
   * @param bool $endTimeUnspecified
   */
  public function setEndTimeUnspecified($endTimeUnspecified)
  {
    $this->endTimeUnspecified = $endTimeUnspecified;
  }
  /**
   * @return bool
   */
  public function getEndTimeUnspecified()
  {
    return $this->endTimeUnspecified;
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
   * Specific type of the event. This cannot be modified after the event is
   * created. Possible values are: - "birthday" - A special all-day event with
   * an annual recurrence.  - "default" - A regular event or not further
   * specified.  - "focusTime" - A focus-time event.  - "fromGmail" - An event
   * from Gmail. This type of event cannot be created.  - "outOfOffice" - An
   * out-of-office event.  - "workingLocation" - A working location event.
   *
   * @param string $eventType
   */
  public function setEventType($eventType)
  {
    $this->eventType = $eventType;
  }
  /**
   * @return string
   */
  public function getEventType()
  {
    return $this->eventType;
  }
  /**
   * Extended properties of the event.
   *
   * @param EventExtendedProperties $extendedProperties
   */
  public function setExtendedProperties(EventExtendedProperties $extendedProperties)
  {
    $this->extendedProperties = $extendedProperties;
  }
  /**
   * @return EventExtendedProperties
   */
  public function getExtendedProperties()
  {
    return $this->extendedProperties;
  }
  /**
   * Focus Time event data. Used if eventType is focusTime.
   *
   * @param EventFocusTimeProperties $focusTimeProperties
   */
  public function setFocusTimeProperties(EventFocusTimeProperties $focusTimeProperties)
  {
    $this->focusTimeProperties = $focusTimeProperties;
  }
  /**
   * @return EventFocusTimeProperties
   */
  public function getFocusTimeProperties()
  {
    return $this->focusTimeProperties;
  }
  /**
   * A gadget that extends this event. Gadgets are deprecated; this structure is
   * instead only used for returning birthday calendar metadata.
   *
   * @param EventGadget $gadget
   */
  public function setGadget(EventGadget $gadget)
  {
    $this->gadget = $gadget;
  }
  /**
   * @return EventGadget
   */
  public function getGadget()
  {
    return $this->gadget;
  }
  /**
   * Whether attendees other than the organizer can invite others to the event.
   * Optional. The default is True.
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
   * Whether attendees other than the organizer can modify the event. Optional.
   * The default is False.
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
   * Whether attendees other than the organizer can see who the event's
   * attendees are. Optional. The default is True.
   *
   * @param bool $guestsCanSeeOtherGuests
   */
  public function setGuestsCanSeeOtherGuests($guestsCanSeeOtherGuests)
  {
    $this->guestsCanSeeOtherGuests = $guestsCanSeeOtherGuests;
  }
  /**
   * @return bool
   */
  public function getGuestsCanSeeOtherGuests()
  {
    return $this->guestsCanSeeOtherGuests;
  }
  /**
   * An absolute link to the Google Hangout associated with this event. Read-
   * only.
   *
   * @param string $hangoutLink
   */
  public function setHangoutLink($hangoutLink)
  {
    $this->hangoutLink = $hangoutLink;
  }
  /**
   * @return string
   */
  public function getHangoutLink()
  {
    return $this->hangoutLink;
  }
  /**
   * An absolute link to this event in the Google Calendar Web UI. Read-only.
   *
   * @param string $htmlLink
   */
  public function setHtmlLink($htmlLink)
  {
    $this->htmlLink = $htmlLink;
  }
  /**
   * @return string
   */
  public function getHtmlLink()
  {
    return $this->htmlLink;
  }
  /**
   * Event unique identifier as defined in RFC5545. It is used to uniquely
   * identify events accross calendaring systems and must be supplied when
   * importing events via the import method. Note that the iCalUID and the id
   * are not identical and only one of them should be supplied at event creation
   * time. One difference in their semantics is that in recurring events, all
   * occurrences of one event have different ids while they all share the same
   * iCalUIDs. To retrieve an event using its iCalUID, call the events.list
   * method using the iCalUID parameter. To retrieve an event using its id, call
   * the events.get method.
   *
   * @param string $iCalUID
   */
  public function setICalUID($iCalUID)
  {
    $this->iCalUID = $iCalUID;
  }
  /**
   * @return string
   */
  public function getICalUID()
  {
    return $this->iCalUID;
  }
  /**
   * Opaque identifier of the event. When creating new single or recurring
   * events, you can specify their IDs. Provided IDs must follow these rules: -
   * characters allowed in the ID are those used in base32hex encoding, i.e.
   * lowercase letters a-v and digits 0-9, see section 3.1.2 in RFC2938  - the
   * length of the ID must be between 5 and 1024 characters  - the ID must be
   * unique per calendar  Due to the globally distributed nature of the system,
   * we cannot guarantee that ID collisions will be detected at event creation
   * time. To minimize the risk of collisions we recommend using an established
   * UUID algorithm such as one described in RFC4122. If you do not specify an
   * ID, it will be automatically generated by the server. Note that the icalUID
   * and the id are not identical and only one of them should be supplied at
   * event creation time. One difference in their semantics is that in recurring
   * events, all occurrences of one event have different ids while they all
   * share the same icalUIDs.
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
   * Type of the resource ("calendar#event").
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
   * Geographic location of the event as free-form text. Optional.
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
   * Whether this is a locked event copy where no changes can be made to the
   * main event fields "summary", "description", "location", "start", "end" or
   * "recurrence". The default is False. Read-Only.
   *
   * @param bool $locked
   */
  public function setLocked($locked)
  {
    $this->locked = $locked;
  }
  /**
   * @return bool
   */
  public function getLocked()
  {
    return $this->locked;
  }
  /**
   * The organizer of the event. If the organizer is also an attendee, this is
   * indicated with a separate entry in attendees with the organizer field set
   * to True. To change the organizer, use the move operation. Read-only, except
   * when importing an event.
   *
   * @param EventOrganizer $organizer
   */
  public function setOrganizer(EventOrganizer $organizer)
  {
    $this->organizer = $organizer;
  }
  /**
   * @return EventOrganizer
   */
  public function getOrganizer()
  {
    return $this->organizer;
  }
  /**
   * For an instance of a recurring event, this is the time at which this event
   * would start according to the recurrence data in the recurring event
   * identified by recurringEventId. It uniquely identifies the instance within
   * the recurring event series even if the instance was moved to a different
   * time. Immutable.
   *
   * @param EventDateTime $originalStartTime
   */
  public function setOriginalStartTime(EventDateTime $originalStartTime)
  {
    $this->originalStartTime = $originalStartTime;
  }
  /**
   * @return EventDateTime
   */
  public function getOriginalStartTime()
  {
    return $this->originalStartTime;
  }
  /**
   * Out of office event data. Used if eventType is outOfOffice.
   *
   * @param EventOutOfOfficeProperties $outOfOfficeProperties
   */
  public function setOutOfOfficeProperties(EventOutOfOfficeProperties $outOfOfficeProperties)
  {
    $this->outOfOfficeProperties = $outOfOfficeProperties;
  }
  /**
   * @return EventOutOfOfficeProperties
   */
  public function getOutOfOfficeProperties()
  {
    return $this->outOfOfficeProperties;
  }
  /**
   * If set to True, Event propagation is disabled. Note that it is not the same
   * thing as Private event properties. Optional. Immutable. The default is
   * False.
   *
   * @param bool $privateCopy
   */
  public function setPrivateCopy($privateCopy)
  {
    $this->privateCopy = $privateCopy;
  }
  /**
   * @return bool
   */
  public function getPrivateCopy()
  {
    return $this->privateCopy;
  }
  /**
   * List of RRULE, EXRULE, RDATE and EXDATE lines for a recurring event, as
   * specified in RFC5545. Note that DTSTART and DTEND lines are not allowed in
   * this field; event start and end times are specified in the start and end
   * fields. This field is omitted for single events or instances of recurring
   * events.
   *
   * @param string[] $recurrence
   */
  public function setRecurrence($recurrence)
  {
    $this->recurrence = $recurrence;
  }
  /**
   * @return string[]
   */
  public function getRecurrence()
  {
    return $this->recurrence;
  }
  /**
   * For an instance of a recurring event, this is the id of the recurring event
   * to which this instance belongs. Immutable.
   *
   * @param string $recurringEventId
   */
  public function setRecurringEventId($recurringEventId)
  {
    $this->recurringEventId = $recurringEventId;
  }
  /**
   * @return string
   */
  public function getRecurringEventId()
  {
    return $this->recurringEventId;
  }
  /**
   * Information about the event's reminders for the authenticated user. Note
   * that changing reminders does not also change the updated property of the
   * enclosing event.
   *
   * @param EventReminders $reminders
   */
  public function setReminders(EventReminders $reminders)
  {
    $this->reminders = $reminders;
  }
  /**
   * @return EventReminders
   */
  public function getReminders()
  {
    return $this->reminders;
  }
  /**
   * Sequence number as per iCalendar.
   *
   * @param int $sequence
   */
  public function setSequence($sequence)
  {
    $this->sequence = $sequence;
  }
  /**
   * @return int
   */
  public function getSequence()
  {
    return $this->sequence;
  }
  /**
   * Source from which the event was created. For example, a web page, an email
   * message or any document identifiable by an URL with HTTP or HTTPS scheme.
   * Can only be seen or modified by the creator of the event.
   *
   * @param EventSource $source
   */
  public function setSource(EventSource $source)
  {
    $this->source = $source;
  }
  /**
   * @return EventSource
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * The (inclusive) start time of the event. For a recurring event, this is the
   * start time of the first instance.
   *
   * @param EventDateTime $start
   */
  public function setStart(EventDateTime $start)
  {
    $this->start = $start;
  }
  /**
   * @return EventDateTime
   */
  public function getStart()
  {
    return $this->start;
  }
  /**
   * Status of the event. Optional. Possible values are: - "confirmed" - The
   * event is confirmed. This is the default status.  - "tentative" - The event
   * is tentatively confirmed.  - "cancelled" - The event is cancelled
   * (deleted). The list method returns cancelled events only on incremental
   * sync (when syncToken or updatedMin are specified) or if the showDeleted
   * flag is set to true. The get method always returns them. A cancelled status
   * represents two different states depending on the event type:   - Cancelled
   * exceptions of an uncancelled recurring event indicate that this instance
   * should no longer be presented to the user. Clients should store these
   * events for the lifetime of the parent recurring event. Cancelled exceptions
   * are only guaranteed to have values for the id, recurringEventId and
   * originalStartTime fields populated. The other fields might be empty.   -
   * All other cancelled events represent deleted events. Clients should remove
   * their locally synced copies. Such cancelled events will eventually
   * disappear, so do not rely on them being available indefinitely. Deleted
   * events are only guaranteed to have the id field populated.   On the
   * organizer's calendar, cancelled events continue to expose event details
   * (summary, location, etc.) so that they can be restored (undeleted).
   * Similarly, the events to which the user was invited and that they manually
   * removed continue to provide details. However, incremental sync requests
   * with showDeleted set to false will not return these details. If an event
   * changes its organizer (for example via the move operation) and the original
   * organizer is not on the attendee list, it will leave behind a cancelled
   * event where only the id field is guaranteed to be populated.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Title of the event.
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
   * Whether the event blocks time on the calendar. Optional. Possible values
   * are: - "opaque" - Default value. The event does block time on the calendar.
   * This is equivalent to setting Show me as to Busy in the Calendar UI.  -
   * "transparent" - The event does not block time on the calendar. This is
   * equivalent to setting Show me as to Available in the Calendar UI.
   *
   * @param string $transparency
   */
  public function setTransparency($transparency)
  {
    $this->transparency = $transparency;
  }
  /**
   * @return string
   */
  public function getTransparency()
  {
    return $this->transparency;
  }
  /**
   * Last modification time of the main event data (as a RFC3339 timestamp).
   * Updating event reminders will not cause this to change. Read-only.
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
  /**
   * Visibility of the event. Optional. Possible values are: - "default" - Uses
   * the default visibility for events on the calendar. This is the default
   * value.  - "public" - The event is public and event details are visible to
   * all readers of the calendar.  - "private" - The event is private and only
   * event attendees may view event details.  - "confidential" - The event is
   * private. This value is provided for compatibility reasons.
   *
   * @param string $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return string
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
  /**
   * Working location event data.
   *
   * @param EventWorkingLocationProperties $workingLocationProperties
   */
  public function setWorkingLocationProperties(EventWorkingLocationProperties $workingLocationProperties)
  {
    $this->workingLocationProperties = $workingLocationProperties;
  }
  /**
   * @return EventWorkingLocationProperties
   */
  public function getWorkingLocationProperties()
  {
    return $this->workingLocationProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Event::class, 'Google_Service_Calendar_Event');
