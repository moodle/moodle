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

class ListParticipantsResponse extends \Google\Collection
{
  protected $collection_key = 'participants';
  /**
   * Token to be circulated back for further List call if current List doesn't
   * include all the participants. Unset if all participants are returned.
   *
   * @var string
   */
  public $nextPageToken;
  protected $participantsType = Participant::class;
  protected $participantsDataType = 'array';
  /**
   * Total, exact number of `participants`. By default, this field isn't
   * included in the response. Set the field mask in
   * [SystemParameterContext](https://cloud.google.com/apis/docs/system-
   * parameters) to receive this field in the response.
   *
   * @var int
   */
  public $totalSize;

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
   * @param Participant[] $participants
   */
  public function setParticipants($participants)
  {
    $this->participants = $participants;
  }
  /**
   * @return Participant[]
   */
  public function getParticipants()
  {
    return $this->participants;
  }
  /**
   * Total, exact number of `participants`. By default, this field isn't
   * included in the response. Set the field mask in
   * [SystemParameterContext](https://cloud.google.com/apis/docs/system-
   * parameters) to receive this field in the response.
   *
   * @param int $totalSize
   */
  public function setTotalSize($totalSize)
  {
    $this->totalSize = $totalSize;
  }
  /**
   * @return int
   */
  public function getTotalSize()
  {
    return $this->totalSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListParticipantsResponse::class, 'Google_Service_Meet_ListParticipantsResponse');
