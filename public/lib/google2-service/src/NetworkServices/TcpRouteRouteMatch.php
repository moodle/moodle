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

namespace Google\Service\NetworkServices;

class TcpRouteRouteMatch extends \Google\Model
{
  /**
   * Required. Must be specified in the CIDR range format. A CIDR range consists
   * of an IP Address and a prefix length to construct the subnet mask. By
   * default, the prefix length is 32 (i.e. matches a single IP address). Only
   * IPV4 addresses are supported. Examples: "10.0.0.1" - matches against this
   * exact IP address. "10.0.0.0/8" - matches against any IP address within the
   * 10.0.0.0 subnet and 255.255.255.0 mask. "0.0.0.0/0" - matches against any
   * IP address'.
   *
   * @var string
   */
  public $address;
  /**
   * Required. Specifies the destination port to match against.
   *
   * @var string
   */
  public $port;

  /**
   * Required. Must be specified in the CIDR range format. A CIDR range consists
   * of an IP Address and a prefix length to construct the subnet mask. By
   * default, the prefix length is 32 (i.e. matches a single IP address). Only
   * IPV4 addresses are supported. Examples: "10.0.0.1" - matches against this
   * exact IP address. "10.0.0.0/8" - matches against any IP address within the
   * 10.0.0.0 subnet and 255.255.255.0 mask. "0.0.0.0/0" - matches against any
   * IP address'.
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
   * Required. Specifies the destination port to match against.
   *
   * @param string $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return string
   */
  public function getPort()
  {
    return $this->port;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TcpRouteRouteMatch::class, 'Google_Service_NetworkServices_TcpRouteRouteMatch');
