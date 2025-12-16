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

class ReportAgentStateResponse extends \Google\Collection
{
  protected $collection_key = 'tasks';
  /**
   * Default report interval override
   *
   * @var string
   */
  public $defaultReportInterval;
  /**
   * Minimum report interval override
   *
   * @var string
   */
  public $minReportInterval;
  protected $tasksType = AgentTask::class;
  protected $tasksDataType = 'array';
  /**
   * If true, the cloud logging for batch agent will use
   * batch.googleapis.com/Job as monitored resource for Batch job related
   * logging.
   *
   * @var bool
   */
  public $useBatchMonitoredResource;

  /**
   * Default report interval override
   *
   * @param string $defaultReportInterval
   */
  public function setDefaultReportInterval($defaultReportInterval)
  {
    $this->defaultReportInterval = $defaultReportInterval;
  }
  /**
   * @return string
   */
  public function getDefaultReportInterval()
  {
    return $this->defaultReportInterval;
  }
  /**
   * Minimum report interval override
   *
   * @param string $minReportInterval
   */
  public function setMinReportInterval($minReportInterval)
  {
    $this->minReportInterval = $minReportInterval;
  }
  /**
   * @return string
   */
  public function getMinReportInterval()
  {
    return $this->minReportInterval;
  }
  /**
   * Tasks assigned to the agent
   *
   * @param AgentTask[] $tasks
   */
  public function setTasks($tasks)
  {
    $this->tasks = $tasks;
  }
  /**
   * @return AgentTask[]
   */
  public function getTasks()
  {
    return $this->tasks;
  }
  /**
   * If true, the cloud logging for batch agent will use
   * batch.googleapis.com/Job as monitored resource for Batch job related
   * logging.
   *
   * @param bool $useBatchMonitoredResource
   */
  public function setUseBatchMonitoredResource($useBatchMonitoredResource)
  {
    $this->useBatchMonitoredResource = $useBatchMonitoredResource;
  }
  /**
   * @return bool
   */
  public function getUseBatchMonitoredResource()
  {
    return $this->useBatchMonitoredResource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportAgentStateResponse::class, 'Google_Service_Batch_ReportAgentStateResponse');
