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

namespace Google\Service\Batch;

class Message extends \Google\Model
{
  /**
   * Job state unspecified.
   */
  public const NEW_JOB_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Job is admitted (validated and persisted) and waiting for resources.
   */
  public const NEW_JOB_STATE_QUEUED = 'QUEUED';
  /**
   * Job is scheduled to run as soon as resource allocation is ready. The
   * resource allocation may happen at a later time but with a high chance to
   * succeed.
   */
  public const NEW_JOB_STATE_SCHEDULED = 'SCHEDULED';
  /**
   * Resource allocation has been successful. At least one Task in the Job is
   * RUNNING.
   */
  public const NEW_JOB_STATE_RUNNING = 'RUNNING';
  /**
   * All Tasks in the Job have finished successfully.
   */
  public const NEW_JOB_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * At least one Task in the Job has failed.
   */
  public const NEW_JOB_STATE_FAILED = 'FAILED';
  /**
   * The Job will be deleted, but has not been deleted yet. Typically this is
   * because resources used by the Job are still being cleaned up.
   */
  public const NEW_JOB_STATE_DELETION_IN_PROGRESS = 'DELETION_IN_PROGRESS';
  /**
   * The Job cancellation is in progress, this is because the resources used by
   * the Job are still being cleaned up.
   */
  public const NEW_JOB_STATE_CANCELLATION_IN_PROGRESS = 'CANCELLATION_IN_PROGRESS';
  /**
   * The Job has been cancelled, the task executions were stopped and the
   * resources were cleaned up.
   */
  public const NEW_JOB_STATE_CANCELLED = 'CANCELLED';
  /**
   * Unknown state.
   */
  public const NEW_TASK_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Task is created and waiting for resources.
   */
  public const NEW_TASK_STATE_PENDING = 'PENDING';
  /**
   * The Task is assigned to at least one VM.
   */
  public const NEW_TASK_STATE_ASSIGNED = 'ASSIGNED';
  /**
   * The Task is running.
   */
  public const NEW_TASK_STATE_RUNNING = 'RUNNING';
  /**
   * The Task has failed.
   */
  public const NEW_TASK_STATE_FAILED = 'FAILED';
  /**
   * The Task has succeeded.
   */
  public const NEW_TASK_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The Task has not been executed when the Job finishes.
   */
  public const NEW_TASK_STATE_UNEXECUTED = 'UNEXECUTED';
  /**
   * Unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Notify users that the job state has changed.
   */
  public const TYPE_JOB_STATE_CHANGED = 'JOB_STATE_CHANGED';
  /**
   * Notify users that the task state has changed.
   */
  public const TYPE_TASK_STATE_CHANGED = 'TASK_STATE_CHANGED';
  /**
   * The new job state.
   *
   * @var string
   */
  public $newJobState;
  /**
   * The new task state.
   *
   * @var string
   */
  public $newTaskState;
  /**
   * The message type.
   *
   * @var string
   */
  public $type;

  /**
   * The new job state.
   *
   * Accepted values: STATE_UNSPECIFIED, QUEUED, SCHEDULED, RUNNING, SUCCEEDED,
   * FAILED, DELETION_IN_PROGRESS, CANCELLATION_IN_PROGRESS, CANCELLED
   *
   * @param self::NEW_JOB_STATE_* $newJobState
   */
  public function setNewJobState($newJobState)
  {
    $this->newJobState = $newJobState;
  }
  /**
   * @return self::NEW_JOB_STATE_*
   */
  public function getNewJobState()
  {
    return $this->newJobState;
  }
  /**
   * The new task state.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, ASSIGNED, RUNNING, FAILED,
   * SUCCEEDED, UNEXECUTED
   *
   * @param self::NEW_TASK_STATE_* $newTaskState
   */
  public function setNewTaskState($newTaskState)
  {
    $this->newTaskState = $newTaskState;
  }
  /**
   * @return self::NEW_TASK_STATE_*
   */
  public function getNewTaskState()
  {
    return $this->newTaskState;
  }
  /**
   * The message type.
   *
   * Accepted values: TYPE_UNSPECIFIED, JOB_STATE_CHANGED, TASK_STATE_CHANGED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_Batch_Message');
