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

namespace Google\Service\CloudKMS;

class ListEkmConnectionsResponse extends \Google\Collection
{
  protected $collection_key = 'ekmConnections';
  protected $ekmConnectionsType = EkmConnection::class;
  protected $ekmConnectionsDataType = 'array';
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListEkmConnectionsRequest.page_token to retrieve the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * The total number of EkmConnections that matched the query. This field is
   * not populated if ListEkmConnectionsRequest.filter is applied.
   *
   * @var int
   */
  public $totalSize;

  /**
   * The list of EkmConnections.
   *
   * @param EkmConnection[] $ekmConnections
   */
  public function setEkmConnections($ekmConnections)
  {
    $this->ekmConnections = $ekmConnections;
  }
  /**
   * @return EkmConnection[]
   */
  public function getEkmConnections()
  {
    return $this->ekmConnections;
  }
  /**
   * A token to retrieve next page of results. Pass this value in
   * ListEkmConnectionsRequest.page_token to retrieve the next page of results.
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
   * The total number of EkmConnections that matched the query. This field is
   * not populated if ListEkmConnectionsRequest.filter is applied.
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
class_alias(ListEkmConnectionsResponse::class, 'Google_Service_CloudKMS_ListEkmConnectionsResponse');
