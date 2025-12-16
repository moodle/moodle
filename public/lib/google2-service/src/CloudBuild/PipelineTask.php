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

namespace Google\Service\CloudBuild;

class PipelineTask extends \Google\Collection
{
  protected $collection_key = 'workspaces';
  /**
   * Name of the task.
   *
   * @var string
   */
  public $name;
  protected $paramsType = Param::class;
  protected $paramsDataType = 'array';
  /**
   * Retries represents how many times this task should be retried in case of
   * task failure.
   *
   * @var int
   */
  public $retries;
  /**
   * RunAfter is the list of PipelineTask names that should be executed before
   * this Task executes. (Used to force a specific ordering in graph execution.)
   *
   * @var string[]
   */
  public $runAfter;
  protected $taskRefType = TaskRef::class;
  protected $taskRefDataType = '';
  protected $taskSpecType = EmbeddedTask::class;
  protected $taskSpecDataType = '';
  /**
   * Time after which the TaskRun times out. Defaults to 1 hour. Specified
   * TaskRun timeout should be less than 24h.
   *
   * @var string
   */
  public $timeout;
  protected $whenExpressionsType = WhenExpression::class;
  protected $whenExpressionsDataType = 'array';
  protected $workspacesType = WorkspacePipelineTaskBinding::class;
  protected $workspacesDataType = 'array';

  /**
   * Name of the task.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Params is a list of parameter names and values.
   *
   * @param Param[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return Param[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Retries represents how many times this task should be retried in case of
   * task failure.
   *
   * @param int $retries
   */
  public function setRetries($retries)
  {
    $this->retries = $retries;
  }
  /**
   * @return int
   */
  public function getRetries()
  {
    return $this->retries;
  }
  /**
   * RunAfter is the list of PipelineTask names that should be executed before
   * this Task executes. (Used to force a specific ordering in graph execution.)
   *
   * @param string[] $runAfter
   */
  public function setRunAfter($runAfter)
  {
    $this->runAfter = $runAfter;
  }
  /**
   * @return string[]
   */
  public function getRunAfter()
  {
    return $this->runAfter;
  }
  /**
   * Reference to a specific instance of a task.
   *
   * @param TaskRef $taskRef
   */
  public function setTaskRef(TaskRef $taskRef)
  {
    $this->taskRef = $taskRef;
  }
  /**
   * @return TaskRef
   */
  public function getTaskRef()
  {
    return $this->taskRef;
  }
  /**
   * Spec to instantiate this TaskRun.
   *
   * @param EmbeddedTask $taskSpec
   */
  public function setTaskSpec(EmbeddedTask $taskSpec)
  {
    $this->taskSpec = $taskSpec;
  }
  /**
   * @return EmbeddedTask
   */
  public function getTaskSpec()
  {
    return $this->taskSpec;
  }
  /**
   * Time after which the TaskRun times out. Defaults to 1 hour. Specified
   * TaskRun timeout should be less than 24h.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Conditions that need to be true for the task to run.
   *
   * @param WhenExpression[] $whenExpressions
   */
  public function setWhenExpressions($whenExpressions)
  {
    $this->whenExpressions = $whenExpressions;
  }
  /**
   * @return WhenExpression[]
   */
  public function getWhenExpressions()
  {
    return $this->whenExpressions;
  }
  /**
   * Workspaces maps workspaces from the pipeline spec to the workspaces
   * declared in the Task.
   *
   * @param WorkspacePipelineTaskBinding[] $workspaces
   */
  public function setWorkspaces($workspaces)
  {
    $this->workspaces = $workspaces;
  }
  /**
   * @return WorkspacePipelineTaskBinding[]
   */
  public function getWorkspaces()
  {
    return $this->workspaces;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PipelineTask::class, 'Google_Service_CloudBuild_PipelineTask');
