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

class FirewallPolicyRuleMatcherLayer4Config extends \Google\Collection
{
  protected $collection_key = 'ports';
  /**
   * The IP protocol to which this rule applies. The protocol type is required
   * when creating a firewall rule. This value can either be one of the
   * following well known protocol strings (tcp,udp, icmp, esp,ah, ipip, sctp),
   * or the IP protocol number.
   *
   * @var string
   */
  public $ipProtocol;
  /**
   * An optional list of ports to which this rule applies. This field is only
   * applicable for UDP or TCP protocol. Each entry must be either an integer or
   * a range. If not specified, this rule applies to connections through any
   * port.
   *
   * Example inputs include: ["22"],["80","443"], and ["12345-12349"].
   *
   * @var string[]
   */
  public $ports;

  /**
   * The IP protocol to which this rule applies. The protocol type is required
   * when creating a firewall rule. This value can either be one of the
   * following well known protocol strings (tcp,udp, icmp, esp,ah, ipip, sctp),
   * or the IP protocol number.
   *
   * @param string $ipProtocol
   */
  public function setIpProtocol($ipProtocol)
  {
    $this->ipProtocol = $ipProtocol;
  }
  /**
   * @return string
   */
  public function getIpProtocol()
  {
    return $this->ipProtocol;
  }
  /**
   * An optional list of ports to which this rule applies. This field is only
   * applicable for UDP or TCP protocol. Each entry must be either an integer or
   * a range. If not specified, this rule applies to connections through any
   * port.
   *
   * Example inputs include: ["22"],["80","443"], and ["12345-12349"].
   *
   * @param string[] $ports
   */
  public function setPorts($ports)
  {
    $this->ports = $ports;
  }
  /**
   * @return string[]
   */
  public function getPorts()
  {
    return $this->ports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallPolicyRuleMatcherLayer4Config::class, 'Google_Service_Compute_FirewallPolicyRuleMatcherLayer4Config');
