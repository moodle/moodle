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

namespace Google\Service\Meet;

class ListParticipantSessionsResponse extends \Google\Collection
{
  protected $collection_key = 'participantSessions';
  /**
   * Token to be circulated back for further List call if current List doesn't
   * include all the participants. Unset if all participants are returned.
   *
   * @var string
   */
  public $nextPageToken;
  protected $participantSessionsType = ParticipantSession::class;
  protected $participantSessionsDataType = 'array';

  /**
   * Token to be circulated back for further List call if current List doesn't
   * include all the participants. Unset if all participants are returned.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * List of participants in one page.
   *
   * @param ParticipantSession[] $participantSessions
   */
  public function setParticipantSessions($participantSessions)
  {
    $this->participantSessions = $participantSessions;
  }
  /**
   * @return ParticipantSession[]
   */
  public function getParticipantSessions()
  {
    return $this->participantSessions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListParticipantSessionsResponse::class, 'Google_Service_Meet_ListParticipantSessionsResponse');
