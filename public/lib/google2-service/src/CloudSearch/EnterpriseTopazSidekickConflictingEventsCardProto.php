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

class EnterpriseTopazSidekickConflictingEventsCardProto extends \Google\Collection
{
  protected $collection_key = 'conflictingEvent';
  protected $conflictingEventType = EnterpriseTopazSidekickAgendaEntry::class;
  protected $conflictingEventDataType = 'array';
  protected $mainEventType = EnterpriseTopazSidekickAgendaEntry::class;
  protected $mainEventDataType = '';

  /**
   * All the events that conflict with main_event.
   *
   * @param EnterpriseTopazSidekickAgendaEntry[] $conflictingEvent
   */
  public function setConflictingEvent($conflictingEvent)
  {
    $this->conflictingEvent = $conflictingEvent;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaEntry[]
   */
  public function getConflictingEvent()
  {
    return $this->conflictingEvent;
  }
  /**
   * The event identified as being the most important.
   *
   * @param EnterpriseTopazSidekickAgendaEntry $mainEvent
   */
  public function setMainEvent(EnterpriseTopazSidekickAgendaEntry $mainEvent)
  {
    $this->mainEvent = $mainEvent;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaEntry
   */
  public function getMainEvent()
  {
    return $this->mainEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickConflictingEventsCardProto::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickConflictingEventsCardProto');
