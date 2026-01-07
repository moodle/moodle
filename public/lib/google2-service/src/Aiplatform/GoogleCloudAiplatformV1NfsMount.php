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

class GoogleCloudAiplatformV1NfsMount extends \Google\Model
{
  /**
   * Required. Destination mount path. The NFS will be mounted for the user
   * under /mnt/nfs/
   *
   * @var string
   */
  public $mountPoint;
  /**
   * Required. Source path exported from NFS server. Has to start with '/', and
   * combined with the ip address, it indicates the source mount path in the
   * form of `server:path`
   *
   * @var string
   */
  public $path;
  /**
   * Required. IP address of the NFS server.
   *
   * @var string
   */
  public $server;

  /**
   * Required. Destination mount path. The NFS will be mounted for the user
   * under /mnt/nfs/
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
   * Required. Source path exported from NFS server. Has to start with '/', and
   * combined with the ip address, it indicates the source mount path in the
   * form of `server:path`
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Required. IP address of the NFS server.
   *
   * @param string $server
   */
  public function setServer($server)
  {
    $this->server = $server;
  }
  /**
   * @return string
   */
  public function getServer()
  {
    return $this->server;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NfsMount::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NfsMount');
