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

class VmwareIpBlock extends \Google\Collection
{
  protected $collection_key = 'ips';
  /**
   * The network gateway used by the VMware user cluster.
   *
   * @var string
   */
  public $gateway;
  protected $ipsType = VmwareHostIp::class;
  protected $ipsDataType = 'array';
  /**
   * The netmask used by the VMware user cluster.
   *
   * @var string
   */
  public $netmask;

  /**
   * The network gateway used by the VMware user cluster.
   *
   * @param string $gateway
   */
  public function setGateway($gateway)
  {
    $this->gateway = $gateway;
  }
  /**
   * @return string
   */
  public function getGateway()
  {
    return $this->gateway;
  }
  /**
   * The node's network configurations used by the VMware user cluster.
   *
   * @param VmwareHostIp[] $ips
   */
  public function setIps($ips)
  {
    $this->ips = $ips;
  }
  /**
   * @return VmwareHostIp[]
   */
  public function getIps()
  {
    return $this->ips;
  }
  /**
   * The netmask used by the VMware user cluster.
   *
   * @param string $netmask
   */
  public function setNetmask($netmask)
  {
    $this->netmask = $netmask;
  }
  /**
   * @return string
   */
  public function getNetmask()
  {
    return $this->netmask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareIpBlock::class, 'Google_Service_GKEOnPrem_VmwareIpBlock');
