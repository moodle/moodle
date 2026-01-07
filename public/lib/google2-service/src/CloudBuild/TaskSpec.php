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

class TaskSpec extends \Google\Collection
{
  protected $collection_key = 'workspaces';
  /**
   * Description of the task.
   *
   * @var string
   */
  public $description;
  /**
   * Sidecars that run alongside the Task’s step containers that should be added
   * to this Task.
   *
   * @var string[]
   */
  public $managedSidecars;
  protected $paramsType = ParamSpec::class;
  protected $paramsDataType = 'array';
  protected $resultsType = TaskResult::class;
  protected $resultsDataType = 'array';
  protected $sidecarsType = Sidecar::class;
  protected $sidecarsDataType = 'array';
  protected $stepTemplateType = StepTemplate::class;
  protected $stepTemplateDataType = '';
  protected $stepsType = Step::class;
  protected $stepsDataType = 'array';
  protected $volumesType = VolumeSource::class;
  protected $volumesDataType = 'array';
  protected $workspacesType = WorkspaceDeclaration::class;
  protected $workspacesDataType = 'array';

  /**
   * Description of the task.
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
   * Sidecars that run alongside the Task’s step containers that should be added
   * to this Task.
   *
   * @param string[] $managedSidecars
   */
  public function setManagedSidecars($managedSidecars)
  {
    $this->managedSidecars = $managedSidecars;
  }
  /**
   * @return string[]
   */
  public function getManagedSidecars()
  {
    return $this->managedSidecars;
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
   * Values that this Task can output.
   *
   * @param TaskResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return TaskResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * Sidecars that run alongside the Task's step containers.
   *
   * @param Sidecar[] $sidecars
   */
  public function setSidecars($sidecars)
  {
    $this->sidecars = $sidecars;
  }
  /**
   * @return Sidecar[]
   */
  public function getSidecars()
  {
    return $this->sidecars;
  }
  /**
   * Optional. StepTemplate can be used as the basis for all step containers
   * within the Task, so that the steps inherit settings on the base container.
   *
   * @param StepTemplate $stepTemplate
   */
  public function setStepTemplate(StepTemplate $stepTemplate)
  {
    $this->stepTemplate = $stepTemplate;
  }
  /**
   * @return StepTemplate
   */
  public function getStepTemplate()
  {
    return $this->stepTemplate;
  }
  /**
   * Steps of the task.
   *
   * @param Step[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return Step[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
  /**
   * A collection of volumes that are available to mount into steps.
   *
   * @param VolumeSource[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return VolumeSource[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
  /**
   * The volumes that this Task requires.
   *
   * @param WorkspaceDeclaration[] $workspaces
   */
  public function setWorkspaces($workspaces)
  {
    $this->workspaces = $workspaces;
  }
  /**
   * @return WorkspaceDeclaration[]
   */
  public function getWorkspaces()
  {
    return $this->workspaces;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskSpec::class, 'Google_Service_CloudBuild_TaskSpec');
