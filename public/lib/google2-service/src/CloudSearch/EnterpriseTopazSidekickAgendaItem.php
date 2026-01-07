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

class EnterpriseTopazSidekickAgendaItem extends \Google\Model
{
  protected $conflictedGroupType = EnterpriseTopazSidekickConflictingEventsCardProto::class;
  protected $conflictedGroupDataType = '';
  protected $gapBeforeType = EnterpriseTopazSidekickGap::class;
  protected $gapBeforeDataType = '';
  protected $meetingType = EnterpriseTopazSidekickAgendaEntry::class;
  protected $meetingDataType = '';

  /**
   * @param EnterpriseTopazSidekickConflictingEventsCardProto $conflictedGroup
   */
  public function setConflictedGroup(EnterpriseTopazSidekickConflictingEventsCardProto $conflictedGroup)
  {
    $this->conflictedGroup = $conflictedGroup;
  }
  /**
   * @return EnterpriseTopazSidekickConflictingEventsCardProto
   */
  public function getConflictedGroup()
  {
    return $this->conflictedGroup;
  }
  /**
   * @param EnterpriseTopazSidekickGap $gapBefore
   */
  public function setGapBefore(EnterpriseTopazSidekickGap $gapBefore)
  {
    $this->gapBefore = $gapBefore;
  }
  /**
   * @return EnterpriseTopazSidekickGap
   */
  public function getGapBefore()
  {
    return $this->gapBefore;
  }
  /**
   * @param EnterpriseTopazSidekickAgendaEntry $meeting
   */
  public function setMeeting(EnterpriseTopazSidekickAgendaEntry $meeting)
  {
    $this->meeting = $meeting;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaEntry
   */
  public function getMeeting()
  {
    return $this->meeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickAgendaItem::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickAgendaItem');
