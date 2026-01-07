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

namespace Google\Service\ToolResults;

class ListEnvironmentsResponse extends \Google\Collection
{
  protected $collection_key = 'environments';
  protected $environmentsType = Environment::class;
  protected $environmentsDataType = 'array';
  /**
   * A Execution id Always set.
   *
   * @var string
   */
  public $executionId;
  /**
   * A History id. Always set.
   *
   * @var string
   */
  public $historyId;
  /**
   * A continuation token to resume the query at the next item. Will only be set
   * if there are more Environments to fetch.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * A Project id. Always set.
   *
   * @var string
   */
  public $projectId;

  /**
   * Environments. Always set.
   *
   * @param Environment[] $environments
   */
  public function setEnvironments($environments)
  {
    $this->environments = $environments;
  }
  /**
   * @return Environment[]
   */
  public function getEnvironments()
  {
    return $this->environments;
  }
  /**
   * A Execution id Always set.
   *
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
  /**
   * A History id. Always set.
   *
   * @param string $historyId
   */
  public function setHistoryId($historyId)
  {
    $this->historyId = $historyId;
  }
  /**
   * @return string
   */
  public function getHistoryId()
  {
    return $this->historyId;
  }
  /**
   * A continuation token to resume the query at the next item. Will only be set
   * if there are more Environments to fetch.
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
   * A Project id. Always set.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListEnvironmentsResponse::class, 'Google_Service_ToolResults_ListEnvironmentsResponse');
