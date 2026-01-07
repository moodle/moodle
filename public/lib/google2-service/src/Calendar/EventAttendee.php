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

class EventAttendee extends \Google\Model
{
  /**
   * Number of additional guests. Optional. The default is 0.
   *
   * @var int
   */
  public $additionalGuests;
  /**
   * The attendee's response comment. Optional.
   *
   * @var string
   */
  public $comment;
  /**
   * The attendee's name, if available. Optional.
   *
   * @var string
   */
  public $displayName;
  /**
   * The attendee's email address, if available. This field must be present when
   * adding an attendee. It must be a valid email address as per RFC5322.
   * Required when adding an attendee.
   *
   * @var string
   */
  public $email;
  /**
   * The attendee's Profile ID, if available.
   *
   * @var string
   */
  public $id;
  /**
   * Whether this is an optional attendee. Optional. The default is False.
   *
   * @var bool
   */
  public $optional;
  /**
   * Whether the attendee is the organizer of the event. Read-only. The default
   * is False.
   *
   * @var bool
   */
  public $organizer;
  /**
   * Whether the attendee is a resource. Can only be set when the attendee is
   * added to the event for the first time. Subsequent modifications are
   * ignored. Optional. The default is False.
   *
   * @var bool
   */
  public $resource;
  /**
   * The attendee's response status. Possible values are: - "needsAction" - The
   * attendee has not responded to the invitation (recommended for new events).
   * - "declined" - The attendee has declined the invitation.  - "tentative" -
   * The attendee has tentatively accepted the invitation.  - "accepted" - The
   * attendee has accepted the invitation.  Warning: If you add an event using
   * the values declined, tentative, or accepted, attendees with the "Add
   * invitations to my calendar" setting set to "When I respond to invitation in
   * email" or "Only if the sender is known" might have their response reset to
   * needsAction and won't see an event in their calendar unless they change
   * their response in the event invitation email. Furthermore, if more than 200
   * guests are invited to the event, response status is not propagated to the
   * guests.
   *
   * @var string
   */
  public $responseStatus;
  /**
   * Whether this entry represents the calendar on which this copy of the event
   * appears. Read-only. The default is False.
   *
   * @var bool
   */
  public $self;

  /**
   * Number of additional guests. Optional. The default is 0.
   *
   * @param int $additionalGuests
   */
  public function setAdditionalGuests($additionalGuests)
  {
    $this->additionalGuests = $additionalGuests;
  }
  /**
   * @return int
   */
  public function getAdditionalGuests()
  {
    return $this->additionalGuests;
  }
  /**
   * The attendee's response comment. Optional.
   *
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * The attendee's name, if available. Optional.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The attendee's email address, if available. This field must be present when
   * adding an attendee. It must be a valid email address as per RFC5322.
   * Required when adding an attendee.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * The attendee's Profile ID, if available.
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
   * Whether this is an optional attendee. Optional. The default is False.
   *
   * @param bool $optional
   */
  public function setOptional($optional)
  {
    $this->optional = $optional;
  }
  /**
   * @return bool
   */
  public function getOptional()
  {
    return $this->optional;
  }
  /**
   * Whether the attendee is the organizer of the event. Read-only. The default
   * is False.
   *
   * @param bool $organizer
   */
  public function setOrganizer($organizer)
  {
    $this->organizer = $organizer;
  }
  /**
   * @return bool
   */
  public function getOrganizer()
  {
    return $this->organizer;
  }
  /**
   * Whether the attendee is a resource. Can only be set when the attendee is
   * added to the event for the first time. Subsequent modifications are
   * ignored. Optional. The default is False.
   *
   * @param bool $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return bool
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The attendee's response status. Possible values are: - "needsAction" - The
   * attendee has not responded to the invitation (recommended for new events).
   * - "declined" - The attendee has declined the invitation.  - "tentative" -
   * The attendee has tentatively accepted the invitation.  - "accepted" - The
   * attendee has accepted the invitation.  Warning: If you add an event using
   * the values declined, tentative, or accepted, attendees with the "Add
   * invitations to my calendar" setting set to "When I respond to invitation in
   * email" or "Only if the sender is known" might have their response reset to
   * needsAction and won't see an event in their calendar unless they change
   * their response in the event invitation email. Furthermore, if more than 200
   * guests are invited to the event, response status is not propagated to the
   * guests.
   *
   * @param string $responseStatus
   */
  public function setResponseStatus($responseStatus)
  {
    $this->responseStatus = $responseStatus;
  }
  /**
   * @return string
   */
  public function getResponseStatus()
  {
    return $this->responseStatus;
  }
  /**
   * Whether this entry represents the calendar on which this copy of the event
   * appears. Read-only. The default is False.
   *
   * @param bool $self
   */
  public function setSelf($self)
  {
    $this->self = $self;
  }
  /**
   * @return bool
   */
  public function getSelf()
  {
    return $this->self;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventAttendee::class, 'Google_Service_Calendar_EventAttendee');
