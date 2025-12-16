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

class CreateConferenceRequest extends \Google\Model
{
  protected $conferenceSolutionKeyType = ConferenceSolutionKey::class;
  protected $conferenceSolutionKeyDataType = '';
  /**
   * The client-generated unique ID for this request. Clients should regenerate
   * this ID for every new request. If an ID provided is the same as for the
   * previous request, the request is ignored.
   *
   * @var string
   */
  public $requestId;
  protected $statusType = ConferenceRequestStatus::class;
  protected $statusDataType = '';

  /**
   * The conference solution, such as Hangouts or Google Meet.
   *
   * @param ConferenceSolutionKey $conferenceSolutionKey
   */
  public function setConferenceSolutionKey(ConferenceSolutionKey $conferenceSolutionKey)
  {
    $this->conferenceSolutionKey = $conferenceSolutionKey;
  }
  /**
   * @return ConferenceSolutionKey
   */
  public function getConferenceSolutionKey()
  {
    return $this->conferenceSolutionKey;
  }
  /**
   * The client-generated unique ID for this request. Clients should regenerate
   * this ID for every new request. If an ID provided is the same as for the
   * previous request, the request is ignored.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * The status of the conference create request.
   *
   * @param ConferenceRequestStatus $status
   */
  public function setStatus(ConferenceRequestStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ConferenceRequestStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateConferenceRequest::class, 'Google_Service_Calendar_CreateConferenceRequest');
