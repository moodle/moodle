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

class AgentTaskInfo extends \Google\Model
{
  /**
   * The highest index of a runnable started by the agent for this task. The
   * runnables are indexed from 1. Value 0 is undefined.
   *
   * @var string
   */
  public $runnable;
  /**
   * ID of the Task
   *
   * @var string
   */
  public $taskId;
  protected $taskStatusType = TaskStatus::class;
  protected $taskStatusDataType = '';

  /**
   * The highest index of a runnable started by the agent for this task. The
   * runnables are indexed from 1. Value 0 is undefined.
   *
   * @param string $runnable
   */
  public function setRunnable($runnable)
  {
    $this->runnable = $runnable;
  }
  /**
   * @return string
   */
  public function getRunnable()
  {
    return $this->runnable;
  }
  /**
   * ID of the Task
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
  /**
   * The status of the Task. If we need agent specific fields we should fork the
   * public TaskStatus into an agent specific one. Or add them below.
   *
   * @param TaskStatus $taskStatus
   */
  public function setTaskStatus(TaskStatus $taskStatus)
  {
    $this->taskStatus = $taskStatus;
  }
  /**
   * @return TaskStatus
   */
  public function getTaskStatus()
  {
    return $this->taskStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentTaskInfo::class, 'Google_Service_Batch_AgentTaskInfo');
