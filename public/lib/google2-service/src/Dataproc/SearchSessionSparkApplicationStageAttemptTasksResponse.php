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

namespace Google\Service\Dataproc;

class SearchSessionSparkApplicationStageAttemptTasksResponse extends \Google\Collection
{
  protected $collection_key = 'sparkApplicationStageAttemptTasks';
  /**
   * This token is included in the response if there are more results to fetch.
   * To fetch additional results, provide this value as the page_token in a
   * subsequent SearchSessionSparkApplicationStageAttemptTasksRequest.
   *
   * @var string
   */
  public $nextPageToken;
  protected $sparkApplicationStageAttemptTasksType = TaskData::class;
  protected $sparkApplicationStageAttemptTasksDataType = 'array';

  /**
   * This token is included in the response if there are more results to fetch.
   * To fetch additional results, provide this value as the page_token in a
   * subsequent SearchSessionSparkApplicationStageAttemptTasksRequest.
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
   * Output only. Data corresponding to tasks created by spark.
   *
   * @param TaskData[] $sparkApplicationStageAttemptTasks
   */
  public function setSparkApplicationStageAttemptTasks($sparkApplicationStageAttemptTasks)
  {
    $this->sparkApplicationStageAttemptTasks = $sparkApplicationStageAttemptTasks;
  }
  /**
   * @return TaskData[]
   */
  public function getSparkApplicationStageAttemptTasks()
  {
    return $this->sparkApplicationStageAttemptTasks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchSessionSparkApplicationStageAttemptTasksResponse::class, 'Google_Service_Dataproc_SearchSessionSparkApplicationStageAttemptTasksResponse');
