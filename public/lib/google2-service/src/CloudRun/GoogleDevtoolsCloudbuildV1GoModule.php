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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1GoModule extends \Google\Model
{
  /**
   * Optional. The Go module's "module path". e.g. example.com/foo/v2
   *
   * @var string
   */
  public $modulePath;
  /**
   * Optional. The Go module's semantic version in the form vX.Y.Z. e.g. v0.1.1
   * Pre-release identifiers can also be added by appending a dash and dot
   * separated ASCII alphanumeric characters and hyphens. e.g.
   * v0.2.3-alpha.x.12m.5
   *
   * @var string
   */
  public $moduleVersion;
  /**
   * Optional. Location of the Artifact Registry repository. i.e. us-east1
   * Defaults to the build’s location.
   *
   * @var string
   */
  public $repositoryLocation;
  /**
   * Optional. Artifact Registry repository name. Specified Go modules will be
   * zipped and uploaded to Artifact Registry with this location as a prefix.
   * e.g. my-go-repo
   *
   * @var string
   */
  public $repositoryName;
  /**
   * Optional. Project ID of the Artifact Registry repository. Defaults to the
   * build project.
   *
   * @var string
   */
  public $repositoryProjectId;
  /**
   * Optional. Source path of the go.mod file in the build's workspace. If not
   * specified, this will default to the current directory. e.g.
   * ~/code/go/mypackage
   *
   * @var string
   */
  public $sourcePath;

  /**
   * Optional. The Go module's "module path". e.g. example.com/foo/v2
   *
   * @param string $modulePath
   */
  public function setModulePath($modulePath)
  {
    $this->modulePath = $modulePath;
  }
  /**
   * @return string
   */
  public function getModulePath()
  {
    return $this->modulePath;
  }
  /**
   * Optional. The Go module's semantic version in the form vX.Y.Z. e.g. v0.1.1
   * Pre-release identifiers can also be added by appending a dash and dot
   * separated ASCII alphanumeric characters and hyphens. e.g.
   * v0.2.3-alpha.x.12m.5
   *
   * @param string $moduleVersion
   */
  public function setModuleVersion($moduleVersion)
  {
    $this->moduleVersion = $moduleVersion;
  }
  /**
   * @return string
   */
  public function getModuleVersion()
  {
    return $this->moduleVersion;
  }
  /**
   * Optional. Location of the Artifact Registry repository. i.e. us-east1
   * Defaults to the build’s location.
   *
   * @param string $repositoryLocation
   */
  public function setRepositoryLocation($repositoryLocation)
  {
    $this->repositoryLocation = $repositoryLocation;
  }
  /**
   * @return string
   */
  public function getRepositoryLocation()
  {
    return $this->repositoryLocation;
  }
  /**
   * Optional. Artifact Registry repository name. Specified Go modules will be
   * zipped and uploaded to Artifact Registry with this location as a prefix.
   * e.g. my-go-repo
   *
   * @param string $repositoryName
   */
  public function setRepositoryName($repositoryName)
  {
    $this->repositoryName = $repositoryName;
  }
  /**
   * @return string
   */
  public function getRepositoryName()
  {
    return $this->repositoryName;
  }
  /**
   * Optional. Project ID of the Artifact Registry repository. Defaults to the
   * build project.
   *
   * @param string $repositoryProjectId
   */
  public function setRepositoryProjectId($repositoryProjectId)
  {
    $this->repositoryProjectId = $repositoryProjectId;
  }
  /**
   * @return string
   */
  public function getRepositoryProjectId()
  {
    return $this->repositoryProjectId;
  }
  /**
   * Optional. Source path of the go.mod file in the build's workspace. If not
   * specified, this will default to the current directory. e.g.
   * ~/code/go/mypackage
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
class_alias(GoogleDevtoolsCloudbuildV1GoModule::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1GoModule');
