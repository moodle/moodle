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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1LustreMount extends \Google\Model
{
  /**
   * Required. The name of the Lustre filesystem.
   *
   * @var string
   */
  public $filesystem;
  /**
   * Required. IP address of the Lustre instance.
   *
   * @var string
   */
  public $instanceIp;
  /**
   * Required. Destination mount path. The Lustre file system will be mounted
   * for the user under /mnt/lustre/
   *
   * @var string
   */
  public $mountPoint;
  /**
   * Required. The unique identifier of the Lustre volume.
   *
   * @var string
   */
  public $volumeHandle;

  /**
   * Required. The name of the Lustre filesystem.
   *
   * @param string $filesystem
   */
  public function setFilesystem($filesystem)
  {
    $this->filesystem = $filesystem;
  }
  /**
   * @return string
   */
  public function getFilesystem()
  {
    return $this->filesystem;
  }
  /**
   * Required. IP address of the Lustre instance.
   *
   * @param string $instanceIp
   */
  public function setInstanceIp($instanceIp)
  {
    $this->instanceIp = $instanceIp;
  }
  /**
   * @return string
   */
  public function getInstanceIp()
  {
    return $this->instanceIp;
  }
  /**
   * Required. Destination mount path. The Lustre file system will be mounted
   * for the user under /mnt/lustre/
   *
   * @param string $mountPoint
   */
  public function setMountPoint($mountPoint)
  {
    $this->mountPoint = $mountPoint;
  }
  /**
   * @return string
   */
  public function getMountPoint()
  {
    return $this->mountPoint;
  }
  /**
   * Required. The unique identifier of the Lustre volume.
   *
   * @param string $volumeHandle
   */
  public function setVolumeHandle($volumeHandle)
  {
    $this->volumeHandle = $volumeHandle;
  }
  /**
   * @return string
   */
  public function getVolumeHandle()
  {
    return $this->volumeHandle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1LustreMount::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1LustreMount');
