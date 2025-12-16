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

class EnterpriseTopazSidekickMeetingNotesCardRequest extends \Google\Collection
{
  protected $collection_key = 'canCreateFor';
  /**
   * Who are the meeting notes created for.
   *
   * @var string[]
   */
  public $canCreateFor;
  protected $errorType = EnterpriseTopazSidekickMeetingNotesCardError::class;
  protected $errorDataType = '';
  protected $eventType = EnterpriseTopazSidekickAgendaEntry::class;
  protected $eventDataType = '';

  /**
   * Who are the meeting notes created for.
   *
   * @param string[] $canCreateFor
   */
  public function setCanCreateFor($canCreateFor)
  {
    $this->canCreateFor = $canCreateFor;
  }
  /**
   * @return string[]
   */
  public function getCanCreateFor()
  {
    return $this->canCreateFor;
  }
  /**
   * The error and reason if known error occured.
   *
   * @param EnterpriseTopazSidekickMeetingNotesCardError $error
   */
  public function setError(EnterpriseTopazSidekickMeetingNotesCardError $error)
  {
    $this->error = $error;
  }
  /**
   * @return EnterpriseTopazSidekickMeetingNotesCardError
   */
  public function getError()
  {
    return $this->error;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickMeetingNotesCardRequest::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickMeetingNotesCardRequest');
