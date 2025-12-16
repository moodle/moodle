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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1LastExecution extends \Google\Model
{
  /**
   * Default unspecified execution result.
   */
  public const RESULT_RESULT_UNSPECIFIED = 'RESULT_UNSPECIFIED';
  /**
   * The plugin instance executed successfully.
   */
  public const RESULT_SUCCEEDED = 'SUCCEEDED';
  /**
   * The plugin instance execution failed.
   */
  public const RESULT_FAILED = 'FAILED';
  /**
   * Output only. The last execution end time of the plugin instance.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Error message describing the failure, if any, during the last
   * execution.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Output only. The result of the last execution of the plugin instance.
   *
   * @var string
   */
  public $result;
  /**
   * Output only. The result metadata of the last execution of the plugin
   * instance. This will be a string representation of a JSON object and will be
   * available on successful execution.
   *
   * @var string
   */
  public $resultMetadata;
  /**
   * Output only. The last execution start time of the plugin instance.
   *
   * @var string
   */
  public $startTime;

  /**
   * Output only. The last execution end time of the plugin instance.
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
   * Output only. Error message describing the failure, if any, during the last
   * execution.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Output only. The result of the last execution of the plugin instance.
   *
   * Accepted values: RESULT_UNSPECIFIED, SUCCEEDED, FAILED
   *
   * @param self::RESULT_* $result
   */
  public function setResult($result)
  {
    $this->result = $result;
  }
  /**
   * @return self::RESULT_*
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * Output only. The result metadata of the last execution of the plugin
   * instance. This will be a string representation of a JSON object and will be
   * available on successful execution.
   *
   * @param string $resultMetadata
   */
  public function setResultMetadata($resultMetadata)
  {
    $this->resultMetadata = $resultMetadata;
  }
  /**
   * @return string
   */
  public function getResultMetadata()
  {
    return $this->resultMetadata;
  }
  /**
   * Output only. The last execution start time of the plugin instance.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1LastExecution::class, 'Google_Service_APIhub_GoogleCloudApihubV1LastExecution');
