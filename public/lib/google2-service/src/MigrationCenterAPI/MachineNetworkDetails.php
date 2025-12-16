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

namespace Google\Service\MigrationCenterAPI;

class MachineNetworkDetails extends \Google\Model
{
  protected $adaptersType = NetworkAdapterList::class;
  protected $adaptersDataType = '';
  /**
   * Optional. Default gateway address.
   *
   * @var string
   */
  public $defaultGateway;
  /**
   * The primary IP address of the machine.
   *
   * @var string
   */
  public $primaryIpAddress;
  /**
   * MAC address of the machine. This property is used to uniqly identify the
   * machine.
   *
   * @var string
   */
  public $primaryMacAddress;
  /**
   * The public IP address of the machine.
   *
   * @var string
   */
  public $publicIpAddress;

  /**
   * List of network adapters.
   *
   * @param NetworkAdapterList $adapters
   */
  public function setAdapters(NetworkAdapterList $adapters)
  {
    $this->adapters = $adapters;
  }
  /**
   * @return NetworkAdapterList
   */
  public function getAdapters()
  {
    return $this->adapters;
  }
  /**
   * Optional. Default gateway address.
   *
   * @param string $defaultGateway
   */
  public function setDefaultGateway($defaultGateway)
  {
    $this->defaultGateway = $defaultGateway;
  }
  /**
   * @return string
   */
  public function getDefaultGateway()
  {
    return $this->defaultGateway;
  }
  /**
   * The primary IP address of the machine.
   *
   * @param string $primaryIpAddress
   */
  public function setPrimaryIpAddress($primaryIpAddress)
  {
    $this->primaryIpAddress = $primaryIpAddress;
  }
  /**
   * @return string
   */
  public function getPrimaryIpAddress()
  {
    return $this->primaryIpAddress;
  }
  /**
   * MAC address of the machine. This property is used to uniqly identify the
   * machine.
   *
   * @param string $primaryMacAddress
   */
  public function setPrimaryMacAddress($primaryMacAddress)
  {
    $this->primaryMacAddress = $primaryMacAddress;
  }
  /**
   * @return string
   */
  public function getPrimaryMacAddress()
  {
    return $this->primaryMacAddress;
  }
  /**
   * The public IP address of the machine.
   *
   * @param string $publicIpAddress
   */
  public function setPublicIpAddress($publicIpAddress)
  {
    $this->publicIpAddress = $publicIpAddress;
  }
  /**
   * @return string
   */
  public function getPublicIpAddress()
  {
    return $this->publicIpAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachineNetworkDetails::class, 'Google_Service_MigrationCenterAPI_MachineNetworkDetails');
