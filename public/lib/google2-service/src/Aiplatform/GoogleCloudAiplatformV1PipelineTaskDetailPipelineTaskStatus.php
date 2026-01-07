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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1PipelineTaskDetailPipelineTaskStatus extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Specifies pending state for the task.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Specifies task is being executed.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Specifies task completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Specifies Task cancel is in pending state.
   */
  public const STATE_CANCEL_PENDING = 'CANCEL_PENDING';
  /**
   * Specifies task is being cancelled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * Specifies task was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Specifies task failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Specifies task was skipped due to cache hit.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  /**
   * Specifies that the task was not triggered because the task's trigger policy
   * is not satisfied. The trigger policy is specified in the `condition` field
   * of PipelineJob.pipeline_spec.
   */
  public const STATE_NOT_TRIGGERED = 'NOT_TRIGGERED';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * Output only. The state of the task.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Update time of this status.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The error that occurred during the state. May be set when the
   * state is any of the non-final state (PENDING/RUNNING/CANCELLING) or FAILED
   * state. If the state is FAILED, the error here is final and not going to be
   * retried. If the state is a non-final state, the error indicates a system-
   * error being retried.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The state of the task.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED,
   * CANCEL_PENDING, CANCELLING, CANCELLED, FAILED, SKIPPED, NOT_TRIGGERED
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
   * Output only. Update time of this status.
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
class_alias(GoogleCloudAiplatformV1PipelineTaskDetailPipelineTaskStatus::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1PipelineTaskDetailPipelineTaskStatus');
