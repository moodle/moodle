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

class VmEndpointNatMappingsInterfaceNatMappingsNatRuleMappings extends \Google\Collection
{
  protected $collection_key = 'natIpPortRanges';
  /**
   * Output only. List of all drain IP:port-range mappings assigned to this
   * interface by this rule. These ranges are inclusive, that is, both the first
   * and the last ports can be used for NAT. Example: ["2.2.2.2:12345-12355",
   * "1.1.1.1:2234-2234"].
   *
   * @var string[]
   */
  public $drainNatIpPortRanges;
  /**
   * Output only. A list of all IP:port-range mappings assigned to this
   * interface by this rule. These ranges are inclusive, that is, both the first
   * and the last ports can be used for NAT. Example: ["2.2.2.2:12345-12355",
   * "1.1.1.1:2234-2234"].
   *
   * @var string[]
   */
  public $natIpPortRanges;
  /**
   * Output only. Total number of drain ports across all NAT IPs allocated to
   * this interface by this rule. It equals the aggregated port number in the
   * field drain_nat_ip_port_ranges.
   *
   * @var int
   */
  public $numTotalDrainNatPorts;
  /**
   * Output only. Total number of ports across all NAT IPs allocated to this
   * interface by this rule. It equals the aggregated port number in the field
   * nat_ip_port_ranges.
   *
   * @var int
   */
  public $numTotalNatPorts;
  /**
   * Output only. Rule number of the NAT Rule.
   *
   * @var int
   */
  public $ruleNumber;

  /**
   * Output only. List of all drain IP:port-range mappings assigned to this
   * interface by this rule. These ranges are inclusive, that is, both the first
   * and the last ports can be used for NAT. Example: ["2.2.2.2:12345-12355",
   * "1.1.1.1:2234-2234"].
   *
   * @param string[] $drainNatIpPortRanges
   */
  public function setDrainNatIpPortRanges($drainNatIpPortRanges)
  {
    $this->drainNatIpPortRanges = $drainNatIpPortRanges;
  }
  /**
   * @return string[]
   */
  public function getDrainNatIpPortRanges()
  {
    return $this->drainNatIpPortRanges;
  }
  /**
   * Output only. A list of all IP:port-range mappings assigned to this
   * interface by this rule. These ranges are inclusive, that is, both the first
   * and the last ports can be used for NAT. Example: ["2.2.2.2:12345-12355",
   * "1.1.1.1:2234-2234"].
   *
   * @param string[] $natIpPortRanges
   */
  public function setNatIpPortRanges($natIpPortRanges)
  {
    $this->natIpPortRanges = $natIpPortRanges;
  }
  /**
   * @return string[]
   */
  public function getNatIpPortRanges()
  {
    return $this->natIpPortRanges;
  }
  /**
   * Output only. Total number of drain ports across all NAT IPs allocated to
   * this interface by this rule. It equals the aggregated port number in the
   * field drain_nat_ip_port_ranges.
   *
   * @param int $numTotalDrainNatPorts
   */
  public function setNumTotalDrainNatPorts($numTotalDrainNatPorts)
  {
    $this->numTotalDrainNatPorts = $numTotalDrainNatPorts;
  }
  /**
   * @return int
   */
  public function getNumTotalDrainNatPorts()
  {
    return $this->numTotalDrainNatPorts;
  }
  /**
   * Output only. Total number of ports across all NAT IPs allocated to this
   * interface by this rule. It equals the aggregated port number in the field
   * nat_ip_port_ranges.
   *
   * @param int $numTotalNatPorts
   */
  public function setNumTotalNatPorts($numTotalNatPorts)
  {
    $this->numTotalNatPorts = $numTotalNatPorts;
  }
  /**
   * @return int
   */
  public function getNumTotalNatPorts()
  {
    return $this->numTotalNatPorts;
  }
  /**
   * Output only. Rule number of the NAT Rule.
   *
   * @param int $ruleNumber
   */
  public function setRuleNumber($ruleNumber)
  {
    $this->ruleNumber = $ruleNumber;
  }
  /**
   * @return int
   */
  public function getRuleNumber()
  {
    return $this->ruleNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmEndpointNatMappingsInterfaceNatMappingsNatRuleMappings::class, 'Google_Service_Compute_VmEndpointNatMappingsInterfaceNatMappingsNatRuleMappings');
