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

namespace Google\Service\Gmail;

class ListThreadsResponse extends \Google\Collection
{
  protected $collection_key = 'threads';
  /**
   * Page token to retrieve the next page of results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Estimated total number of results.
   *
   * @var string
   */
  public $resultSizeEstimate;
  protected $threadsType = Thread::class;
  protected $threadsDataType = 'array';

  /**
   * Page token to retrieve the next page of results in the list.
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
   * Estimated total number of results.
   *
   * @param string $resultSizeEstimate
   */
  public function setResultSizeEstimate($resultSizeEstimate)
  {
    $this->resultSizeEstimate = $resultSizeEstimate;
  }
  /**
   * @return string
   */
  public function getResultSizeEstimate()
  {
    return $this->resultSizeEstimate;
  }
  /**
   * List of threads. Note that each thread resource does not contain a list of
   * `messages`. The list of `messages` for a given thread can be fetched using
   * the [`threads.get`](https://developers.google.com/workspace/gmail/api/v1/re
   * ference/users/threads/get) method.
   *
   * @param Thread[] $threads
   */
  public function setThreads($threads)
  {
    $this->threads = $threads;
  }
  /**
   * @return Thread[]
   */
  public function getThreads()
  {
    return $this->threads;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListThreadsResponse::class, 'Google_Service_Gmail_ListThreadsResponse');
