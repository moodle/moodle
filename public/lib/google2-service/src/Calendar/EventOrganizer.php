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

class EventOrganizer extends \Google\Model
{
  /**
   * The organizer's name, if available.
   *
   * @var string
   */
  public $displayName;
  /**
   * The organizer's email address, if available. It must be a valid email
   * address as per RFC5322.
   *
   * @var string
   */
  public $email;
  /**
   * The organizer's Profile ID, if available.
   *
   * @var string
   */
  public $id;
  /**
   * Whether the organizer corresponds to the calendar on which this copy of the
   * event appears. Read-only. The default is False.
   *
   * @var bool
   */
  public $self;

  /**
   * The organizer's name, if available.
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
   * The organizer's email address, if available. It must be a valid email
   * address as per RFC5322.
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
   * The organizer's Profile ID, if available.
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
   * Whether the organizer corresponds to the calendar on which this copy of the
   * event appears. Read-only. The default is False.
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
class_alias(EventOrganizer::class, 'Google_Service_Calendar_EventOrganizer');
