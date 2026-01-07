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

namespace Google\Service\Directory;

class BluetoothAdapterInfo extends \Google\Model
{
  /**
   * Output only. The MAC address of the adapter.
   *
   * @var string
   */
  public $address;
  /**
   * Output only. The number of devices connected to this adapter.
   *
   * @var int
   */
  public $numConnectedDevices;

  /**
   * Output only. The MAC address of the adapter.
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
   * Output only. The number of devices connected to this adapter.
   *
   * @param int $numConnectedDevices
   */
  public function setNumConnectedDevices($numConnectedDevices)
  {
    $this->numConnectedDevices = $numConnectedDevices;
  }
  /**
   * @return int
   */
  public function getNumConnectedDevices()
  {
    return $this->numConnectedDevices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BluetoothAdapterInfo::class, 'Google_Service_Directory_BluetoothAdapterInfo');
