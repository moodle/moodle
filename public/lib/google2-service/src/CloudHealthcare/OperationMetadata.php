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

namespace Google\Service\CloudHealthcare;

class OperationMetadata extends \Google\Model
{
  /**
   * The name of the API method that initiated the operation.
   *
   * @var string
   */
  public $apiMethodName;
  /**
   * Specifies if cancellation was requested for the operation.
   *
   * @var bool
   */
  public $cancelRequested;
  protected $counterType = ProgressCounter::class;
  protected $counterDataType = '';
  /**
   * The time at which the operation was created by the API.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time at which execution was completed.
   *
   * @var string
   */
  public $endTime;
  /**
   * A link to audit and error logs in the log viewer. Error logs are generated
   * only by some operations, listed at [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging).
   *
   * @var string
   */
  public $logsUrl;

  /**
   * The name of the API method that initiated the operation.
   *
   * @param string $apiMethodName
   */
  public function setApiMethodName($apiMethodName)
  {
    $this->apiMethodName = $apiMethodName;
  }
  /**
   * @return string
   */
  public function getApiMethodName()
  {
    return $this->apiMethodName;
  }
  /**
   * Specifies if cancellation was requested for the operation.
   *
   * @param bool $cancelRequested
   */
  public function setCancelRequested($cancelRequested)
  {
    $this->cancelRequested = $cancelRequested;
  }
  /**
   * @return bool
   */
  public function getCancelRequested()
  {
    return $this->cancelRequested;
  }
  /**
   * @param ProgressCounter $counter
   */
  public function setCounter(ProgressCounter $counter)
  {
    $this->counter = $counter;
  }
  /**
   * @return ProgressCounter
   */
  public function getCounter()
  {
    return $this->counter;
  }
  /**
   * The time at which the operation was created by the API.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The time at which execution was completed.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * A link to audit and error logs in the log viewer. Error logs are generated
   * only by some operations, listed at [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging).
   *
   * @param string $logsUrl
   */
  public function setLogsUrl($logsUrl)
  {
    $this->logsUrl = $logsUrl;
  }
  /**
   * @return string
   */
  public function getLogsUrl()
  {
    return $this->logsUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OperationMetadata::class, 'Google_Service_CloudHealthcare_OperationMetadata');
