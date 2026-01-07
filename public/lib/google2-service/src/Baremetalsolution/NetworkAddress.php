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

class NetworkAddress extends \Google\Model
{
  /**
   * IPv4 address to be assigned to the server.
   *
   * @var string
   */
  public $address;
  /**
   * Name of the existing network to use.
   *
   * @var string
   */
  public $existingNetworkId;
  /**
   * Id of the network to use, within the same ProvisioningConfig request.
   *
   * @var string
   */
  public $networkId;

  /**
   * IPv4 address to be assigned to the server.
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
   * Name of the existing network to use.
   *
   * @param string $existingNetworkId
   */
  public function setExistingNetworkId($existingNetworkId)
  {
    $this->existingNetworkId = $existingNetworkId;
  }
  /**
   * @return string
   */
  public function getExistingNetworkId()
  {
    return $this->existingNetworkId;
  }
  /**
   * Id of the network to use, within the same ProvisioningConfig request.
   *
   * @param string $networkId
   */
  public function setNetworkId($networkId)
  {
    $this->networkId = $networkId;
  }
  /**
   * @return string
   */
  public function getNetworkId()
  {
    return $this->networkId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkAddress::class, 'Google_Service_Baremetalsolution_NetworkAddress');
