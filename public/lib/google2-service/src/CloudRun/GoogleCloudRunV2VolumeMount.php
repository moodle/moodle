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

class GoogleCloudRunV2VolumeMount extends \Google\Model
{
  /**
   * Required. Path within the container at which the volume should be mounted.
   * Must not contain ':'. For Cloud SQL volumes, it can be left empty, or must
   * otherwise be `/cloudsql`. All instances defined in the Volume will be
   * available as `/cloudsql/[instance]`. For more information on Cloud SQL
   * volumes, visit https://cloud.google.com/sql/docs/mysql/connect-run
   *
   * @var string
   */
  public $mountPath;
  /**
   * Required. This must match the Name of a Volume.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Path within the volume from which the container's volume should
   * be mounted. Defaults to "" (volume's root).
   *
   * @var string
   */
  public $subPath;

  /**
   * Required. Path within the container at which the volume should be mounted.
   * Must not contain ':'. For Cloud SQL volumes, it can be left empty, or must
   * otherwise be `/cloudsql`. All instances defined in the Volume will be
   * available as `/cloudsql/[instance]`. For more information on Cloud SQL
   * volumes, visit https://cloud.google.com/sql/docs/mysql/connect-run
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
   * Required. This must match the Name of a Volume.
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
   * Optional. Path within the volume from which the container's volume should
   * be mounted. Defaults to "" (volume's root).
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
class_alias(GoogleCloudRunV2VolumeMount::class, 'Google_Service_CloudRun_GoogleCloudRunV2VolumeMount');
