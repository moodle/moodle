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

class ConfigManagementState extends \Google\Model
{
  protected $binauthzStateType = ConfigManagementBinauthzState::class;
  protected $binauthzStateDataType = '';
  /**
   * Output only. This field is set to the `cluster_name` field of the
   * Membership Spec if it is not empty. Otherwise, it is set to the cluster's
   * fleet membership name.
   *
   * @var string
   */
  public $clusterName;
  protected $configSyncStateType = ConfigManagementConfigSyncState::class;
  protected $configSyncStateDataType = '';
  protected $hierarchyControllerStateType = ConfigManagementHierarchyControllerState::class;
  protected $hierarchyControllerStateDataType = '';
  /**
   * Output only. The Kubernetes API server version of the cluster.
   *
   * @var string
   */
  public $kubernetesApiServerVersion;
  protected $membershipSpecType = ConfigManagementSpec::class;
  protected $membershipSpecDataType = '';
  protected $operatorStateType = ConfigManagementOperatorState::class;
  protected $operatorStateDataType = '';
  protected $policyControllerStateType = ConfigManagementPolicyControllerState::class;
  protected $policyControllerStateDataType = '';

  /**
   * Output only. Binauthz status.
   *
   * @param ConfigManagementBinauthzState $binauthzState
   */
  public function setBinauthzState(ConfigManagementBinauthzState $binauthzState)
  {
    $this->binauthzState = $binauthzState;
  }
  /**
   * @return ConfigManagementBinauthzState
   */
  public function getBinauthzState()
  {
    return $this->binauthzState;
  }
  /**
   * Output only. This field is set to the `cluster_name` field of the
   * Membership Spec if it is not empty. Otherwise, it is set to the cluster's
   * fleet membership name.
   *
   * @param string $clusterName
   */
  public function setClusterName($clusterName)
  {
    $this->clusterName = $clusterName;
  }
  /**
   * @return string
   */
  public function getClusterName()
  {
    return $this->clusterName;
  }
  /**
   * Output only. Current sync status.
   *
   * @param ConfigManagementConfigSyncState $configSyncState
   */
  public function setConfigSyncState(ConfigManagementConfigSyncState $configSyncState)
  {
    $this->configSyncState = $configSyncState;
  }
  /**
   * @return ConfigManagementConfigSyncState
   */
  public function getConfigSyncState()
  {
    return $this->configSyncState;
  }
  /**
   * Output only. Hierarchy Controller status.
   *
   * @param ConfigManagementHierarchyControllerState $hierarchyControllerState
   */
  public function setHierarchyControllerState(ConfigManagementHierarchyControllerState $hierarchyControllerState)
  {
    $this->hierarchyControllerState = $hierarchyControllerState;
  }
  /**
   * @return ConfigManagementHierarchyControllerState
   */
  public function getHierarchyControllerState()
  {
    return $this->hierarchyControllerState;
  }
  /**
   * Output only. The Kubernetes API server version of the cluster.
   *
   * @param string $kubernetesApiServerVersion
   */
  public function setKubernetesApiServerVersion($kubernetesApiServerVersion)
  {
    $this->kubernetesApiServerVersion = $kubernetesApiServerVersion;
  }
  /**
   * @return string
   */
  public function getKubernetesApiServerVersion()
  {
    return $this->kubernetesApiServerVersion;
  }
  /**
   * Output only. Membership configuration in the cluster. This represents the
   * actual state in the cluster, while the MembershipSpec in the FeatureSpec
   * represents the intended state.
   *
   * @param ConfigManagementSpec $membershipSpec
   */
  public function setMembershipSpec(ConfigManagementSpec $membershipSpec)
  {
    $this->membershipSpec = $membershipSpec;
  }
  /**
   * @return ConfigManagementSpec
   */
  public function getMembershipSpec()
  {
    return $this->membershipSpec;
  }
  /**
   * Output only. Current install status of ACM's Operator.
   *
   * @param ConfigManagementOperatorState $operatorState
   */
  public function setOperatorState(ConfigManagementOperatorState $operatorState)
  {
    $this->operatorState = $operatorState;
  }
  /**
   * @return ConfigManagementOperatorState
   */
  public function getOperatorState()
  {
    return $this->operatorState;
  }
  /**
   * Output only. PolicyController status.
   *
   * @param ConfigManagementPolicyControllerState $policyControllerState
   */
  public function setPolicyControllerState(ConfigManagementPolicyControllerState $policyControllerState)
  {
    $this->policyControllerState = $policyControllerState;
  }
  /**
   * @return ConfigManagementPolicyControllerState
   */
  public function getPolicyControllerState()
  {
    return $this->policyControllerState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementState::class, 'Google_Service_GKEHub_ConfigManagementState');
