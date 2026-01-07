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

namespace Google\Service\AndroidManagement;

class ConnectEvent extends \Google\Model
{
  /**
   * The destination IP address of the connect call.
   *
   * @var string
   */
  public $destinationIpAddress;
  /**
   * The destination port of the connect call.
   *
   * @var int
   */
  public $destinationPort;
  /**
   * The package name of the UID that performed the connect call.
   *
   * @var string
   */
  public $packageName;

  /**
   * The destination IP address of the connect call.
   *
   * @param string $destinationIpAddress
   */
  public function setDestinationIpAddress($destinationIpAddress)
  {
    $this->destinationIpAddress = $destinationIpAddress;
  }
  /**
   * @return string
   */
  public function getDestinationIpAddress()
  {
    return $this->destinationIpAddress;
  }
  /**
   * The destination port of the connect call.
   *
   * @param int $destinationPort
   */
  public function setDestinationPort($destinationPort)
  {
    $this->destinationPort = $destinationPort;
  }
  /**
   * @return int
   */
  public function getDestinationPort()
  {
    return $this->destinationPort;
  }
  /**
   * The package name of the UID that performed the connect call.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectEvent::class, 'Google_Service_AndroidManagement_ConnectEvent');
