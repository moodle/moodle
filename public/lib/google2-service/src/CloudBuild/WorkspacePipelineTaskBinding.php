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

class WorkspacePipelineTaskBinding extends \Google\Model
{
  /**
   * Name of the workspace as declared by the task.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. SubPath is optionally a directory on the volume which should be
   * used for this binding (i.e. the volume will be mounted at this sub
   * directory). +optional
   *
   * @var string
   */
  public $subPath;
  /**
   * Name of the workspace declared by the pipeline.
   *
   * @var string
   */
  public $workspace;

  /**
   * Name of the workspace as declared by the task.
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
   * Optional. SubPath is optionally a directory on the volume which should be
   * used for this binding (i.e. the volume will be mounted at this sub
   * directory). +optional
   *
   * @param string $subPath
   */
  public function setSubPath($subPath)
  {
    $this->subPath = $subPath;
  }
  /**
   * @return string
   */
  public function getSubPath()
  {
    return $this->subPath;
  }
  /**
   * Name of the workspace declared by the pipeline.
   *
   * @param string $workspace
   */
  public function setWorkspace($workspace)
  {
    $this->workspace = $workspace;
  }
  /**
   * @return string
   */
  public function getWorkspace()
  {
    return $this->workspace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkspacePipelineTaskBinding::class, 'Google_Service_CloudBuild_WorkspacePipelineTaskBinding');
