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

class PipelineSpec extends \Google\Collection
{
  protected $collection_key = 'workspaces';
  protected $finallyTasksType = PipelineTask::class;
  protected $finallyTasksDataType = 'array';
  /**
   * Output only. auto-generated yaml that is output only for display purpose
   * for workflows using pipeline_spec, used by UI/gcloud cli for Workflows.
   *
   * @var string
   */
  public $generatedYaml;
  protected $paramsType = ParamSpec::class;
  protected $paramsDataType = 'array';
  protected $resultsType = PipelineResult::class;
  protected $resultsDataType = 'array';
  protected $tasksType = PipelineTask::class;
  protected $tasksDataType = 'array';
  protected $workspacesType = PipelineWorkspaceDeclaration::class;
  protected $workspacesDataType = 'array';

  /**
   * List of Tasks that execute just before leaving the Pipeline i.e. either
   * after all Tasks are finished executing successfully or after a failure
   * which would result in ending the Pipeline.
   *
   * @param PipelineTask[] $finallyTasks
   */
  public function setFinallyTasks($finallyTasks)
  {
    $this->finallyTasks = $finallyTasks;
  }
  /**
   * @return PipelineTask[]
   */
  public function getFinallyTasks()
  {
    return $this->finallyTasks;
  }
  /**
   * Output only. auto-generated yaml that is output only for display purpose
   * for workflows using pipeline_spec, used by UI/gcloud cli for Workflows.
   *
   * @param string $generatedYaml
   */
  public function setGeneratedYaml($generatedYaml)
  {
    $this->generatedYaml = $generatedYaml;
  }
  /**
   * @return string
   */
  public function getGeneratedYaml()
  {
    return $this->generatedYaml;
  }
  /**
   * List of parameters.
   *
   * @param ParamSpec[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return ParamSpec[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Optional. Output only. List of results written out by the pipeline's
   * containers
   *
   * @param PipelineResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return PipelineResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * List of Tasks that execute when this Pipeline is run.
   *
   * @param PipelineTask[] $tasks
   */
  public function setTasks($tasks)
  {
    $this->tasks = $tasks;
  }
  /**
   * @return PipelineTask[]
   */
  public function getTasks()
  {
    return $this->tasks;
  }
  /**
   * Workspaces declares a set of named workspaces that are expected to be
   * provided by a PipelineRun.
   *
   * @param PipelineWorkspaceDeclaration[] $workspaces
   */
  public function setWorkspaces($workspaces)
  {
    $this->workspaces = $workspaces;
  }
  /**
   * @return PipelineWorkspaceDeclaration[]
   */
  public function getWorkspaces()
  {
    return $this->workspaces;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PipelineSpec::class, 'Google_Service_CloudBuild_PipelineSpec');
