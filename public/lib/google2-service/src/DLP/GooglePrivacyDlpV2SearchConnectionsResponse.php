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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2SearchConnectionsResponse extends \Google\Collection
{
  protected $collection_key = 'connections';
  protected $connectionsType = GooglePrivacyDlpV2Connection::class;
  protected $connectionsDataType = 'array';
  /**
   * Token to retrieve the next page of results. An empty value means there are
   * no more results.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * List of connections that match the search query. Note that only a subset of
   * the fields will be populated, and only "name" is guaranteed to be set. For
   * full details of a Connection, call GetConnection with the name.
   *
   * @param GooglePrivacyDlpV2Connection[] $connections
   */
  public function setConnections($connections)
  {
    $this->connections = $connections;
  }
  /**
   * @return GooglePrivacyDlpV2Connection[]
   */
  public function getConnections()
  {
    return $this->connections;
  }
  /**
   * Token to retrieve the next page of results. An empty value means there are
   * no more results.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2SearchConnectionsResponse::class, 'Google_Service_DLP_GooglePrivacyDlpV2SearchConnectionsResponse');
