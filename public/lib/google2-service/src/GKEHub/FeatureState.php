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

class FeatureState extends \Google\Model
{
  protected $appdevexperienceType = AppDevExperienceState::class;
  protected $appdevexperienceDataType = '';
  protected $clusterupgradeType = ClusterUpgradeState::class;
  protected $clusterupgradeDataType = '';
  protected $configmanagementType = ConfigManagementState::class;
  protected $configmanagementDataType = '';
  protected $identityserviceType = IdentityServiceState::class;
  protected $identityserviceDataType = '';
  protected $meteringType = MeteringState::class;
  protected $meteringDataType = '';
  protected $policycontrollerType = PolicyControllerState::class;
  protected $policycontrollerDataType = '';
  protected $rbacrolebindingactuationType = RBACRoleBindingActuationState::class;
  protected $rbacrolebindingactuationDataType = '';
  protected $servicemeshType = ServiceMeshState::class;
  protected $servicemeshDataType = '';
  protected $stateType = State::class;
  protected $stateDataType = '';

  /**
   * Appdevexperience specific state.
   *
   * @param AppDevExperienceState $appdevexperience
   */
  public function setAppdevexperience(AppDevExperienceState $appdevexperience)
  {
    $this->appdevexperience = $appdevexperience;
  }
  /**
   * @return AppDevExperienceState
   */
  public function getAppdevexperience()
  {
    return $this->appdevexperience;
  }
  /**
   * Cluster upgrade state.
   *
   * @param ClusterUpgradeState $clusterupgrade
   */
  public function setClusterupgrade(ClusterUpgradeState $clusterupgrade)
  {
    $this->clusterupgrade = $clusterupgrade;
  }
  /**
   * @return ClusterUpgradeState
   */
  public function getClusterupgrade()
  {
    return $this->clusterupgrade;
  }
  /**
   * Config Management state
   *
   * @param ConfigManagementState $configmanagement
   */
  public function setConfigmanagement(ConfigManagementState $configmanagement)
  {
    $this->configmanagement = $configmanagement;
  }
  /**
   * @return ConfigManagementState
   */
  public function getConfigmanagement()
  {
    return $this->configmanagement;
  }
  /**
   * Identity service state
   *
   * @param IdentityServiceState $identityservice
   */
  public function setIdentityservice(IdentityServiceState $identityservice)
  {
    $this->identityservice = $identityservice;
  }
  /**
   * @return IdentityServiceState
   */
  public function getIdentityservice()
  {
    return $this->identityservice;
  }
  /**
   * Metering state
   *
   * @param MeteringState $metering
   */
  public function setMetering(MeteringState $metering)
  {
    $this->metering = $metering;
  }
  /**
   * @return MeteringState
   */
  public function getMetering()
  {
    return $this->metering;
  }
  /**
   * Policy Controller state
   *
   * @param PolicyControllerState $policycontroller
   */
  public function setPolicycontroller(PolicyControllerState $policycontroller)
  {
    $this->policycontroller = $policycontroller;
  }
  /**
   * @return PolicyControllerState
   */
  public function getPolicycontroller()
  {
    return $this->policycontroller;
  }
  /**
   * RBAC Role Binding Actuation state
   *
   * @param RBACRoleBindingActuationState $rbacrolebindingactuation
   */
  public function setRbacrolebindingactuation(RBACRoleBindingActuationState $rbacrolebindingactuation)
  {
    $this->rbacrolebindingactuation = $rbacrolebindingactuation;
  }
  /**
   * @return RBACRoleBindingActuationState
   */
  public function getRbacrolebindingactuation()
  {
    return $this->rbacrolebindingactuation;
  }
  /**
   * Service mesh state
   *
   * @param ServiceMeshState $servicemesh
   */
  public function setServicemesh(ServiceMeshState $servicemesh)
  {
    $this->servicemesh = $servicemesh;
  }
  /**
   * @return ServiceMeshState
   */
  public function getServicemesh()
  {
    return $this->servicemesh;
  }
  /**
   * The high-level state of this MembershipFeature.
   *
   * @param State $state
   */
  public function setState(State $state)
  {
    $this->state = $state;
  }
  /**
   * @return State
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FeatureState::class, 'Google_Service_GKEHub_FeatureState');
