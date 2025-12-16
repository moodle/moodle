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

namespace Google\Service\WorkflowExecutions;

class ListStepEntriesResponse extends \Google\Collection
{
  protected $collection_key = 'stepEntries';
  /**
   * A token to retrieve next page of results. Pass this value in the
   * ListStepEntriesRequest.page_token field in the subsequent call to
   * `ListStepEntries` method to retrieve the next page of results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $stepEntriesType = StepEntry::class;
  protected $stepEntriesDataType = 'array';
  /**
   * Indicates the total number of StepEntries that matched the request filter.
   * For running executions, this number shows the number of StepEntries that
   * are executed thus far.
   *
   * @var int
   */
  public $totalSize;

  /**
   * A token to retrieve next page of results. Pass this value in the
   * ListStepEntriesRequest.page_token field in the subsequent call to
   * `ListStepEntries` method to retrieve the next page of results.
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
   * The list of entries.
   *
   * @param StepEntry[] $stepEntries
   */
  public function setStepEntries($stepEntries)
  {
    $this->stepEntries = $stepEntries;
  }
  /**
   * @return StepEntry[]
   */
  public function getStepEntries()
  {
    return $this->stepEntries;
  }
  /**
   * Indicates the total number of StepEntries that matched the request filter.
   * For running executions, this number shows the number of StepEntries that
   * are executed thus far.
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
class_alias(ListStepEntriesResponse::class, 'Google_Service_WorkflowExecutions_ListStepEntriesResponse');
