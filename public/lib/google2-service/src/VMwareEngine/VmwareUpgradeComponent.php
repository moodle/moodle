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

namespace Google\Service\VMwareEngine;

class VmwareUpgradeComponent extends \Google\Model
{
  /**
   * The default value. This value should never be used.
   */
  public const COMPONENT_TYPE_VMWARE_COMPONENT_TYPE_UNSPECIFIED = 'VMWARE_COMPONENT_TYPE_UNSPECIFIED';
  /**
   * vcenter
   */
  public const COMPONENT_TYPE_VCENTER = 'VCENTER';
  /**
   * esxi nodes + transport nodes
   */
  public const COMPONENT_TYPE_ESXI = 'ESXI';
  /**
   * nsxt upgrade coordinator
   */
  public const COMPONENT_TYPE_NSXT_UC = 'NSXT_UC';
  /**
   * nsxt edges cluster
   */
  public const COMPONENT_TYPE_NSXT_EDGE = 'NSXT_EDGE';
  /**
   * nsxt managers/management plane
   */
  public const COMPONENT_TYPE_NSXT_MGR = 'NSXT_MGR';
  /**
   * hcx
   */
  public const COMPONENT_TYPE_HCX = 'HCX';
  /**
   * VSAN cluster
   */
  public const COMPONENT_TYPE_VSAN = 'VSAN';
  /**
   * DVS switch
   */
  public const COMPONENT_TYPE_DVS = 'DVS';
  /**
   * Nameserver VMs
   */
  public const COMPONENT_TYPE_NAMESERVER_VM = 'NAMESERVER_VM';
  /**
   * KMS VM used for vsan encryption
   */
  public const COMPONENT_TYPE_KMS_VM = 'KMS_VM';
  /**
   * witness VM in case of stretch PC
   */
  public const COMPONENT_TYPE_WITNESS_VM = 'WITNESS_VM';
  /**
   * nsxt
   */
  public const COMPONENT_TYPE_NSXT = 'NSXT';
  /**
   * Cluster is used in case of BM
   */
  public const COMPONENT_TYPE_CLUSTER = 'CLUSTER';
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Component's upgrade is in progress
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The component's upgrade is paused. Will be resumed when upgrade job is
   * resumed
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The component's upgrade is successfully completed
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The component's upgrade has failed. This will move to resume if upgrade is
   * resumed or stay as is
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Component's upgrade has not started yet
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * Component's upgrade is not applicable in this upgrade. It will be skipped.
   */
  public const STATE_NOT_APPLICABLE = 'NOT_APPLICABLE';
  /**
   * Output only. Type of component
   *
   * @var string
   */
  public $componentType;
  /**
   * Output only. Component's upgrade state.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Type of component
   *
   * Accepted values: VMWARE_COMPONENT_TYPE_UNSPECIFIED, VCENTER, ESXI, NSXT_UC,
   * NSXT_EDGE, NSXT_MGR, HCX, VSAN, DVS, NAMESERVER_VM, KMS_VM, WITNESS_VM,
   * NSXT, CLUSTER
   *
   * @param self::COMPONENT_TYPE_* $componentType
   */
  public function setComponentType($componentType)
  {
    $this->componentType = $componentType;
  }
  /**
   * @return self::COMPONENT_TYPE_*
   */
  public function getComponentType()
  {
    return $this->componentType;
  }
  /**
   * Output only. Component's upgrade state.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, PAUSED, SUCCEEDED, FAILED,
   * NOT_STARTED, NOT_APPLICABLE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareUpgradeComponent::class, 'Google_Service_VMwareEngine_VmwareUpgradeComponent');
