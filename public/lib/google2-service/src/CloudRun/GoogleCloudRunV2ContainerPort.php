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

class GoogleCloudRunV2ContainerPort extends \Google\Model
{
  /**
   * Port number the container listens on. This must be a valid TCP port number,
   * 0 < container_port < 65536.
   *
   * @var int
   */
  public $containerPort;
  /**
   * If specified, used to specify which protocol to use. Allowed values are
   * "http1" and "h2c".
   *
   * @var string
   */
  public $name;

  /**
   * Port number the container listens on. This must be a valid TCP port number,
   * 0 < container_port < 65536.
   *
   * @param int $containerPort
   */
  public function setContainerPort($containerPort)
  {
    $this->containerPort = $containerPort;
  }
  /**
   * @return int
   */
  public function getContainerPort()
  {
    return $this->containerPort;
  }
  /**
   * If specified, used to specify which protocol to use. Allowed values are
   * "http1" and "h2c".
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
class_alias(GoogleCloudRunV2ContainerPort::class, 'Google_Service_CloudRun_GoogleCloudRunV2ContainerPort');
