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

namespace Google\Service\WorkspaceEvents;

class TaskStatus extends \Google\Model
{
  public const STATE_TASK_STATE_UNSPECIFIED = 'TASK_STATE_UNSPECIFIED';
  /**
   * Represents the status that acknowledges a task is created
   */
  public const STATE_TASK_STATE_SUBMITTED = 'TASK_STATE_SUBMITTED';
  /**
   * Represents the status that a task is actively being processed
   */
  public const STATE_TASK_STATE_WORKING = 'TASK_STATE_WORKING';
  /**
   * Represents the status a task is finished. This is a terminal state
   */
  public const STATE_TASK_STATE_COMPLETED = 'TASK_STATE_COMPLETED';
  /**
   * Represents the status a task is done but failed. This is a terminal state
   */
  public const STATE_TASK_STATE_FAILED = 'TASK_STATE_FAILED';
  /**
   * Represents the status a task was cancelled before it finished. This is a
   * terminal state.
   */
  public const STATE_TASK_STATE_CANCELLED = 'TASK_STATE_CANCELLED';
  /**
   * Represents the status that the task requires information to complete. This
   * is an interrupted state.
   */
  public const STATE_TASK_STATE_INPUT_REQUIRED = 'TASK_STATE_INPUT_REQUIRED';
  /**
   * Represents the status that the agent has decided to not perform the task.
   * This may be done during initial task creation or later once an agent has
   * determined it can't or won't proceed. This is a terminal state.
   */
  public const STATE_TASK_STATE_REJECTED = 'TASK_STATE_REJECTED';
  /**
   * Represents the state that some authentication is needed from the upstream
   * client. Authentication is expected to come out-of-band thus this is not an
   * interrupted or terminal state.
   */
  public const STATE_TASK_STATE_AUTH_REQUIRED = 'TASK_STATE_AUTH_REQUIRED';
  protected $messageType = Message::class;
  protected $messageDataType = '';
  /**
   * The current state of this task
   *
   * @var string
   */
  public $state;
  /**
   * Timestamp when the status was recorded. Example: "2023-10-27T10:00:00Z"
   *
   * @var string
   */
  public $timestamp;

  /**
   * A message associated with the status.
   *
   * @param Message $message
   */
  public function setMessage(Message $message)
  {
    $this->message = $message;
  }
  /**
   * @return Message
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The current state of this task
   *
   * Accepted values: TASK_STATE_UNSPECIFIED, TASK_STATE_SUBMITTED,
   * TASK_STATE_WORKING, TASK_STATE_COMPLETED, TASK_STATE_FAILED,
   * TASK_STATE_CANCELLED, TASK_STATE_INPUT_REQUIRED, TASK_STATE_REJECTED,
   * TASK_STATE_AUTH_REQUIRED
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
   * Timestamp when the status was recorded. Example: "2023-10-27T10:00:00Z"
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskStatus::class, 'Google_Service_WorkspaceEvents_TaskStatus');
