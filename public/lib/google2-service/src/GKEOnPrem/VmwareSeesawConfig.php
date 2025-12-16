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

class VmwareSeesawConfig extends \Google\Collection
{
  protected $collection_key = 'vms';
  /**
   * Enable two load balancer VMs to achieve a highly-available Seesaw load
   * balancer.
   *
   * @var bool
   */
  public $enableHa;
  /**
   * Required. In general the following format should be used for the Seesaw
   * group name: seesaw-for-[cluster_name].
   *
   * @var string
   */
  public $group;
  protected $ipBlocksType = VmwareIpBlock::class;
  protected $ipBlocksDataType = 'array';
  /**
   * Required. MasterIP is the IP announced by the master of Seesaw group.
   *
   * @var string
   */
  public $masterIp;
  /**
   * Name to be used by Stackdriver.
   *
   * @var string
   */
  public $stackdriverName;
  /**
   * Names of the VMs created for this Seesaw group.
   *
   * @var string[]
   */
  public $vms;

  /**
   * Enable two load balancer VMs to achieve a highly-available Seesaw load
   * balancer.
   *
   * @param bool $enableHa
   */
  public function setEnableHa($enableHa)
  {
    $this->enableHa = $enableHa;
  }
  /**
   * @return bool
   */
  public function getEnableHa()
  {
    return $this->enableHa;
  }
  /**
   * Required. In general the following format should be used for the Seesaw
   * group name: seesaw-for-[cluster_name].
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Required. The IP Blocks to be used by the Seesaw load balancer
   *
   * @param VmwareIpBlock[] $ipBlocks
   */
  public function setIpBlocks($ipBlocks)
  {
    $this->ipBlocks = $ipBlocks;
  }
  /**
   * @return VmwareIpBlock[]
   */
  public function getIpBlocks()
  {
    return $this->ipBlocks;
  }
  /**
   * Required. MasterIP is the IP announced by the master of Seesaw group.
   *
   * @param string $masterIp
   */
  public function setMasterIp($masterIp)
  {
    $this->masterIp = $masterIp;
  }
  /**
   * @return string
   */
  public function getMasterIp()
  {
    return $this->masterIp;
  }
  /**
   * Name to be used by Stackdriver.
   *
   * @param string $stackdriverName
   */
  public function setStackdriverName($stackdriverName)
  {
    $this->stackdriverName = $stackdriverName;
  }
  /**
   * @return string
   */
  public function getStackdriverName()
  {
    return $this->stackdriverName;
  }
  /**
   * Names of the VMs created for this Seesaw group.
   *
   * @param string[] $vms
   */
  public function setVms($vms)
  {
    $this->vms = $vms;
  }
  /**
   * @return string[]
   */
  public function getVms()
  {
    return $this->vms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareSeesawConfig::class, 'Google_Service_GKEOnPrem_VmwareSeesawConfig');
