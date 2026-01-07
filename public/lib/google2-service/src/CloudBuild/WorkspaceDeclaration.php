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

class WorkspaceDeclaration extends \Google\Model
{
  /**
   * Description is a human readable description of this volume.
   *
   * @var string
   */
  public $description;
  /**
   * MountPath overrides the directory that the volume will be made available
   * at.
   *
   * @var string
   */
  public $mountPath;
  /**
   * Name is the name by which you can bind the volume at runtime.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Optional marks a Workspace as not being required in TaskRuns. By
   * default this field is false and so declared workspaces are required.
   *
   * @var bool
   */
  public $optional;
  /**
   * ReadOnly dictates whether a mounted volume is writable.
   *
   * @var bool
   */
  public $readOnly;

  /**
   * Description is a human readable description of this volume.
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
   * MountPath overrides the directory that the volume will be made available
   * at.
   *
   * @param string $mountPath
   */
  public function setMountPath($mountPath)
  {
    $this->mountPath = $mountPath;
  }
  /**
   * @return string
   */
  public function getMountPath()
  {
    return $this->mountPath;
  }
  /**
   * Name is the name by which you can bind the volume at runtime.
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
   * Optional. Optional marks a Workspace as not being required in TaskRuns. By
   * default this field is false and so declared workspaces are required.
   *
   * @param bool $optional
   */
  public function setOptional($optional)
  {
    $this->optional = $optional;
  }
  /**
   * @return bool
   */
  public function getOptional()
  {
    return $this->optional;
  }
  /**
   * ReadOnly dictates whether a mounted volume is writable.
   *
   * @param bool $readOnly
   */
  public function setReadOnly($readOnly)
  {
    $this->readOnly = $readOnly;
  }
  /**
   * @return bool
   */
  public function getReadOnly()
  {
    return $this->readOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkspaceDeclaration::class, 'Google_Service_CloudBuild_WorkspaceDeclaration');
