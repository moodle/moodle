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

class VolumeSource extends \Google\Model
{
  protected $emptyDirType = EmptyDirVolumeSource::class;
  protected $emptyDirDataType = '';
  /**
   * Name of the Volume. Must be a DNS_LABEL and unique within the pod. More
   * info: https://kubernetes.io/docs/concepts/overview/working-with-
   * objects/names/#names
   *
   * @var string
   */
  public $name;

  /**
   * A temporary directory that shares a pod's lifetime.
   *
   * @param EmptyDirVolumeSource $emptyDir
   */
  public function setEmptyDir(EmptyDirVolumeSource $emptyDir)
  {
    $this->emptyDir = $emptyDir;
  }
  /**
   * @return EmptyDirVolumeSource
   */
  public function getEmptyDir()
  {
    return $this->emptyDir;
  }
  /**
   * Name of the Volume. Must be a DNS_LABEL and unique within the pod. More
   * info: https://kubernetes.io/docs/concepts/overview/working-with-
   * objects/names/#names
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeSource::class, 'Google_Service_CloudBuild_VolumeSource');
