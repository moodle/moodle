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

namespace Google\Service\NetworkSecurity;

class FirewallEndpointAssociation extends \Google\Model
{
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Active and ready for traffic.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Down or in an error state.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The project that housed the association has been deleted.
   */
  public const STATE_ORPHAN = 'ORPHAN';
  /**
   * Output only. Create time stamp
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Whether the association is disabled. True indicates that traffic
   * won't be intercepted
   *
   * @var bool
   */
  public $disabled;
  /**
   * Required. The URL of the FirewallEndpoint that is being associated.
   *
   * @var string
   */
  public $firewallEndpoint;
  /**
   * Optional. Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. Identifier. name of resource
   *
   * @var string
   */
  public $name;
  /**
   * Required. The URL of the network that is being associated.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. Whether reconciling is in progress, recommended per
   * https://google.aip.dev/128.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. Current state of the association.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. The URL of the TlsInspectionPolicy that is being associated.
   *
   * @var string
   */
  public $tlsInspectionPolicy;
  /**
   * Output only. Update time stamp
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Create time stamp
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Whether the association is disabled. True indicates that traffic
   * won't be intercepted
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Required. The URL of the FirewallEndpoint that is being associated.
   *
   * @param string $firewallEndpoint
   */
  public function setFirewallEndpoint($firewallEndpoint)
  {
    $this->firewallEndpoint = $firewallEndpoint;
  }
  /**
   * @return string
   */
  public function getFirewallEndpoint()
  {
    return $this->firewallEndpoint;
  }
  /**
   * Optional. Labels as key value pairs
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Immutable. Identifier. name of resource
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
   * Required. The URL of the network that is being associated.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Output only. Whether reconciling is in progress, recommended per
   * https://google.aip.dev/128.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. Current state of the association.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, INACTIVE,
   * ORPHAN
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
  /**
   * Optional. The URL of the TlsInspectionPolicy that is being associated.
   *
   * @param string $tlsInspectionPolicy
   */
  public function setTlsInspectionPolicy($tlsInspectionPolicy)
  {
    $this->tlsInspectionPolicy = $tlsInspectionPolicy;
  }
  /**
   * @return string
   */
  public function getTlsInspectionPolicy()
  {
    return $this->tlsInspectionPolicy;
  }
  /**
   * Output only. Update time stamp
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FirewallEndpointAssociation::class, 'Google_Service_NetworkSecurity_FirewallEndpointAssociation');
