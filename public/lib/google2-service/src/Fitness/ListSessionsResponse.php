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

namespace Google\Service\Fitness;

class ListSessionsResponse extends \Google\Collection
{
  protected $collection_key = 'session';
  protected $deletedSessionType = Session::class;
  protected $deletedSessionDataType = 'array';
  /**
   * Flag to indicate server has more data to transfer. DO NOT USE THIS FIELD.
   * It is never populated in responses from the server.
   *
   * @deprecated
   * @var bool
   */
  public $hasMoreData;
  /**
   * The sync token which is used to sync further changes. This will only be
   * provided if both startTime and endTime are omitted from the request.
   *
   * @var string
   */
  public $nextPageToken;
  protected $sessionType = Session::class;
  protected $sessionDataType = 'array';

  /**
   * If includeDeleted is set to true in the request, and startTime and endTime
   * are omitted, this will include sessions which were deleted since the last
   * sync.
   *
   * @param Session[] $deletedSession
   */
  public function setDeletedSession($deletedSession)
  {
    $this->deletedSession = $deletedSession;
  }
  /**
   * @return Session[]
   */
  public function getDeletedSession()
  {
    return $this->deletedSession;
  }
  /**
   * Flag to indicate server has more data to transfer. DO NOT USE THIS FIELD.
   * It is never populated in responses from the server.
   *
   * @deprecated
   * @param bool $hasMoreData
   */
  public function setHasMoreData($hasMoreData)
  {
    $this->hasMoreData = $hasMoreData;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getHasMoreData()
  {
    return $this->hasMoreData;
  }
  /**
   * The sync token which is used to sync further changes. This will only be
   * provided if both startTime and endTime are omitted from the request.
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
   * Sessions starting before endTime of the request and ending after startTime
   * of the request up to (endTime of the request + 1 day).
   *
   * @param Session[] $session
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return Session[]
   */
  public function getSession()
  {
    return $this->session;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListSessionsResponse::class, 'Google_Service_Fitness_ListSessionsResponse');
