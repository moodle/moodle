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

class RouterStatusNatStatusNatRuleStatus extends \Google\Collection
{
  protected $collection_key = 'drainNatIps';
  /**
   * Output only. A list of active IPs for NAT. Example: ["1.1.1.1",
   * "179.12.26.133"].
   *
   * @var string[]
   */
  public $activeNatIps;
  /**
   * Output only. A list of IPs for NAT that are in drain mode. Example:
   * ["1.1.1.1", "179.12.26.133"].
   *
   * @var string[]
   */
  public $drainNatIps;
  /**
   * Output only. The number of extra IPs to allocate. This will be greater than
   * 0 only if the existing IPs in this NAT Rule are NOT enough to allow all
   * configured VMs to use NAT.
   *
   * @var int
   */
  public $minExtraIpsNeeded;
  /**
   * Output only. Number of VM endpoints (i.e., NICs) that have NAT Mappings
   * from this NAT Rule.
   *
   * @var int
   */
  public $numVmEndpointsWithNatMappings;
  /**
   * Output only. Rule number of the rule.
   *
   * @var int
   */
  public $ruleNumber;

  /**
   * Output only. A list of active IPs for NAT. Example: ["1.1.1.1",
   * "179.12.26.133"].
   *
   * @param string[] $activeNatIps
   */
  public function setActiveNatIps($activeNatIps)
  {
    $this->activeNatIps = $activeNatIps;
  }
  /**
   * @return string[]
   */
  public function getActiveNatIps()
  {
    return $this->activeNatIps;
  }
  /**
   * Output only. A list of IPs for NAT that are in drain mode. Example:
   * ["1.1.1.1", "179.12.26.133"].
   *
   * @param string[] $drainNatIps
   */
  public function setDrainNatIps($drainNatIps)
  {
    $this->drainNatIps = $drainNatIps;
  }
  /**
   * @return string[]
   */
  public function getDrainNatIps()
  {
    return $this->drainNatIps;
  }
  /**
   * Output only. The number of extra IPs to allocate. This will be greater than
   * 0 only if the existing IPs in this NAT Rule are NOT enough to allow all
   * configured VMs to use NAT.
   *
   * @param int $minExtraIpsNeeded
   */
  public function setMinExtraIpsNeeded($minExtraIpsNeeded)
  {
    $this->minExtraIpsNeeded = $minExtraIpsNeeded;
  }
  /**
   * @return int
   */
  public function getMinExtraIpsNeeded()
  {
    return $this->minExtraIpsNeeded;
  }
  /**
   * Output only. Number of VM endpoints (i.e., NICs) that have NAT Mappings
   * from this NAT Rule.
   *
   * @param int $numVmEndpointsWithNatMappings
   */
  public function setNumVmEndpointsWithNatMappings($numVmEndpointsWithNatMappings)
  {
    $this->numVmEndpointsWithNatMappings = $numVmEndpointsWithNatMappings;
  }
  /**
   * @return int
   */
  public function getNumVmEndpointsWithNatMappings()
  {
    return $this->numVmEndpointsWithNatMappings;
  }
  /**
   * Output only. Rule number of the rule.
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
class_alias(RouterStatusNatStatusNatRuleStatus::class, 'Google_Service_Compute_RouterStatusNatStatusNatRuleStatus');
