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

namespace Google\Service\Compute;

class PreservedStatePreservedNetworkIpIpAddress extends \Google\Model
{
  /**
   * The URL of the reservation for this IP address.
   *
   * @var string
   */
  public $address;
  /**
   * An IPv4 internal network address to assign to the instance for this network
   * interface.
   *
   * @var string
   */
  public $literal;

  /**
   * The URL of the reservation for this IP address.
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
   * An IPv4 internal network address to assign to the instance for this network
   * interface.
   *
   * @param string $literal
   */
  public function setLiteral($literal)
  {
    $this->literal = $literal;
  }
  /**
   * @return string
   */
  public function getLiteral()
  {
    return $this->literal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreservedStatePreservedNetworkIpIpAddress::class, 'Google_Service_Compute_PreservedStatePreservedNetworkIpIpAddress');
