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

class EventBirthdayProperties extends \Google\Model
{
  /**
   * Resource name of the contact this birthday event is linked to. This can be
   * used to fetch contact details from People API. Format: "people/c12345".
   * Read-only.
   *
   * @var string
   */
  public $contact;
  /**
   * Custom type label specified for this event. This is populated if
   * birthdayProperties.type is set to "custom". Read-only.
   *
   * @var string
   */
  public $customTypeName;
  /**
   * Type of birthday or special event. Possible values are: - "anniversary" -
   * An anniversary other than birthday. Always has a contact.  - "birthday" - A
   * birthday event. This is the default value.  - "custom" - A special date
   * whose label is further specified in the customTypeName field. Always has a
   * contact.  - "other" - A special date which does not fall into the other
   * categories, and does not have a custom label. Always has a contact.  -
   * "self" - Calendar owner's own birthday. Cannot have a contact.  The
   * Calendar API only supports creating events with the type "birthday". The
   * type cannot be changed after the event is created.
   *
   * @var string
   */
  public $type;

  /**
   * Resource name of the contact this birthday event is linked to. This can be
   * used to fetch contact details from People API. Format: "people/c12345".
   * Read-only.
   *
   * @param string $contact
   */
  public function setContact($contact)
  {
    $this->contact = $contact;
  }
  /**
   * @return string
   */
  public function getContact()
  {
    return $this->contact;
  }
  /**
   * Custom type label specified for this event. This is populated if
   * birthdayProperties.type is set to "custom". Read-only.
   *
   * @param string $customTypeName
   */
  public function setCustomTypeName($customTypeName)
  {
    $this->customTypeName = $customTypeName;
  }
  /**
   * @return string
   */
  public function getCustomTypeName()
  {
    return $this->customTypeName;
  }
  /**
   * Type of birthday or special event. Possible values are: - "anniversary" -
   * An anniversary other than birthday. Always has a contact.  - "birthday" - A
   * birthday event. This is the default value.  - "custom" - A special date
   * whose label is further specified in the customTypeName field. Always has a
   * contact.  - "other" - A special date which does not fall into the other
   * categories, and does not have a custom label. Always has a contact.  -
   * "self" - Calendar owner's own birthday. Cannot have a contact.  The
   * Calendar API only supports creating events with the type "birthday". The
   * type cannot be changed after the event is created.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventBirthdayProperties::class, 'Google_Service_Calendar_EventBirthdayProperties');
