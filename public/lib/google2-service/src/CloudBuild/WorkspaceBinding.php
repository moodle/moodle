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

class WorkspaceBinding extends \Google\Model
{
  /**
   * Name of the workspace.
   *
   * @var string
   */
  public $name;
  protected $secretType = SecretVolumeSource::class;
  protected $secretDataType = '';
  /**
   * Optional. SubPath is optionally a directory on the volume which should be
   * used for this binding (i.e. the volume will be mounted at this sub
   * directory). +optional
   *
   * @var string
   */
  public $subPath;

  /**
   * Name of the workspace.
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
   * Secret Volume Source.
   *
   * @param SecretVolumeSource $secret
   */
  public function setSecret(SecretVolumeSource $secret)
  {
    $this->secret = $secret;
  }
  /**
   * @return SecretVolumeSource
   */
  public function getSecret()
  {
    return $this->secret;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkspaceBinding::class, 'Google_Service_CloudBuild_WorkspaceBinding');
