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

class TaskStatusUpdateEvent extends \Google\Model
{
  /**
   * The id of the context that the task belongs to
   *
   * @var string
   */
  public $contextId;
  /**
   * Whether this is the last status update expected for this task.
   *
   * @var bool
   */
  public $final;
  /**
   * Optional metadata to associate with the task update.
   *
   * @var array[]
   */
  public $metadata;
  protected $statusType = TaskStatus::class;
  protected $statusDataType = '';
  /**
   * The id of the task that is changed
   *
   * @var string
   */
  public $taskId;

  /**
   * The id of the context that the task belongs to
   *
   * @param string $contextId
   */
  public function setContextId($contextId)
  {
    $this->contextId = $contextId;
  }
  /**
   * @return string
   */
  public function getContextId()
  {
    return $this->contextId;
  }
  /**
   * Whether this is the last status update expected for this task.
   *
   * @param bool $final
   */
  public function setFinal($final)
  {
    $this->final = $final;
  }
  /**
   * @return bool
   */
  public function getFinal()
  {
    return $this->final;
  }
  /**
   * Optional metadata to associate with the task update.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The new status of the task.
   *
   * @param TaskStatus $status
   */
  public function setStatus(TaskStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return TaskStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The id of the task that is changed
   *
   * @param string $taskId
   */
  public function setTaskId($taskId)
  {
    $this->taskId = $taskId;
  }
  /**
   * @return string
   */
  public function getTaskId()
  {
    return $this->taskId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskStatusUpdateEvent::class, 'Google_Service_WorkspaceEvents_TaskStatusUpdateEvent');
