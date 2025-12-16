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

class EnterpriseTopazSidekickMeetingNotesCardError extends \Google\Model
{
  /**
   * No reason (default value).
   */
  public const REASON_NONE = 'NONE';
  /**
   * The user is not an owner.
   */
  public const REASON_NOT_OWNER = 'NOT_OWNER';
  /**
   * Unknown reason.
   */
  public const REASON_UNKNOWN = 'UNKNOWN';
  /**
   * The description of the reason why create-meeting-notes failed.
   *
   * @var string
   */
  public $description;
  protected $eventType = EnterpriseTopazSidekickAgendaEntry::class;
  protected $eventDataType = '';
  /**
   * The reason why create-meeting-notes failed.
   *
   * @var string
   */
  public $reason;

  /**
   * The description of the reason why create-meeting-notes failed.
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
   * The event to request meeting notes creation
   *
   * @param EnterpriseTopazSidekickAgendaEntry $event
   */
  public function setEvent(EnterpriseTopazSidekickAgendaEntry $event)
  {
    $this->event = $event;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaEntry
   */
  public function getEvent()
  {
    return $this->event;
  }
  /**
   * The reason why create-meeting-notes failed.
   *
   * Accepted values: NONE, NOT_OWNER, UNKNOWN
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickMeetingNotesCardError::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickMeetingNotesCardError');
