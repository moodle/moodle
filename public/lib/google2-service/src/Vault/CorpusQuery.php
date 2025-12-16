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

namespace Google\Service\Vault;

class CorpusQuery extends \Google\Model
{
  protected $calendarQueryType = HeldCalendarQuery::class;
  protected $calendarQueryDataType = '';
  protected $driveQueryType = HeldDriveQuery::class;
  protected $driveQueryDataType = '';
  protected $groupsQueryType = HeldGroupsQuery::class;
  protected $groupsQueryDataType = '';
  protected $hangoutsChatQueryType = HeldHangoutsChatQuery::class;
  protected $hangoutsChatQueryDataType = '';
  protected $mailQueryType = HeldMailQuery::class;
  protected $mailQueryDataType = '';
  protected $voiceQueryType = HeldVoiceQuery::class;
  protected $voiceQueryDataType = '';

  /**
   * Service-specific options for Calendar holds. If set, **CorpusType** must be
   * **CALENDAR**.
   *
   * @param HeldCalendarQuery $calendarQuery
   */
  public function setCalendarQuery(HeldCalendarQuery $calendarQuery)
  {
    $this->calendarQuery = $calendarQuery;
  }
  /**
   * @return HeldCalendarQuery
   */
  public function getCalendarQuery()
  {
    return $this->calendarQuery;
  }
  /**
   * Service-specific options for Drive holds. If set, **CorpusType** must be
   * **DRIVE**.
   *
   * @param HeldDriveQuery $driveQuery
   */
  public function setDriveQuery(HeldDriveQuery $driveQuery)
  {
    $this->driveQuery = $driveQuery;
  }
  /**
   * @return HeldDriveQuery
   */
  public function getDriveQuery()
  {
    return $this->driveQuery;
  }
  /**
   * Service-specific options for Groups holds. If set, **CorpusType** must be
   * **GROUPS**.
   *
   * @param HeldGroupsQuery $groupsQuery
   */
  public function setGroupsQuery(HeldGroupsQuery $groupsQuery)
  {
    $this->groupsQuery = $groupsQuery;
  }
  /**
   * @return HeldGroupsQuery
   */
  public function getGroupsQuery()
  {
    return $this->groupsQuery;
  }
  /**
   * Service-specific options for Chat holds. If set, **CorpusType** must be
   * **HANGOUTS_CHAT**.
   *
   * @param HeldHangoutsChatQuery $hangoutsChatQuery
   */
  public function setHangoutsChatQuery(HeldHangoutsChatQuery $hangoutsChatQuery)
  {
    $this->hangoutsChatQuery = $hangoutsChatQuery;
  }
  /**
   * @return HeldHangoutsChatQuery
   */
  public function getHangoutsChatQuery()
  {
    return $this->hangoutsChatQuery;
  }
  /**
   * Service-specific options for Gmail holds. If set, **CorpusType** must be
   * **MAIL**.
   *
   * @param HeldMailQuery $mailQuery
   */
  public function setMailQuery(HeldMailQuery $mailQuery)
  {
    $this->mailQuery = $mailQuery;
  }
  /**
   * @return HeldMailQuery
   */
  public function getMailQuery()
  {
    return $this->mailQuery;
  }
  /**
   * Service-specific options for Voice holds. If set, **CorpusType** must be
   * **VOICE**.
   *
   * @param HeldVoiceQuery $voiceQuery
   */
  public function setVoiceQuery(HeldVoiceQuery $voiceQuery)
  {
    $this->voiceQuery = $voiceQuery;
  }
  /**
   * @return HeldVoiceQuery
   */
  public function getVoiceQuery()
  {
    return $this->voiceQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CorpusQuery::class, 'Google_Service_Vault_CorpusQuery');
