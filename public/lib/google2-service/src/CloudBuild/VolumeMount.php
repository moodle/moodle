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

class VolumeMount extends \Google\Model
{
  /**
   * Path within the container at which the volume should be mounted. Must not
   * contain ':'.
   *
   * @var string
   */
  public $mountPath;
  /**
   * Name of the volume.
   *
   * @var string
   */
  public $name;
  /**
   * Mounted read-only if true, read-write otherwise (false or unspecified).
   *
   * @var bool
   */
  public $readOnly;
  /**
   * Path within the volume from which the container's volume should be mounted.
   * Defaults to "" (volume's root).
   *
   * @var string
   */
  public $subPath;
  /**
   * Expanded path within the volume from which the container's volume should be
   * mounted. Behaves similarly to SubPath but environment variable references
   * $(VAR_NAME) are expanded using the container's environment. Defaults to ""
   * (volume's root).
   *
   * @var string
   */
  public $subPathExpr;

  /**
   * Path within the container at which the volume should be mounted. Must not
   * contain ':'.
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
   * Name of the volume.
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
   * Mounted read-only if true, read-write otherwise (false or unspecified).
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
  /**
   * Path within the volume from which the container's volume should be mounted.
   * Defaults to "" (volume's root).
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
   * Expanded path within the volume from which the container's volume should be
   * mounted. Behaves similarly to SubPath but environment variable references
   * $(VAR_NAME) are expanded using the container's environment. Defaults to ""
   * (volume's root).
   *
   * @param string $subPathExpr
   */
  public function setSubPathExpr($subPathExpr)
  {
    $this->subPathExpr = $subPathExpr;
  }
  /**
   * @return string
   */
  public function getSubPathExpr()
  {
    return $this->subPathExpr;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeMount::class, 'Google_Service_CloudBuild_VolumeMount');
