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

namespace Google\Service\CloudTasks;

class ListQueuesResponse extends \Google\Collection
{
  protected $collection_key = 'queues';
  /**
   * A token to retrieve next page of results. To return the next page of
   * results, call ListQueues with this value as the page_token. If the
   * next_page_token is empty, there are no more results. The page token is
   * valid for only 2 hours.
   *
   * @var string
   */
  public $nextPageToken;
  protected $queuesType = Queue::class;
  protected $queuesDataType = 'array';

  /**
   * A token to retrieve next page of results. To return the next page of
   * results, call ListQueues with this value as the page_token. If the
   * next_page_token is empty, there are no more results. The page token is
   * valid for only 2 hours.
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
   * The list of queues.
   *
   * @param Queue[] $queues
   */
  public function setQueues($queues)
  {
    $this->queues = $queues;
  }
  /**
   * @return Queue[]
   */
  public function getQueues()
  {
    return $this->queues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListQueuesResponse::class, 'Google_Service_CloudTasks_ListQueuesResponse');
