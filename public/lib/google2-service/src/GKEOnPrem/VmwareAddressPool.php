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

namespace Google\Service\GKEOnPrem;

class VmwareAddressPool extends \Google\Collection
{
  protected $collection_key = 'addresses';
  /**
   * Required. The addresses that are part of this pool. Each address must be
   * either in the CIDR form (1.2.3.0/24) or range form (1.2.3.1-1.2.3.5).
   *
   * @var string[]
   */
  public $addresses;
  /**
   * If true, avoid using IPs ending in .0 or .255. This avoids buggy consumer
   * devices mistakenly dropping IPv4 traffic for those special IP addresses.
   *
   * @var bool
   */
  public $avoidBuggyIps;
  /**
   * If true, prevent IP addresses from being automatically assigned.
   *
   * @var bool
   */
  public $manualAssign;
  /**
   * Required. The name of the address pool.
   *
   * @var string
   */
  public $pool;

  /**
   * Required. The addresses that are part of this pool. Each address must be
   * either in the CIDR form (1.2.3.0/24) or range form (1.2.3.1-1.2.3.5).
   *
   * @param string[] $addresses
   */
  public function setAddresses($addresses)
  {
    $this->addresses = $addresses;
  }
  /**
   * @return string[]
   */
  public function getAddresses()
  {
    return $this->addresses;
  }
  /**
   * If true, avoid using IPs ending in .0 or .255. This avoids buggy consumer
   * devices mistakenly dropping IPv4 traffic for those special IP addresses.
   *
   * @param bool $avoidBuggyIps
   */
  public function setAvoidBuggyIps($avoidBuggyIps)
  {
    $this->avoidBuggyIps = $avoidBuggyIps;
  }
  /**
   * @return bool
   */
  public function getAvoidBuggyIps()
  {
    return $this->avoidBuggyIps;
  }
  /**
   * If true, prevent IP addresses from being automatically assigned.
   *
   * @param bool $manualAssign
   */
  public function setManualAssign($manualAssign)
  {
    $this->manualAssign = $manualAssign;
  }
  /**
   * @return bool
   */
  public function getManualAssign()
  {
    return $this->manualAssign;
  }
  /**
   * Required. The name of the address pool.
   *
   * @param string $pool
   */
  public function setPool($pool)
  {
    $this->pool = $pool;
  }
  /**
   * @return string
   */
  public function getPool()
  {
    return $this->pool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAddressPool::class, 'Google_Service_GKEOnPrem_VmwareAddressPool');
