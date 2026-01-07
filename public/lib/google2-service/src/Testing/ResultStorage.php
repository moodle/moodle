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

namespace Google\Service\Testing;

class ResultStorage extends \Google\Model
{
  protected $googleCloudStorageType = GoogleCloudStorage::class;
  protected $googleCloudStorageDataType = '';
  /**
   * Output only. URL to the results in the Firebase Web Console.
   *
   * @var string
   */
  public $resultsUrl;
  protected $toolResultsExecutionType = ToolResultsExecution::class;
  protected $toolResultsExecutionDataType = '';
  protected $toolResultsHistoryType = ToolResultsHistory::class;
  protected $toolResultsHistoryDataType = '';

  /**
   * Required.
   *
   * @param GoogleCloudStorage $googleCloudStorage
   */
  public function setGoogleCloudStorage(GoogleCloudStorage $googleCloudStorage)
  {
    $this->googleCloudStorage = $googleCloudStorage;
  }
  /**
   * @return GoogleCloudStorage
   */
  public function getGoogleCloudStorage()
  {
    return $this->googleCloudStorage;
  }
  /**
   * Output only. URL to the results in the Firebase Web Console.
   *
   * @param string $resultsUrl
   */
  public function setResultsUrl($resultsUrl)
  {
    $this->resultsUrl = $resultsUrl;
  }
  /**
   * @return string
   */
  public function getResultsUrl()
  {
    return $this->resultsUrl;
  }
  /**
   * Output only. The tool results execution that results are written to.
   *
   * @param ToolResultsExecution $toolResultsExecution
   */
  public function setToolResultsExecution(ToolResultsExecution $toolResultsExecution)
  {
    $this->toolResultsExecution = $toolResultsExecution;
  }
  /**
   * @return ToolResultsExecution
   */
  public function getToolResultsExecution()
  {
    return $this->toolResultsExecution;
  }
  /**
   * The tool results history that contains the tool results execution that
   * results are written to. If not provided, the service will choose an
   * appropriate value.
   *
   * @param ToolResultsHistory $toolResultsHistory
   */
  public function setToolResultsHistory(ToolResultsHistory $toolResultsHistory)
  {
    $this->toolResultsHistory = $toolResultsHistory;
  }
  /**
   * @return ToolResultsHistory
   */
  public function getToolResultsHistory()
  {
    return $this->toolResultsHistory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResultStorage::class, 'Google_Service_Testing_ResultStorage');
