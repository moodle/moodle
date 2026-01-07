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

namespace Google\Service\CloudRedis;

class DiscoveryEndpoint extends \Google\Model
{
  /**
   * Output only. Address of the exposed Redis endpoint used by clients to
   * connect to the service. The address could be either IP or hostname.
   *
   * @var string
   */
  public $address;
  /**
   * Output only. The port number of the exposed Redis endpoint.
   *
   * @var int
   */
  public $port;
  protected $pscConfigType = PscConfig::class;
  protected $pscConfigDataType = '';

  /**
   * Output only. Address of the exposed Redis endpoint used by clients to
   * connect to the service. The address could be either IP or hostname.
   *
   * @param string $address
   */
  public function setAddress($address)
  {
    $this->address = $address;
  }
  /**
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Output only. The port number of the exposed Redis endpoint.
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
  /**
   * Output only. Customer configuration for where the endpoint is created and
   * accessed from.
   *
   * @param PscConfig $pscConfig
   */
  public function setPscConfig(PscConfig $pscConfig)
  {
    $this->pscConfig = $pscConfig;
  }
  /**
   * @return PscConfig
   */
  public function getPscConfig()
  {
    return $this->pscConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiscoveryEndpoint::class, 'Google_Service_CloudRedis_DiscoveryEndpoint');
