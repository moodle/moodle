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

class VirtualMachineNetworkDetails extends \Google\Model
{
  /**
   * @var string
   */
  public $defaultGw;
  protected $networkAdaptersType = NetworkAdapterList::class;
  protected $networkAdaptersDataType = '';
  /**
   * @var string
   */
  public $primaryIpAddress;
  /**
   * @var string
   */
  public $primaryMacAddress;
  /**
   * @var string
   */
  public $publicIpAddress;

  /**
   * @param string
   */
  public function setDefaultGw($defaultGw)
  {
    $this->defaultGw = $defaultGw;
  }
  /**
   * @return string
   */
  public function getDefaultGw()
  {
    return $this->defaultGw;
  }
  /**
   * @param NetworkAdapterList
   */
  public function setNetworkAdapters(NetworkAdapterList $networkAdapters)
  {
    $this->networkAdapters = $networkAdapters;
  }
  /**
   * @return NetworkAdapterList
   */
  public function getNetworkAdapters()
  {
    return $this->networkAdapters;
  }
  /**
   * @param string
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
   * @param string
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
   * @param string
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
class_alias(VirtualMachineNetworkDetails::class, 'Google_Service_MigrationCenterAPI_VirtualMachineNetworkDetails');
