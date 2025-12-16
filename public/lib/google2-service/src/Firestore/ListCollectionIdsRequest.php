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

namespace Google\Service\Firestore;

class ListCollectionIdsRequest extends \Google\Model
{
  /**
   * The maximum number of results to return.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A page token. Must be a value from ListCollectionIdsResponse.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Reads documents as they were at the given time. This must be a microsecond
   * precision timestamp within the past one hour, or if Point-in-Time Recovery
   * is enabled, can additionally be a whole minute timestamp within the past 7
   * days.
   *
   * @var string
   */
  public $readTime;

  /**
   * The maximum number of results to return.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * A page token. Must be a value from ListCollectionIdsResponse.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Reads documents as they were at the given time. This must be a microsecond
   * precision timestamp within the past one hour, or if Point-in-Time Recovery
   * is enabled, can additionally be a whole minute timestamp within the past 7
   * days.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListCollectionIdsRequest::class, 'Google_Service_Firestore_ListCollectionIdsRequest');
