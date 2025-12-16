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

class MembershipFeatureState extends \Google\Model
{
  protected $appdevexperienceType = AppDevExperienceFeatureState::class;
  protected $appdevexperienceDataType = '';
  protected $clusterupgradeType = ClusterUpgradeMembershipState::class;
  protected $clusterupgradeDataType = '';
  protected $configmanagementType = ConfigManagementMembershipState::class;
  protected $configmanagementDataType = '';
  protected $fleetobservabilityType = FleetObservabilityMembershipState::class;
  protected $fleetobservabilityDataType = '';
  protected $identityserviceType = IdentityServiceMembershipState::class;
  protected $identityserviceDataType = '';
  protected $policycontrollerType = PolicyControllerMembershipState::class;
  protected $policycontrollerDataType = '';
  protected $servicemeshType = ServiceMeshMembershipState::class;
  protected $servicemeshDataType = '';
  protected $stateType = FeatureState::class;
  protected $stateDataType = '';

  /**
   * @param AppDevExperienceFeatureState
   */
  public function setAppdevexperience(AppDevExperienceFeatureState $appdevexperience)
  {
    $this->appdevexperience = $appdevexperience;
  }
  /**
   * @return AppDevExperienceFeatureState
   */
  public function getAppdevexperience()
  {
    return $this->appdevexperience;
  }
  /**
   * @param ClusterUpgradeMembershipState
   */
  public function setClusterupgrade(ClusterUpgradeMembershipState $clusterupgrade)
  {
    $this->clusterupgrade = $clusterupgrade;
  }
  /**
   * @return ClusterUpgradeMembershipState
   */
  public function getClusterupgrade()
  {
    return $this->clusterupgrade;
  }
  /**
   * @param ConfigManagementMembershipState
   */
  public function setConfigmanagement(ConfigManagementMembershipState $configmanagement)
  {
    $this->configmanagement = $configmanagement;
  }
  /**
   * @return ConfigManagementMembershipState
   */
  public function getConfigmanagement()
  {
    return $this->configmanagement;
  }
  /**
   * @param FleetObservabilityMembershipState
   */
  public function setFleetobservability(FleetObservabilityMembershipState $fleetobservability)
  {
    $this->fleetobservability = $fleetobservability;
  }
  /**
   * @return FleetObservabilityMembershipState
   */
  public function getFleetobservability()
  {
    return $this->fleetobservability;
  }
  /**
   * @param IdentityServiceMembershipState
   */
  public function setIdentityservice(IdentityServiceMembershipState $identityservice)
  {
    $this->identityservice = $identityservice;
  }
  /**
   * @return IdentityServiceMembershipState
   */
  public function getIdentityservice()
  {
    return $this->identityservice;
  }
  /**
   * @param PolicyControllerMembershipState
   */
  public function setPolicycontroller(PolicyControllerMembershipState $policycontroller)
  {
    $this->policycontroller = $policycontroller;
  }
  /**
   * @return PolicyControllerMembershipState
   */
  public function getPolicycontroller()
  {
    return $this->policycontroller;
  }
  /**
   * @param ServiceMeshMembershipState
   */
  public function setServicemesh(ServiceMeshMembershipState $servicemesh)
  {
    $this->servicemesh = $servicemesh;
  }
  /**
   * @return ServiceMeshMembershipState
   */
  public function getServicemesh()
  {
    return $this->servicemesh;
  }
  /**
   * @param FeatureState
   */
  public function setState(FeatureState $state)
  {
    $this->state = $state;
  }
  /**
   * @return FeatureState
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MembershipFeatureState::class, 'Google_Service_GKEHub_MembershipFeatureState');
