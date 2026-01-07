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

class GoogleCloudRunV2NFSVolumeSource extends \Google\Model
{
  /**
   * Path that is exported by the NFS server.
   *
   * @var string
   */
  public $path;
  /**
   * If true, the volume will be mounted as read only for all mounts.
   *
   * @var bool
   */
  public $readOnly;
  /**
   * Hostname or IP address of the NFS server
   *
   * @var string
   */
  public $server;

  /**
   * Path that is exported by the NFS server.
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
   * If true, the volume will be mounted as read only for all mounts.
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
   * Hostname or IP address of the NFS server
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
class_alias(GoogleCloudRunV2NFSVolumeSource::class, 'Google_Service_CloudRun_GoogleCloudRunV2NFSVolumeSource');
