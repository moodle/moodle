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

class StatusEvent extends \Google\Model
{
  /**
   * Unknown state.
   */
  public const TASK_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Task is created and waiting for resources.
   */
  public const TASK_STATE_PENDING = 'PENDING';
  /**
   * The Task is assigned to at least one VM.
   */
  public const TASK_STATE_ASSIGNED = 'ASSIGNED';
  /**
   * The Task is running.
   */
  public const TASK_STATE_RUNNING = 'RUNNING';
  /**
   * The Task has failed.
   */
  public const TASK_STATE_FAILED = 'FAILED';
  /**
   * The Task has succeeded.
   */
  public const TASK_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The Task has not been executed when the Job finishes.
   */
  public const TASK_STATE_UNEXECUTED = 'UNEXECUTED';
  /**
   * Description of the event.
   *
   * @var string
   */
  public $description;
  /**
   * The time this event occurred.
   *
   * @var string
   */
  public $eventTime;
  protected $taskExecutionType = TaskExecution::class;
  protected $taskExecutionDataType = '';
  /**
   * Task State. This field is only defined for task-level status events.
   *
   * @var string
   */
  public $taskState;
  /**
   * Type of the event.
   *
   * @var string
   */
  public $type;

  /**
   * Description of the event.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The time this event occurred.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Task Execution. This field is only defined for task-level status events
   * where the task fails.
   *
   * @param TaskExecution $taskExecution
   */
  public function setTaskExecution(TaskExecution $taskExecution)
  {
    $this->taskExecution = $taskExecution;
  }
  /**
   * @return TaskExecution
   */
  public function getTaskExecution()
  {
    return $this->taskExecution;
  }
  /**
   * Task State. This field is only defined for task-level status events.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, ASSIGNED, RUNNING, FAILED,
   * SUCCEEDED, UNEXECUTED
   *
   * @param self::TASK_STATE_* $taskState
   */
  public function setTaskState($taskState)
  {
    $this->taskState = $taskState;
  }
  /**
   * @return self::TASK_STATE_*
   */
  public function getTaskState()
  {
    return $this->taskState;
  }
  /**
   * Type of the event.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StatusEvent::class, 'Google_Service_Batch_StatusEvent');
