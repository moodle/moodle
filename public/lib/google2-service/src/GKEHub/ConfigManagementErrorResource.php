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

namespace Google\Service\GKEHub;

class ConfigManagementErrorResource extends \Google\Model
{
  protected $resourceGvkType = ConfigManagementGroupVersionKind::class;
  protected $resourceGvkDataType = '';
  /**
   * Metadata name of the resource that is causing an error
   *
   * @var string
   */
  public $resourceName;
  /**
   * Namespace of the resource that is causing an error
   *
   * @var string
   */
  public $resourceNamespace;
  /**
   * Path in the git repo of the erroneous config
   *
   * @var string
   */
  public $sourcePath;

  /**
   * Group/version/kind of the resource that is causing an error
   *
   * @param ConfigManagementGroupVersionKind $resourceGvk
   */
  public function setResourceGvk(ConfigManagementGroupVersionKind $resourceGvk)
  {
    $this->resourceGvk = $resourceGvk;
  }
  /**
   * @return ConfigManagementGroupVersionKind
   */
  public function getResourceGvk()
  {
    return $this->resourceGvk;
  }
  /**
   * Metadata name of the resource that is causing an error
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Namespace of the resource that is causing an error
   *
   * @param string $resourceNamespace
   */
  public function setResourceNamespace($resourceNamespace)
  {
    $this->resourceNamespace = $resourceNamespace;
  }
  /**
   * @return string
   */
  public function getResourceNamespace()
  {
    return $this->resourceNamespace;
  }
  /**
   * Path in the git repo of the erroneous config
   *
   * @param string $sourcePath
   */
  public function setSourcePath($sourcePath)
  {
    $this->sourcePath = $sourcePath;
  }
  /**
   * @return string
   */
  public function getSourcePath()
  {
    return $this->sourcePath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementErrorResource::class, 'Google_Service_GKEHub_ConfigManagementErrorResource');
