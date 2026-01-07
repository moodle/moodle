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

namespace Google\Service\TPU;

class NetworkEndpoint extends \Google\Model
{
  protected $accessConfigType = AccessConfig::class;
  protected $accessConfigDataType = '';
  /**
   * The internal IP address of this network endpoint.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * The port of this network endpoint.
   *
   * @var int
   */
  public $port;

  /**
   * The access config for the TPU worker.
   *
   * @param AccessConfig $accessConfig
   */
  public function setAccessConfig(AccessConfig $accessConfig)
  {
    $this->accessConfig = $accessConfig;
  }
  /**
   * @return AccessConfig
   */
  public function getAccessConfig()
  {
    return $this->accessConfig;
  }
  /**
   * The internal IP address of this network endpoint.
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
   * The port of this network endpoint.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpoint::class, 'Google_Service_TPU_NetworkEndpoint');
