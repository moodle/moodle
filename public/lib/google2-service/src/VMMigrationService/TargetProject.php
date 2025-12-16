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

namespace Google\Service\VMMigrationService;

class TargetProject extends \Google\Model
{
  /**
   * Output only. The time this target project resource was created (not related
   * to when the Compute Engine project it points to was created).
   *
   * @var string
   */
  public $createTime;
  /**
   * The target project's description.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The name of the target project.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The target project ID (number) or project name.
   *
   * @var string
   */
  public $project;
  /**
   * Output only. The last time the target project resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time this target project resource was created (not related
   * to when the Compute Engine project it points to was created).
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The target project's description.
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
   * Output only. The name of the target project.
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
   * Required. The target project ID (number) or project name.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * Output only. The last time the target project resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetProject::class, 'Google_Service_VMMigrationService_TargetProject');
