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

namespace Google\Service\CloudObservability;

class TraceScope extends \Google\Collection
{
  protected $collection_key = 'resourceNames';
  /**
   * Output only. The creation timestamp of the trace scope.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Describes this trace scope. The maximum length of the description
   * is 8000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Identifier. The resource name of the trace scope. For example: projects/my-
   * project/locations/global/traceScopes/my-trace-scope
   *
   * @var string
   */
  public $name;
  /**
   * Required. Names of the projects that are included in this trace scope. *
   * `projects/[PROJECT_ID]` A trace scope can include a maximum of 20 projects.
   *
   * @var string[]
   */
  public $resourceNames;
  /**
   * Output only. The last update timestamp of the trace scope.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The creation timestamp of the trace scope.
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
   * Optional. Describes this trace scope. The maximum length of the description
   * is 8000 characters.
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
   * Identifier. The resource name of the trace scope. For example: projects/my-
   * project/locations/global/traceScopes/my-trace-scope
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
   * Required. Names of the projects that are included in this trace scope. *
   * `projects/[PROJECT_ID]` A trace scope can include a maximum of 20 projects.
   *
   * @param string[] $resourceNames
   */
  public function setResourceNames($resourceNames)
  {
    $this->resourceNames = $resourceNames;
  }
  /**
   * @return string[]
   */
  public function getResourceNames()
  {
    return $this->resourceNames;
  }
  /**
   * Output only. The last update timestamp of the trace scope.
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
class_alias(TraceScope::class, 'Google_Service_CloudObservability_TraceScope');
