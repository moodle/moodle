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

class NetworkAdapterDetails extends \Google\Model
{
  /**
   * Network adapter type (e.g. VMXNET3).
   *
   * @var string
   */
  public $adapterType;
  protected $addressesType = NetworkAddressList::class;
  protected $addressesDataType = '';
  /**
   * MAC address.
   *
   * @var string
   */
  public $macAddress;

  /**
   * Network adapter type (e.g. VMXNET3).
   *
   * @param string $adapterType
   */
  public function setAdapterType($adapterType)
  {
    $this->adapterType = $adapterType;
  }
  /**
   * @return string
   */
  public function getAdapterType()
  {
    return $this->adapterType;
  }
  /**
   * NetworkAddressList
   *
   * @param NetworkAddressList $addresses
   */
  public function setAddresses(NetworkAddressList $addresses)
  {
    $this->addresses = $addresses;
  }
  /**
   * @return NetworkAddressList
   */
  public function getAddresses()
  {
    return $this->addresses;
  }
  /**
   * MAC address.
   *
   * @param string $macAddress
   */
  public function setMacAddress($macAddress)
  {
    $this->macAddress = $macAddress;
  }
  /**
   * @return string
   */
  public function getMacAddress()
  {
    return $this->macAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkAdapterDetails::class, 'Google_Service_MigrationCenterAPI_NetworkAdapterDetails');
