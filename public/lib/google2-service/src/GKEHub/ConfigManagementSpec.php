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

class ConfigManagementSpec extends \Google\Model
{
  /**
   * Unspecified
   */
  public const MANAGEMENT_MANAGEMENT_UNSPECIFIED = 'MANAGEMENT_UNSPECIFIED';
  /**
   * Google will manage the Feature for the cluster.
   */
  public const MANAGEMENT_MANAGEMENT_AUTOMATIC = 'MANAGEMENT_AUTOMATIC';
  /**
   * User will manually manage the Feature for the cluster.
   */
  public const MANAGEMENT_MANAGEMENT_MANUAL = 'MANAGEMENT_MANUAL';
  protected $binauthzType = ConfigManagementBinauthzConfig::class;
  protected $binauthzDataType = '';
  /**
   * Optional. The user-specified cluster name used by Config Sync cluster-name-
   * selector annotation or ClusterSelector, for applying configs to only a
   * subset of clusters. Omit this field if the cluster's fleet membership name
   * is used by Config Sync cluster-name-selector annotation or ClusterSelector.
   * Set this field if a name different from the cluster's fleet membership name
   * is used by Config Sync cluster-name-selector annotation or ClusterSelector.
   *
   * @var string
   */
  public $cluster;
  protected $configSyncType = ConfigManagementConfigSync::class;
  protected $configSyncDataType = '';
  protected $hierarchyControllerType = ConfigManagementHierarchyControllerConfig::class;
  protected $hierarchyControllerDataType = '';
  /**
   * Optional. Enables automatic Feature management.
   *
   * @var string
   */
  public $management;
  protected $policyControllerType = ConfigManagementPolicyController::class;
  protected $policyControllerDataType = '';
  /**
   * Optional. Version of ACM installed.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. Binauthz conifguration for the cluster. Deprecated: This field
   * will be ignored and should not be set.
   *
   * @deprecated
   * @param ConfigManagementBinauthzConfig $binauthz
   */
  public function setBinauthz(ConfigManagementBinauthzConfig $binauthz)
  {
    $this->binauthz = $binauthz;
  }
  /**
   * @deprecated
   * @return ConfigManagementBinauthzConfig
   */
  public function getBinauthz()
  {
    return $this->binauthz;
  }
  /**
   * Optional. The user-specified cluster name used by Config Sync cluster-name-
   * selector annotation or ClusterSelector, for applying configs to only a
   * subset of clusters. Omit this field if the cluster's fleet membership name
   * is used by Config Sync cluster-name-selector annotation or ClusterSelector.
   * Set this field if a name different from the cluster's fleet membership name
   * is used by Config Sync cluster-name-selector annotation or ClusterSelector.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Optional. Config Sync configuration for the cluster.
   *
   * @param ConfigManagementConfigSync $configSync
   */
  public function setConfigSync(ConfigManagementConfigSync $configSync)
  {
    $this->configSync = $configSync;
  }
  /**
   * @return ConfigManagementConfigSync
   */
  public function getConfigSync()
  {
    return $this->configSync;
  }
  /**
   * Optional. Hierarchy Controller configuration for the cluster. Deprecated:
   * Configuring Hierarchy Controller through the configmanagement feature is no
   * longer recommended. Use https://github.com/kubernetes-sigs/hierarchical-
   * namespaces instead.
   *
   * @deprecated
   * @param ConfigManagementHierarchyControllerConfig $hierarchyController
   */
  public function setHierarchyController(ConfigManagementHierarchyControllerConfig $hierarchyController)
  {
    $this->hierarchyController = $hierarchyController;
  }
  /**
   * @deprecated
   * @return ConfigManagementHierarchyControllerConfig
   */
  public function getHierarchyController()
  {
    return $this->hierarchyController;
  }
  /**
   * Optional. Enables automatic Feature management.
   *
   * Accepted values: MANAGEMENT_UNSPECIFIED, MANAGEMENT_AUTOMATIC,
   * MANAGEMENT_MANUAL
   *
   * @param self::MANAGEMENT_* $management
   */
  public function setManagement($management)
  {
    $this->management = $management;
  }
  /**
   * @return self::MANAGEMENT_*
   */
  public function getManagement()
  {
    return $this->management;
  }
  /**
   * Optional. Policy Controller configuration for the cluster. Deprecated:
   * Configuring Policy Controller through the configmanagement feature is no
   * longer recommended. Use the policycontroller feature instead.
   *
   * @deprecated
   * @param ConfigManagementPolicyController $policyController
   */
  public function setPolicyController(ConfigManagementPolicyController $policyController)
  {
    $this->policyController = $policyController;
  }
  /**
   * @deprecated
   * @return ConfigManagementPolicyController
   */
  public function getPolicyController()
  {
    return $this->policyController;
  }
  /**
   * Optional. Version of ACM installed.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementSpec::class, 'Google_Service_GKEHub_ConfigManagementSpec');
