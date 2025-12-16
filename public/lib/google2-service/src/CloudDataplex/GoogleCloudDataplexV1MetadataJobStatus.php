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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1MetadataJobStatus extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The job is queued.
   */
  public const STATE_QUEUED = 'QUEUED';
  /**
   * The job is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The job is being canceled.
   */
  public const STATE_CANCELING = 'CANCELING';
  /**
   * The job is canceled.
   */
  public const STATE_CANCELED = 'CANCELED';
  /**
   * The job succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The job failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The job completed with some errors.
   */
  public const STATE_SUCCEEDED_WITH_ERRORS = 'SUCCEEDED_WITH_ERRORS';
  /**
   * Output only. Progress tracking.
   *
   * @var int
   */
  public $completionPercent;
  /**
   * Output only. Message relating to the progression of a metadata job.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. State of the metadata job.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time when the status was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Progress tracking.
   *
   * @param int $completionPercent
   */
  public function setCompletionPercent($completionPercent)
  {
    $this->completionPercent = $completionPercent;
  }
  /**
   * @return int
   */
  public function getCompletionPercent()
  {
    return $this->completionPercent;
  }
  /**
   * Output only. Message relating to the progression of a metadata job.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Output only. State of the metadata job.
   *
   * Accepted values: STATE_UNSPECIFIED, QUEUED, RUNNING, CANCELING, CANCELED,
   * SUCCEEDED, FAILED, SUCCEEDED_WITH_ERRORS
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The time when the status was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1MetadataJobStatus::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1MetadataJobStatus');
