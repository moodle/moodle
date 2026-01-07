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

namespace Google\Service\Baremetalsolution;

class NetworkMountPoint extends \Google\Model
{
  /**
   * Network should be a default gateway.
   *
   * @var bool
   */
  public $defaultGateway;
  /**
   * Instance to attach network to.
   *
   * @var string
   */
  public $instance;
  /**
   * Ip address of the server.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Logical interface to detach from.
   *
   * @var string
   */
  public $logicalInterface;

  /**
   * Network should be a default gateway.
   *
   * @param bool $defaultGateway
   */
  public function setDefaultGateway($defaultGateway)
  {
    $this->defaultGateway = $defaultGateway;
  }
  /**
   * @return bool
   */
  public function getDefaultGateway()
  {
    return $this->defaultGateway;
  }
  /**
   * Instance to attach network to.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Ip address of the server.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Logical interface to detach from.
   *
   * @param string $logicalInterface
   */
  public function setLogicalInterface($logicalInterface)
  {
    $this->logicalInterface = $logicalInterface;
  }
  /**
   * @return string
   */
  public function getLogicalInterface()
  {
    return $this->logicalInterface;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkMountPoint::class, 'Google_Service_Baremetalsolution_NetworkMountPoint');
