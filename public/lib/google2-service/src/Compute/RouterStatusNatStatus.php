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

class RouterStatusNatStatus extends \Google\Collection
{
  protected $collection_key = 'userAllocatedNatIps';
  /**
   * Output only. A list of IPs auto-allocated for NAT. Example: ["1.1.1.1",
   * "129.2.16.89"]
   *
   * @var string[]
   */
  public $autoAllocatedNatIps;
  /**
   * Output only. A list of IPs auto-allocated for NAT that are in drain mode.
   * Example: ["1.1.1.1", "179.12.26.133"].
   *
   * @var string[]
   */
  public $drainAutoAllocatedNatIps;
  /**
   * Output only. A list of IPs user-allocated for NAT that are in drain mode.
   * Example: ["1.1.1.1", "179.12.26.133"].
   *
   * @var string[]
   */
  public $drainUserAllocatedNatIps;
  /**
   * Output only. The number of extra IPs to allocate. This will be greater than
   * 0 only if user-specified IPs are NOT enough to allow all configured VMs to
   * use NAT. This value is meaningful only when auto-allocation of NAT IPs is
   * *not* used.
   *
   * @var int
   */
  public $minExtraNatIpsNeeded;
  /**
   * Output only. Unique name of this NAT.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Number of VM endpoints (i.e., Nics) that can use NAT.
   *
   * @var int
   */
  public $numVmEndpointsWithNatMappings;
  protected $ruleStatusType = RouterStatusNatStatusNatRuleStatus::class;
  protected $ruleStatusDataType = 'array';
  /**
   * Output only. A list of fully qualified URLs of reserved IP address
   * resources.
   *
   * @var string[]
   */
  public $userAllocatedNatIpResources;
  /**
   * Output only. A list of IPs user-allocated for NAT. They will be raw IP
   * strings like "179.12.26.133".
   *
   * @var string[]
   */
  public $userAllocatedNatIps;

  /**
   * Output only. A list of IPs auto-allocated for NAT. Example: ["1.1.1.1",
   * "129.2.16.89"]
   *
   * @param string[] $autoAllocatedNatIps
   */
  public function setAutoAllocatedNatIps($autoAllocatedNatIps)
  {
    $this->autoAllocatedNatIps = $autoAllocatedNatIps;
  }
  /**
   * @return string[]
   */
  public function getAutoAllocatedNatIps()
  {
    return $this->autoAllocatedNatIps;
  }
  /**
   * Output only. A list of IPs auto-allocated for NAT that are in drain mode.
   * Example: ["1.1.1.1", "179.12.26.133"].
   *
   * @param string[] $drainAutoAllocatedNatIps
   */
  public function setDrainAutoAllocatedNatIps($drainAutoAllocatedNatIps)
  {
    $this->drainAutoAllocatedNatIps = $drainAutoAllocatedNatIps;
  }
  /**
   * @return string[]
   */
  public function getDrainAutoAllocatedNatIps()
  {
    return $this->drainAutoAllocatedNatIps;
  }
  /**
   * Output only. A list of IPs user-allocated for NAT that are in drain mode.
   * Example: ["1.1.1.1", "179.12.26.133"].
   *
   * @param string[] $drainUserAllocatedNatIps
   */
  public function setDrainUserAllocatedNatIps($drainUserAllocatedNatIps)
  {
    $this->drainUserAllocatedNatIps = $drainUserAllocatedNatIps;
  }
  /**
   * @return string[]
   */
  public function getDrainUserAllocatedNatIps()
  {
    return $this->drainUserAllocatedNatIps;
  }
  /**
   * Output only. The number of extra IPs to allocate. This will be greater than
   * 0 only if user-specified IPs are NOT enough to allow all configured VMs to
   * use NAT. This value is meaningful only when auto-allocation of NAT IPs is
   * *not* used.
   *
   * @param int $minExtraNatIpsNeeded
   */
  public function setMinExtraNatIpsNeeded($minExtraNatIpsNeeded)
  {
    $this->minExtraNatIpsNeeded = $minExtraNatIpsNeeded;
  }
  /**
   * @return int
   */
  public function getMinExtraNatIpsNeeded()
  {
    return $this->minExtraNatIpsNeeded;
  }
  /**
   * Output only. Unique name of this NAT.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Number of VM endpoints (i.e., Nics) that can use NAT.
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
   * Status of rules in this NAT.
   *
   * @param RouterStatusNatStatusNatRuleStatus[] $ruleStatus
   */
  public function setRuleStatus($ruleStatus)
  {
    $this->ruleStatus = $ruleStatus;
  }
  /**
   * @return RouterStatusNatStatusNatRuleStatus[]
   */
  public function getRuleStatus()
  {
    return $this->ruleStatus;
  }
  /**
   * Output only. A list of fully qualified URLs of reserved IP address
   * resources.
   *
   * @param string[] $userAllocatedNatIpResources
   */
  public function setUserAllocatedNatIpResources($userAllocatedNatIpResources)
  {
    $this->userAllocatedNatIpResources = $userAllocatedNatIpResources;
  }
  /**
   * @return string[]
   */
  public function getUserAllocatedNatIpResources()
  {
    return $this->userAllocatedNatIpResources;
  }
  /**
   * Output only. A list of IPs user-allocated for NAT. They will be raw IP
   * strings like "179.12.26.133".
   *
   * @param string[] $userAllocatedNatIps
   */
  public function setUserAllocatedNatIps($userAllocatedNatIps)
  {
    $this->userAllocatedNatIps = $userAllocatedNatIps;
  }
  /**
   * @return string[]
   */
  public function getUserAllocatedNatIps()
  {
    return $this->userAllocatedNatIps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterStatusNatStatus::class, 'Google_Service_Compute_RouterStatusNatStatus');
