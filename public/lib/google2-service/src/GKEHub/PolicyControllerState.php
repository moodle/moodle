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

namespace Google\Service\GKEHub;

class PolicyControllerState extends \Google\Model
{
  /**
   * The lifecycle state is unspecified.
   */
  public const STATE_LIFECYCLE_STATE_UNSPECIFIED = 'LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * The PC does not exist on the given cluster, and no k8s resources of any
   * type that are associated with the PC should exist there. The cluster does
   * not possess a membership with the PCH.
   */
  public const STATE_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * The PCH possesses a Membership, however the PC is not fully installed on
   * the cluster. In this state the hub can be expected to be taking actions to
   * install the PC on the cluster.
   */
  public const STATE_INSTALLING = 'INSTALLING';
  /**
   * The PC is fully installed on the cluster and in an operational mode. In
   * this state PCH will be reconciling state with the PC, and the PC will be
   * performing it's operational tasks per that software. Entering a READY state
   * requires that the hub has confirmed the PC is installed and its pods are
   * operational with the version of the PC the PCH expects.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The PC is fully installed, but in the process of changing the configuration
   * (including changing the version of PC either up and down, or modifying the
   * manifests of PC) of the resources running on the cluster. The PCH has a
   * Membership, is aware of the version the cluster should be running in, but
   * has not confirmed for itself that the PC is running with that version.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The PC may have resources on the cluster, but the PCH wishes to remove the
   * Membership. The Membership still exists.
   */
  public const STATE_DECOMMISSIONING = 'DECOMMISSIONING';
  /**
   * The PC is not operational, and the PCH is unable to act to make it
   * operational. Entering a CLUSTER_ERROR state happens automatically when the
   * PCH determines that a PC installed on the cluster is non-operative or that
   * the cluster does not meet requirements set for the PCH to administer the
   * cluster but has nevertheless been given an instruction to do so (such as
   * ‘install').
   */
  public const STATE_CLUSTER_ERROR = 'CLUSTER_ERROR';
  /**
   * In this state, the PC may still be operational, and only the PCH is unable
   * to act. The hub should not issue instructions to change the PC state, or
   * otherwise interfere with the on-cluster resources. Entering a HUB_ERROR
   * state happens automatically when the PCH determines the hub is in an
   * unhealthy state and it wishes to ‘take hands off' to avoid corrupting the
   * PC or other data.
   */
  public const STATE_HUB_ERROR = 'HUB_ERROR';
  /**
   * Policy Controller (PC) is installed but suspended. This means that the
   * policies are not enforced, but violations are still recorded (through
   * audit).
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * PoCo Hub is not taking any action to reconcile cluster objects. Changes to
   * those objects will not be overwritten by PoCo Hub.
   */
  public const STATE_DETACHED = 'DETACHED';
  protected $componentStatesType = PolicyControllerOnClusterState::class;
  protected $componentStatesDataType = 'map';
  protected $policyContentStateType = PolicyControllerPolicyContentState::class;
  protected $policyContentStateDataType = '';
  /**
   * The overall Policy Controller lifecycle state observed by the Hub Feature
   * controller.
   *
   * @var string
   */
  public $state;

  /**
   * Currently these include (also serving as map keys): 1. "admission" 2.
   * "audit" 3. "mutation"
   *
   * @param PolicyControllerOnClusterState[] $componentStates
   */
  public function setComponentStates($componentStates)
  {
    $this->componentStates = $componentStates;
  }
  /**
   * @return PolicyControllerOnClusterState[]
   */
  public function getComponentStates()
  {
    return $this->componentStates;
  }
  /**
   * The overall content state observed by the Hub Feature controller.
   *
   * @param PolicyControllerPolicyContentState $policyContentState
   */
  public function setPolicyContentState(PolicyControllerPolicyContentState $policyContentState)
  {
    $this->policyContentState = $policyContentState;
  }
  /**
   * @return PolicyControllerPolicyContentState
   */
  public function getPolicyContentState()
  {
    return $this->policyContentState;
  }
  /**
   * The overall Policy Controller lifecycle state observed by the Hub Feature
   * controller.
   *
   * Accepted values: LIFECYCLE_STATE_UNSPECIFIED, NOT_INSTALLED, INSTALLING,
   * ACTIVE, UPDATING, DECOMMISSIONING, CLUSTER_ERROR, HUB_ERROR, SUSPENDED,
   * DETACHED
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
class_alias(PolicyControllerState::class, 'Google_Service_GKEHub_PolicyControllerState');
