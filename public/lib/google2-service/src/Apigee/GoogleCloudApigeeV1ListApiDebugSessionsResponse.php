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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ListApiDebugSessionsResponse extends \Google\Collection
{
  protected $collection_key = 'sessions';
  /**
   * Page token that you can include in a ListApiDebugSessionsRequest to
   * retrieve the next page. If omitted, no subsequent pages exist.
   *
   * @var string
   */
  public $nextPageToken;
  protected $sessionsType = GoogleCloudApigeeV1ApiDebugSession::class;
  protected $sessionsDataType = 'array';

  /**
   * Page token that you can include in a ListApiDebugSessionsRequest to
   * retrieve the next page. If omitted, no subsequent pages exist.
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
   * Session info that includes debug session ID, environment ID, api proxy
   * revision ID and the first transaction creation timestamp.
   *
   * @param GoogleCloudApigeeV1ApiDebugSession[] $sessions
   */
  public function setSessions($sessions)
  {
    $this->sessions = $sessions;
  }
  /**
   * @return GoogleCloudApigeeV1ApiDebugSession[]
   */
  public function getSessions()
  {
    return $this->sessions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ListApiDebugSessionsResponse::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ListApiDebugSessionsResponse');
