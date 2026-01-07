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

class AgentInfo extends \Google\Collection
{
  /**
   * Unspecified state.
   */
  public const STATE_AGENT_STATE_UNSPECIFIED = 'AGENT_STATE_UNSPECIFIED';
  /**
   * The agent is starting on the VM instance.
   */
  public const STATE_AGENT_STARTING = 'AGENT_STARTING';
  /**
   * The agent is running. The agent in the RUNNING state can never go back to
   * the STARTING state.
   */
  public const STATE_AGENT_RUNNING = 'AGENT_RUNNING';
  /**
   * The agent has stopped, either on request or due to a failure.
   */
  public const STATE_AGENT_STOPPED = 'AGENT_STOPPED';
  protected $collection_key = 'tasks';
  /**
   * Optional. The assigned Job ID
   *
   * @var string
   */
  public $jobId;
  /**
   * When the AgentInfo is generated.
   *
   * @var string
   */
  public $reportTime;
  /**
   * Agent state.
   *
   * @var string
   */
  public $state;
  /**
   * The assigned task group ID.
   *
   * @var string
   */
  public $taskGroupId;
  protected $tasksType = AgentTaskInfo::class;
  protected $tasksDataType = 'array';

  /**
   * Optional. The assigned Job ID
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * When the AgentInfo is generated.
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
  /**
   * Agent state.
   *
   * Accepted values: AGENT_STATE_UNSPECIFIED, AGENT_STARTING, AGENT_RUNNING,
   * AGENT_STOPPED
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
   * The assigned task group ID.
   *
   * @param string $taskGroupId
   */
  public function setTaskGroupId($taskGroupId)
  {
    $this->taskGroupId = $taskGroupId;
  }
  /**
   * @return string
   */
  public function getTaskGroupId()
  {
    return $this->taskGroupId;
  }
  /**
   * Task Info.
   *
   * @param AgentTaskInfo[] $tasks
   */
  public function setTasks($tasks)
  {
    $this->tasks = $tasks;
  }
  /**
   * @return AgentTaskInfo[]
   */
  public function getTasks()
  {
    return $this->tasks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentInfo::class, 'Google_Service_Batch_AgentInfo');
