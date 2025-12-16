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

namespace Google\Service\Container;

class Cluster extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The PROVISIONING state indicates the cluster is being created.
   */
  public const STATUS_PROVISIONING = 'PROVISIONING';
  /**
   * The RUNNING state indicates the cluster has been created and is fully
   * usable.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The RECONCILING state indicates that some work is actively being done on
   * the cluster, such as upgrading the master or node software. Details can be
   * found in the `statusMessage` field.
   */
  public const STATUS_RECONCILING = 'RECONCILING';
  /**
   * The STOPPING state indicates the cluster is being deleted.
   */
  public const STATUS_STOPPING = 'STOPPING';
  /**
   * The ERROR state indicates the cluster is unusable. It will be automatically
   * deleted. Details can be found in the `statusMessage` field.
   */
  public const STATUS_ERROR = 'ERROR';
  /**
   * The DEGRADED state indicates the cluster requires user action to restore
   * full functionality. Details can be found in the `statusMessage` field.
   */
  public const STATUS_DEGRADED = 'DEGRADED';
  protected $collection_key = 'nodePools';
  protected $addonsConfigType = AddonsConfig::class;
  protected $addonsConfigDataType = '';
  /**
   * The list of user specified Kubernetes feature gates. Each string represents
   * the activation status of a feature gate (e.g. "featureX=true" or
   * "featureX=false")
   *
   * @var string[]
   */
  public $alphaClusterFeatureGates;
  protected $anonymousAuthenticationConfigType = AnonymousAuthenticationConfig::class;
  protected $anonymousAuthenticationConfigDataType = '';
  protected $authenticatorGroupsConfigType = AuthenticatorGroupsConfig::class;
  protected $authenticatorGroupsConfigDataType = '';
  protected $autopilotType = Autopilot::class;
  protected $autopilotDataType = '';
  protected $autoscalingType = ClusterAutoscaling::class;
  protected $autoscalingDataType = '';
  protected $binaryAuthorizationType = BinaryAuthorization::class;
  protected $binaryAuthorizationDataType = '';
  /**
   * The IP address range of the container pods in this cluster, in
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`). Leave blank to have one automatically
   * chosen or specify a `/14` block in `10.0.0.0/8`.
   *
   * @var string
   */
  public $clusterIpv4Cidr;
  protected $compliancePostureConfigType = CompliancePostureConfig::class;
  protected $compliancePostureConfigDataType = '';
  protected $conditionsType = StatusCondition::class;
  protected $conditionsDataType = 'array';
  protected $confidentialNodesType = ConfidentialNodes::class;
  protected $confidentialNodesDataType = '';
  protected $controlPlaneEndpointsConfigType = ControlPlaneEndpointsConfig::class;
  protected $controlPlaneEndpointsConfigDataType = '';
  protected $costManagementConfigType = CostManagementConfig::class;
  protected $costManagementConfigDataType = '';
  /**
   * Output only. The time the cluster was created, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The current software version of the master endpoint.
   *
   * @var string
   */
  public $currentMasterVersion;
  /**
   * Output only. The number of nodes currently in the cluster. Deprecated. Call
   * Kubernetes API directly to retrieve node information.
   *
   * @deprecated
   * @var int
   */
  public $currentNodeCount;
  /**
   * Output only. Deprecated, use
   * [NodePools.version](https://cloud.google.com/kubernetes-
   * engine/docs/reference/rest/v1/projects.locations.clusters.nodePools)
   * instead. The current version of the node software components. If they are
   * currently at multiple versions because they're in the process of being
   * upgraded, this reflects the minimum version of all nodes.
   *
   * @deprecated
   * @var string
   */
  public $currentNodeVersion;
  protected $databaseEncryptionType = DatabaseEncryption::class;
  protected $databaseEncryptionDataType = '';
  protected $defaultMaxPodsConstraintType = MaxPodsConstraint::class;
  protected $defaultMaxPodsConstraintDataType = '';
  /**
   * An optional description of this cluster.
   *
   * @var string
   */
  public $description;
  protected $enableK8sBetaApisType = K8sBetaAPIConfig::class;
  protected $enableK8sBetaApisDataType = '';
  /**
   * Kubernetes alpha features are enabled on this cluster. This includes alpha
   * API groups (e.g. v1alpha1) and features that may not be production ready in
   * the kubernetes version of the master and nodes. The cluster has no SLA for
   * uptime and master/node upgrades are disabled. Alpha enabled clusters are
   * automatically deleted thirty days after creation.
   *
   * @var bool
   */
  public $enableKubernetesAlpha;
  /**
   * Enable the ability to use Cloud TPUs in this cluster. This field is
   * deprecated due to the deprecation of 2VM TPU. The end of life date for 2VM
   * TPU is 2025-04-25.
   *
   * @deprecated
   * @var bool
   */
  public $enableTpu;
  /**
   * Output only. The IP address of this cluster's master endpoint. The endpoint
   * can be accessed from the internet at `https://username:password@endpoint/`.
   * See the `masterAuth` property of this resource for username and password
   * information.
   *
   * @var string
   */
  public $endpoint;
  protected $enterpriseConfigType = EnterpriseConfig::class;
  protected $enterpriseConfigDataType = '';
  /**
   * This checksum is computed by the server based on the value of cluster
   * fields, and may be sent on update requests to ensure the client has an up-
   * to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The time the cluster will be automatically deleted in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @var string
   */
  public $expireTime;
  protected $fleetType = Fleet::class;
  protected $fleetDataType = '';
  protected $gkeAutoUpgradeConfigType = GkeAutoUpgradeConfig::class;
  protected $gkeAutoUpgradeConfigDataType = '';
  /**
   * Output only. Unique id for the cluster.
   *
   * @var string
   */
  public $id;
  protected $identityServiceConfigType = IdentityServiceConfig::class;
  protected $identityServiceConfigDataType = '';
  /**
   * The initial Kubernetes version for this cluster. Valid versions are those
   * found in validMasterVersions returned by getServerConfig. The version can
   * be upgraded over time; such upgrades are reflected in currentMasterVersion
   * and currentNodeVersion. Users may specify either explicit versions offered
   * by Kubernetes Engine or version aliases, which have the following behavior:
   * - "latest": picks the highest valid Kubernetes version - "1.X": picks the
   * highest valid patch+gke.N patch in the 1.X version - "1.X.Y": picks the
   * highest valid gke.N patch in the 1.X.Y version - "1.X.Y-gke.N": picks an
   * explicit Kubernetes version - "","-": picks the default Kubernetes version
   *
   * @var string
   */
  public $initialClusterVersion;
  /**
   * The number of nodes to create in this cluster. You must ensure that your
   * Compute Engine [resource quota](https://cloud.google.com/compute/quotas) is
   * sufficient for this number of instances. You must also have available
   * firewall and routes quota. For requests, this field should only be used in
   * lieu of a "node_pool" object, since this configuration (along with the
   * "node_config") will be used to create a "NodePool" object with an auto-
   * generated name. Do not use this and a node_pool at the same time. This
   * field is deprecated, use node_pool.initial_node_count instead.
   *
   * @deprecated
   * @var int
   */
  public $initialNodeCount;
  /**
   * Output only. Deprecated. Use node_pools.instance_group_urls.
   *
   * @deprecated
   * @var string[]
   */
  public $instanceGroupUrls;
  protected $ipAllocationPolicyType = IPAllocationPolicy::class;
  protected $ipAllocationPolicyDataType = '';
  /**
   * The fingerprint of the set of labels for this cluster.
   *
   * @var string
   */
  public $labelFingerprint;
  protected $legacyAbacType = LegacyAbac::class;
  protected $legacyAbacDataType = '';
  /**
   * Output only. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/regions-zones/regions-
   * zones#available) or [region](https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones#available) in which the cluster resides.
   *
   * @var string
   */
  public $location;
  /**
   * The list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster's nodes should be located. This field provides a default value if
   * [NodePool.Locations](https://cloud.google.com/kubernetes-engine/docs/refere
   * nce/rest/v1/projects.locations.clusters.nodePools#NodePool.FIELDS.locations
   * ) are not specified during node pool creation. Warning: changing cluster
   * locations will update the
   * [NodePool.Locations](https://cloud.google.com/kubernetes-engine/docs/refere
   * nce/rest/v1/projects.locations.clusters.nodePools#NodePool.FIELDS.locations
   * ) of all node pools and will result in nodes being added and/or removed.
   *
   * @var string[]
   */
  public $locations;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * The logging service the cluster should use to write logs. Currently
   * available options: * `logging.googleapis.com/kubernetes` - The Cloud
   * Logging service with a Kubernetes-native resource model *
   * `logging.googleapis.com` - The legacy Cloud Logging service (no longer
   * available as of GKE 1.15). * `none` - no logs will be exported from the
   * cluster. If left as an empty string,`logging.googleapis.com/kubernetes`
   * will be used for GKE 1.14+ or `logging.googleapis.com` for earlier
   * versions.
   *
   * @var string
   */
  public $loggingService;
  protected $maintenancePolicyType = MaintenancePolicy::class;
  protected $maintenancePolicyDataType = '';
  protected $managedOpentelemetryConfigType = ManagedOpenTelemetryConfig::class;
  protected $managedOpentelemetryConfigDataType = '';
  protected $masterAuthType = MasterAuth::class;
  protected $masterAuthDataType = '';
  protected $masterAuthorizedNetworksConfigType = MasterAuthorizedNetworksConfig::class;
  protected $masterAuthorizedNetworksConfigDataType = '';
  protected $meshCertificatesType = MeshCertificates::class;
  protected $meshCertificatesDataType = '';
  protected $monitoringConfigType = MonitoringConfig::class;
  protected $monitoringConfigDataType = '';
  /**
   * The monitoring service the cluster should use to write metrics. Currently
   * available options: * `monitoring.googleapis.com/kubernetes` - The Cloud
   * Monitoring service with a Kubernetes-native resource model *
   * `monitoring.googleapis.com` - The legacy Cloud Monitoring service (no
   * longer available as of GKE 1.15). * `none` - No metrics will be exported
   * from the cluster. If left as an empty
   * string,`monitoring.googleapis.com/kubernetes` will be used for GKE 1.14+ or
   * `monitoring.googleapis.com` for earlier versions.
   *
   * @var string
   */
  public $monitoringService;
  /**
   * The name of this cluster. The name must be unique within this project and
   * location (e.g. zone or region), and can be up to 40 characters with the
   * following restrictions: * Lowercase letters, numbers, and hyphens only. *
   * Must start with a letter. * Must end with a number or a letter.
   *
   * @var string
   */
  public $name;
  /**
   * The name of the Google Compute Engine
   * [network](https://cloud.google.com/compute/docs/networks-and-
   * firewalls#networks) to which the cluster is connected. If left unspecified,
   * the `default` network will be used.
   *
   * @var string
   */
  public $network;
  protected $networkConfigType = NetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $networkPolicyType = NetworkPolicy::class;
  protected $networkPolicyDataType = '';
  protected $nodeConfigType = NodeConfig::class;
  protected $nodeConfigDataType = '';
  /**
   * Output only. The size of the address space on each node for hosting
   * containers. This is provisioned from within the `container_ipv4_cidr`
   * range. This field will only be set when cluster is in route-based network
   * mode.
   *
   * @var int
   */
  public $nodeIpv4CidrSize;
  protected $nodePoolAutoConfigType = NodePoolAutoConfig::class;
  protected $nodePoolAutoConfigDataType = '';
  protected $nodePoolDefaultsType = NodePoolDefaults::class;
  protected $nodePoolDefaultsDataType = '';
  protected $nodePoolsType = NodePool::class;
  protected $nodePoolsDataType = 'array';
  protected $notificationConfigType = NotificationConfig::class;
  protected $notificationConfigDataType = '';
  protected $parentProductConfigType = ParentProductConfig::class;
  protected $parentProductConfigDataType = '';
  protected $podAutoscalingType = PodAutoscaling::class;
  protected $podAutoscalingDataType = '';
  protected $privateClusterConfigType = PrivateClusterConfig::class;
  protected $privateClusterConfigDataType = '';
  protected $rbacBindingConfigType = RBACBindingConfig::class;
  protected $rbacBindingConfigDataType = '';
  protected $releaseChannelType = ReleaseChannel::class;
  protected $releaseChannelDataType = '';
  /**
   * The resource labels for the cluster to use to annotate any related Google
   * Compute Engine resources.
   *
   * @var string[]
   */
  public $resourceLabels;
  protected $resourceUsageExportConfigType = ResourceUsageExportConfig::class;
  protected $resourceUsageExportConfigDataType = '';
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $secretManagerConfigType = SecretManagerConfig::class;
  protected $secretManagerConfigDataType = '';
  protected $securityPostureConfigType = SecurityPostureConfig::class;
  protected $securityPostureConfigDataType = '';
  /**
   * Output only. Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. The IP address range of the Kubernetes services in this
   * cluster, in [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-
   * Domain_Routing) notation (e.g. `1.2.3.4/29`). Service addresses are
   * typically put in the last `/16` from the container CIDR.
   *
   * @var string
   */
  public $servicesIpv4Cidr;
  protected $shieldedNodesType = ShieldedNodes::class;
  protected $shieldedNodesDataType = '';
  /**
   * Output only. The current status of this cluster.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. Deprecated. Use conditions instead. Additional information
   * about the current status of this cluster, if available.
   *
   * @deprecated
   * @var string
   */
  public $statusMessage;
  /**
   * The name of the Google Compute Engine
   * [subnetwork](https://cloud.google.com/compute/docs/subnetworks) to which
   * the cluster is connected.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * Output only. The IP address range of the Cloud TPUs in this cluster, in
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `1.2.3.4/29`). This field is deprecated due to the
   * deprecation of 2VM TPU. The end of life date for 2VM TPU is 2025-04-25.
   *
   * @deprecated
   * @var string
   */
  public $tpuIpv4CidrBlock;
  protected $userManagedKeysConfigType = UserManagedKeysConfig::class;
  protected $userManagedKeysConfigDataType = '';
  protected $verticalPodAutoscalingType = VerticalPodAutoscaling::class;
  protected $verticalPodAutoscalingDataType = '';
  protected $workloadIdentityConfigType = WorkloadIdentityConfig::class;
  protected $workloadIdentityConfigDataType = '';
  /**
   * Output only. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field is deprecated, use location instead.
   *
   * @deprecated
   * @var string
   */
  public $zone;

  /**
   * Configurations for the various addons available to run in the cluster.
   *
   * @param AddonsConfig $addonsConfig
   */
  public function setAddonsConfig(AddonsConfig $addonsConfig)
  {
    $this->addonsConfig = $addonsConfig;
  }
  /**
   * @return AddonsConfig
   */
  public function getAddonsConfig()
  {
    return $this->addonsConfig;
  }
  /**
   * The list of user specified Kubernetes feature gates. Each string represents
   * the activation status of a feature gate (e.g. "featureX=true" or
   * "featureX=false")
   *
   * @param string[] $alphaClusterFeatureGates
   */
  public function setAlphaClusterFeatureGates($alphaClusterFeatureGates)
  {
    $this->alphaClusterFeatureGates = $alphaClusterFeatureGates;
  }
  /**
   * @return string[]
   */
  public function getAlphaClusterFeatureGates()
  {
    return $this->alphaClusterFeatureGates;
  }
  /**
   * Configuration for limiting anonymous access to all endpoints except the
   * health checks.
   *
   * @param AnonymousAuthenticationConfig $anonymousAuthenticationConfig
   */
  public function setAnonymousAuthenticationConfig(AnonymousAuthenticationConfig $anonymousAuthenticationConfig)
  {
    $this->anonymousAuthenticationConfig = $anonymousAuthenticationConfig;
  }
  /**
   * @return AnonymousAuthenticationConfig
   */
  public function getAnonymousAuthenticationConfig()
  {
    return $this->anonymousAuthenticationConfig;
  }
  /**
   * Configuration controlling RBAC group membership information.
   *
   * @param AuthenticatorGroupsConfig $authenticatorGroupsConfig
   */
  public function setAuthenticatorGroupsConfig(AuthenticatorGroupsConfig $authenticatorGroupsConfig)
  {
    $this->authenticatorGroupsConfig = $authenticatorGroupsConfig;
  }
  /**
   * @return AuthenticatorGroupsConfig
   */
  public function getAuthenticatorGroupsConfig()
  {
    return $this->authenticatorGroupsConfig;
  }
  /**
   * Autopilot configuration for the cluster.
   *
   * @param Autopilot $autopilot
   */
  public function setAutopilot(Autopilot $autopilot)
  {
    $this->autopilot = $autopilot;
  }
  /**
   * @return Autopilot
   */
  public function getAutopilot()
  {
    return $this->autopilot;
  }
  /**
   * Cluster-level autoscaling configuration.
   *
   * @param ClusterAutoscaling $autoscaling
   */
  public function setAutoscaling(ClusterAutoscaling $autoscaling)
  {
    $this->autoscaling = $autoscaling;
  }
  /**
   * @return ClusterAutoscaling
   */
  public function getAutoscaling()
  {
    return $this->autoscaling;
  }
  /**
   * Configuration for Binary Authorization.
   *
   * @param BinaryAuthorization $binaryAuthorization
   */
  public function setBinaryAuthorization(BinaryAuthorization $binaryAuthorization)
  {
    $this->binaryAuthorization = $binaryAuthorization;
  }
  /**
   * @return BinaryAuthorization
   */
  public function getBinaryAuthorization()
  {
    return $this->binaryAuthorization;
  }
  /**
   * The IP address range of the container pods in this cluster, in
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`). Leave blank to have one automatically
   * chosen or specify a `/14` block in `10.0.0.0/8`.
   *
   * @param string $clusterIpv4Cidr
   */
  public function setClusterIpv4Cidr($clusterIpv4Cidr)
  {
    $this->clusterIpv4Cidr = $clusterIpv4Cidr;
  }
  /**
   * @return string
   */
  public function getClusterIpv4Cidr()
  {
    return $this->clusterIpv4Cidr;
  }
  /**
   * Enable/Disable Compliance Posture features for the cluster.
   *
   * @param CompliancePostureConfig $compliancePostureConfig
   */
  public function setCompliancePostureConfig(CompliancePostureConfig $compliancePostureConfig)
  {
    $this->compliancePostureConfig = $compliancePostureConfig;
  }
  /**
   * @return CompliancePostureConfig
   */
  public function getCompliancePostureConfig()
  {
    return $this->compliancePostureConfig;
  }
  /**
   * Which conditions caused the current cluster state.
   *
   * @param StatusCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return StatusCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Configuration of Confidential Nodes. All the nodes in the cluster will be
   * Confidential VM once enabled.
   *
   * @param ConfidentialNodes $confidentialNodes
   */
  public function setConfidentialNodes(ConfidentialNodes $confidentialNodes)
  {
    $this->confidentialNodes = $confidentialNodes;
  }
  /**
   * @return ConfidentialNodes
   */
  public function getConfidentialNodes()
  {
    return $this->confidentialNodes;
  }
  /**
   * Configuration for all cluster's control plane endpoints.
   *
   * @param ControlPlaneEndpointsConfig $controlPlaneEndpointsConfig
   */
  public function setControlPlaneEndpointsConfig(ControlPlaneEndpointsConfig $controlPlaneEndpointsConfig)
  {
    $this->controlPlaneEndpointsConfig = $controlPlaneEndpointsConfig;
  }
  /**
   * @return ControlPlaneEndpointsConfig
   */
  public function getControlPlaneEndpointsConfig()
  {
    return $this->controlPlaneEndpointsConfig;
  }
  /**
   * Configuration for the fine-grained cost management feature.
   *
   * @param CostManagementConfig $costManagementConfig
   */
  public function setCostManagementConfig(CostManagementConfig $costManagementConfig)
  {
    $this->costManagementConfig = $costManagementConfig;
  }
  /**
   * @return CostManagementConfig
   */
  public function getCostManagementConfig()
  {
    return $this->costManagementConfig;
  }
  /**
   * Output only. The time the cluster was created, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
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
   * Output only. The current software version of the master endpoint.
   *
   * @param string $currentMasterVersion
   */
  public function setCurrentMasterVersion($currentMasterVersion)
  {
    $this->currentMasterVersion = $currentMasterVersion;
  }
  /**
   * @return string
   */
  public function getCurrentMasterVersion()
  {
    return $this->currentMasterVersion;
  }
  /**
   * Output only. The number of nodes currently in the cluster. Deprecated. Call
   * Kubernetes API directly to retrieve node information.
   *
   * @deprecated
   * @param int $currentNodeCount
   */
  public function setCurrentNodeCount($currentNodeCount)
  {
    $this->currentNodeCount = $currentNodeCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getCurrentNodeCount()
  {
    return $this->currentNodeCount;
  }
  /**
   * Output only. Deprecated, use
   * [NodePools.version](https://cloud.google.com/kubernetes-
   * engine/docs/reference/rest/v1/projects.locations.clusters.nodePools)
   * instead. The current version of the node software components. If they are
   * currently at multiple versions because they're in the process of being
   * upgraded, this reflects the minimum version of all nodes.
   *
   * @deprecated
   * @param string $currentNodeVersion
   */
  public function setCurrentNodeVersion($currentNodeVersion)
  {
    $this->currentNodeVersion = $currentNodeVersion;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getCurrentNodeVersion()
  {
    return $this->currentNodeVersion;
  }
  /**
   * Configuration of etcd encryption.
   *
   * @param DatabaseEncryption $databaseEncryption
   */
  public function setDatabaseEncryption(DatabaseEncryption $databaseEncryption)
  {
    $this->databaseEncryption = $databaseEncryption;
  }
  /**
   * @return DatabaseEncryption
   */
  public function getDatabaseEncryption()
  {
    return $this->databaseEncryption;
  }
  /**
   * The default constraint on the maximum number of pods that can be run
   * simultaneously on a node in the node pool of this cluster. Only honored if
   * cluster created with IP Alias support.
   *
   * @param MaxPodsConstraint $defaultMaxPodsConstraint
   */
  public function setDefaultMaxPodsConstraint(MaxPodsConstraint $defaultMaxPodsConstraint)
  {
    $this->defaultMaxPodsConstraint = $defaultMaxPodsConstraint;
  }
  /**
   * @return MaxPodsConstraint
   */
  public function getDefaultMaxPodsConstraint()
  {
    return $this->defaultMaxPodsConstraint;
  }
  /**
   * An optional description of this cluster.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Beta APIs Config
   *
   * @param K8sBetaAPIConfig $enableK8sBetaApis
   */
  public function setEnableK8sBetaApis(K8sBetaAPIConfig $enableK8sBetaApis)
  {
    $this->enableK8sBetaApis = $enableK8sBetaApis;
  }
  /**
   * @return K8sBetaAPIConfig
   */
  public function getEnableK8sBetaApis()
  {
    return $this->enableK8sBetaApis;
  }
  /**
   * Kubernetes alpha features are enabled on this cluster. This includes alpha
   * API groups (e.g. v1alpha1) and features that may not be production ready in
   * the kubernetes version of the master and nodes. The cluster has no SLA for
   * uptime and master/node upgrades are disabled. Alpha enabled clusters are
   * automatically deleted thirty days after creation.
   *
   * @param bool $enableKubernetesAlpha
   */
  public function setEnableKubernetesAlpha($enableKubernetesAlpha)
  {
    $this->enableKubernetesAlpha = $enableKubernetesAlpha;
  }
  /**
   * @return bool
   */
  public function getEnableKubernetesAlpha()
  {
    return $this->enableKubernetesAlpha;
  }
  /**
   * Enable the ability to use Cloud TPUs in this cluster. This field is
   * deprecated due to the deprecation of 2VM TPU. The end of life date for 2VM
   * TPU is 2025-04-25.
   *
   * @deprecated
   * @param bool $enableTpu
   */
  public function setEnableTpu($enableTpu)
  {
    $this->enableTpu = $enableTpu;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableTpu()
  {
    return $this->enableTpu;
  }
  /**
   * Output only. The IP address of this cluster's master endpoint. The endpoint
   * can be accessed from the internet at `https://username:password@endpoint/`.
   * See the `masterAuth` property of this resource for username and password
   * information.
   *
   * @param string $endpoint
   */
  public function setEndpoint($endpoint)
  {
    $this->endpoint = $endpoint;
  }
  /**
   * @return string
   */
  public function getEndpoint()
  {
    return $this->endpoint;
  }
  /**
   * GKE Enterprise Configuration. Deprecated: GKE Enterprise features are now
   * available without an Enterprise tier.
   *
   * @deprecated
   * @param EnterpriseConfig $enterpriseConfig
   */
  public function setEnterpriseConfig(EnterpriseConfig $enterpriseConfig)
  {
    $this->enterpriseConfig = $enterpriseConfig;
  }
  /**
   * @deprecated
   * @return EnterpriseConfig
   */
  public function getEnterpriseConfig()
  {
    return $this->enterpriseConfig;
  }
  /**
   * This checksum is computed by the server based on the value of cluster
   * fields, and may be sent on update requests to ensure the client has an up-
   * to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. The time the cluster will be automatically deleted in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Fleet information for the cluster.
   *
   * @param Fleet $fleet
   */
  public function setFleet(Fleet $fleet)
  {
    $this->fleet = $fleet;
  }
  /**
   * @return Fleet
   */
  public function getFleet()
  {
    return $this->fleet;
  }
  /**
   * Configuration for GKE auto upgrades.
   *
   * @param GkeAutoUpgradeConfig $gkeAutoUpgradeConfig
   */
  public function setGkeAutoUpgradeConfig(GkeAutoUpgradeConfig $gkeAutoUpgradeConfig)
  {
    $this->gkeAutoUpgradeConfig = $gkeAutoUpgradeConfig;
  }
  /**
   * @return GkeAutoUpgradeConfig
   */
  public function getGkeAutoUpgradeConfig()
  {
    return $this->gkeAutoUpgradeConfig;
  }
  /**
   * Output only. Unique id for the cluster.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Configuration for Identity Service component.
   *
   * @param IdentityServiceConfig $identityServiceConfig
   */
  public function setIdentityServiceConfig(IdentityServiceConfig $identityServiceConfig)
  {
    $this->identityServiceConfig = $identityServiceConfig;
  }
  /**
   * @return IdentityServiceConfig
   */
  public function getIdentityServiceConfig()
  {
    return $this->identityServiceConfig;
  }
  /**
   * The initial Kubernetes version for this cluster. Valid versions are those
   * found in validMasterVersions returned by getServerConfig. The version can
   * be upgraded over time; such upgrades are reflected in currentMasterVersion
   * and currentNodeVersion. Users may specify either explicit versions offered
   * by Kubernetes Engine or version aliases, which have the following behavior:
   * - "latest": picks the highest valid Kubernetes version - "1.X": picks the
   * highest valid patch+gke.N patch in the 1.X version - "1.X.Y": picks the
   * highest valid gke.N patch in the 1.X.Y version - "1.X.Y-gke.N": picks an
   * explicit Kubernetes version - "","-": picks the default Kubernetes version
   *
   * @param string $initialClusterVersion
   */
  public function setInitialClusterVersion($initialClusterVersion)
  {
    $this->initialClusterVersion = $initialClusterVersion;
  }
  /**
   * @return string
   */
  public function getInitialClusterVersion()
  {
    return $this->initialClusterVersion;
  }
  /**
   * The number of nodes to create in this cluster. You must ensure that your
   * Compute Engine [resource quota](https://cloud.google.com/compute/quotas) is
   * sufficient for this number of instances. You must also have available
   * firewall and routes quota. For requests, this field should only be used in
   * lieu of a "node_pool" object, since this configuration (along with the
   * "node_config") will be used to create a "NodePool" object with an auto-
   * generated name. Do not use this and a node_pool at the same time. This
   * field is deprecated, use node_pool.initial_node_count instead.
   *
   * @deprecated
   * @param int $initialNodeCount
   */
  public function setInitialNodeCount($initialNodeCount)
  {
    $this->initialNodeCount = $initialNodeCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getInitialNodeCount()
  {
    return $this->initialNodeCount;
  }
  /**
   * Output only. Deprecated. Use node_pools.instance_group_urls.
   *
   * @deprecated
   * @param string[] $instanceGroupUrls
   */
  public function setInstanceGroupUrls($instanceGroupUrls)
  {
    $this->instanceGroupUrls = $instanceGroupUrls;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getInstanceGroupUrls()
  {
    return $this->instanceGroupUrls;
  }
  /**
   * Configuration for cluster IP allocation.
   *
   * @param IPAllocationPolicy $ipAllocationPolicy
   */
  public function setIpAllocationPolicy(IPAllocationPolicy $ipAllocationPolicy)
  {
    $this->ipAllocationPolicy = $ipAllocationPolicy;
  }
  /**
   * @return IPAllocationPolicy
   */
  public function getIpAllocationPolicy()
  {
    return $this->ipAllocationPolicy;
  }
  /**
   * The fingerprint of the set of labels for this cluster.
   *
   * @param string $labelFingerprint
   */
  public function setLabelFingerprint($labelFingerprint)
  {
    $this->labelFingerprint = $labelFingerprint;
  }
  /**
   * @return string
   */
  public function getLabelFingerprint()
  {
    return $this->labelFingerprint;
  }
  /**
   * Configuration for the legacy ABAC authorization mode.
   *
   * @param LegacyAbac $legacyAbac
   */
  public function setLegacyAbac(LegacyAbac $legacyAbac)
  {
    $this->legacyAbac = $legacyAbac;
  }
  /**
   * @return LegacyAbac
   */
  public function getLegacyAbac()
  {
    return $this->legacyAbac;
  }
  /**
   * Output only. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/regions-zones/regions-
   * zones#available) or [region](https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones#available) in which the cluster resides.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster's nodes should be located. This field provides a default value if
   * [NodePool.Locations](https://cloud.google.com/kubernetes-engine/docs/refere
   * nce/rest/v1/projects.locations.clusters.nodePools#NodePool.FIELDS.locations
   * ) are not specified during node pool creation. Warning: changing cluster
   * locations will update the
   * [NodePool.Locations](https://cloud.google.com/kubernetes-engine/docs/refere
   * nce/rest/v1/projects.locations.clusters.nodePools#NodePool.FIELDS.locations
   * ) of all node pools and will result in nodes being added and/or removed.
   *
   * @param string[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return string[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * Logging configuration for the cluster.
   *
   * @param LoggingConfig $loggingConfig
   */
  public function setLoggingConfig(LoggingConfig $loggingConfig)
  {
    $this->loggingConfig = $loggingConfig;
  }
  /**
   * @return LoggingConfig
   */
  public function getLoggingConfig()
  {
    return $this->loggingConfig;
  }
  /**
   * The logging service the cluster should use to write logs. Currently
   * available options: * `logging.googleapis.com/kubernetes` - The Cloud
   * Logging service with a Kubernetes-native resource model *
   * `logging.googleapis.com` - The legacy Cloud Logging service (no longer
   * available as of GKE 1.15). * `none` - no logs will be exported from the
   * cluster. If left as an empty string,`logging.googleapis.com/kubernetes`
   * will be used for GKE 1.14+ or `logging.googleapis.com` for earlier
   * versions.
   *
   * @param string $loggingService
   */
  public function setLoggingService($loggingService)
  {
    $this->loggingService = $loggingService;
  }
  /**
   * @return string
   */
  public function getLoggingService()
  {
    return $this->loggingService;
  }
  /**
   * Configure the maintenance policy for this cluster.
   *
   * @param MaintenancePolicy $maintenancePolicy
   */
  public function setMaintenancePolicy(MaintenancePolicy $maintenancePolicy)
  {
    $this->maintenancePolicy = $maintenancePolicy;
  }
  /**
   * @return MaintenancePolicy
   */
  public function getMaintenancePolicy()
  {
    return $this->maintenancePolicy;
  }
  /**
   * Configuration for Managed OpenTelemetry pipeline.
   *
   * @param ManagedOpenTelemetryConfig $managedOpentelemetryConfig
   */
  public function setManagedOpentelemetryConfig(ManagedOpenTelemetryConfig $managedOpentelemetryConfig)
  {
    $this->managedOpentelemetryConfig = $managedOpentelemetryConfig;
  }
  /**
   * @return ManagedOpenTelemetryConfig
   */
  public function getManagedOpentelemetryConfig()
  {
    return $this->managedOpentelemetryConfig;
  }
  /**
   * The authentication information for accessing the master endpoint. If
   * unspecified, the defaults are used: For clusters before v1.12, if
   * master_auth is unspecified, `username` will be set to "admin", a random
   * password will be generated, and a client certificate will be issued.
   *
   * @param MasterAuth $masterAuth
   */
  public function setMasterAuth(MasterAuth $masterAuth)
  {
    $this->masterAuth = $masterAuth;
  }
  /**
   * @return MasterAuth
   */
  public function getMasterAuth()
  {
    return $this->masterAuth;
  }
  /**
   * The configuration options for master authorized networks feature.
   * Deprecated: Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.authorized_networks_config
   * instead.
   *
   * @deprecated
   * @param MasterAuthorizedNetworksConfig $masterAuthorizedNetworksConfig
   */
  public function setMasterAuthorizedNetworksConfig(MasterAuthorizedNetworksConfig $masterAuthorizedNetworksConfig)
  {
    $this->masterAuthorizedNetworksConfig = $masterAuthorizedNetworksConfig;
  }
  /**
   * @deprecated
   * @return MasterAuthorizedNetworksConfig
   */
  public function getMasterAuthorizedNetworksConfig()
  {
    return $this->masterAuthorizedNetworksConfig;
  }
  /**
   * Configuration for issuance of mTLS keys and certificates to Kubernetes
   * pods.
   *
   * @param MeshCertificates $meshCertificates
   */
  public function setMeshCertificates(MeshCertificates $meshCertificates)
  {
    $this->meshCertificates = $meshCertificates;
  }
  /**
   * @return MeshCertificates
   */
  public function getMeshCertificates()
  {
    return $this->meshCertificates;
  }
  /**
   * Monitoring configuration for the cluster.
   *
   * @param MonitoringConfig $monitoringConfig
   */
  public function setMonitoringConfig(MonitoringConfig $monitoringConfig)
  {
    $this->monitoringConfig = $monitoringConfig;
  }
  /**
   * @return MonitoringConfig
   */
  public function getMonitoringConfig()
  {
    return $this->monitoringConfig;
  }
  /**
   * The monitoring service the cluster should use to write metrics. Currently
   * available options: * `monitoring.googleapis.com/kubernetes` - The Cloud
   * Monitoring service with a Kubernetes-native resource model *
   * `monitoring.googleapis.com` - The legacy Cloud Monitoring service (no
   * longer available as of GKE 1.15). * `none` - No metrics will be exported
   * from the cluster. If left as an empty
   * string,`monitoring.googleapis.com/kubernetes` will be used for GKE 1.14+ or
   * `monitoring.googleapis.com` for earlier versions.
   *
   * @param string $monitoringService
   */
  public function setMonitoringService($monitoringService)
  {
    $this->monitoringService = $monitoringService;
  }
  /**
   * @return string
   */
  public function getMonitoringService()
  {
    return $this->monitoringService;
  }
  /**
   * The name of this cluster. The name must be unique within this project and
   * location (e.g. zone or region), and can be up to 40 characters with the
   * following restrictions: * Lowercase letters, numbers, and hyphens only. *
   * Must start with a letter. * Must end with a number or a letter.
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
   * The name of the Google Compute Engine
   * [network](https://cloud.google.com/compute/docs/networks-and-
   * firewalls#networks) to which the cluster is connected. If left unspecified,
   * the `default` network will be used.
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
   * Configuration for cluster networking.
   *
   * @param NetworkConfig $networkConfig
   */
  public function setNetworkConfig(NetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return NetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Configuration options for the NetworkPolicy feature.
   *
   * @param NetworkPolicy $networkPolicy
   */
  public function setNetworkPolicy(NetworkPolicy $networkPolicy)
  {
    $this->networkPolicy = $networkPolicy;
  }
  /**
   * @return NetworkPolicy
   */
  public function getNetworkPolicy()
  {
    return $this->networkPolicy;
  }
  /**
   * Parameters used in creating the cluster's nodes. For requests, this field
   * should only be used in lieu of a "node_pool" object, since this
   * configuration (along with the "initial_node_count") will be used to create
   * a "NodePool" object with an auto-generated name. Do not use this and a
   * node_pool at the same time. For responses, this field will be populated
   * with the node configuration of the first node pool. (For configuration of
   * each node pool, see `node_pool.config`) If unspecified, the defaults are
   * used. This field is deprecated, use node_pool.config instead.
   *
   * @deprecated
   * @param NodeConfig $nodeConfig
   */
  public function setNodeConfig(NodeConfig $nodeConfig)
  {
    $this->nodeConfig = $nodeConfig;
  }
  /**
   * @deprecated
   * @return NodeConfig
   */
  public function getNodeConfig()
  {
    return $this->nodeConfig;
  }
  /**
   * Output only. The size of the address space on each node for hosting
   * containers. This is provisioned from within the `container_ipv4_cidr`
   * range. This field will only be set when cluster is in route-based network
   * mode.
   *
   * @param int $nodeIpv4CidrSize
   */
  public function setNodeIpv4CidrSize($nodeIpv4CidrSize)
  {
    $this->nodeIpv4CidrSize = $nodeIpv4CidrSize;
  }
  /**
   * @return int
   */
  public function getNodeIpv4CidrSize()
  {
    return $this->nodeIpv4CidrSize;
  }
  /**
   * Node pool configs that apply to all auto-provisioned node pools in
   * autopilot clusters and node auto-provisioning enabled clusters.
   *
   * @param NodePoolAutoConfig $nodePoolAutoConfig
   */
  public function setNodePoolAutoConfig(NodePoolAutoConfig $nodePoolAutoConfig)
  {
    $this->nodePoolAutoConfig = $nodePoolAutoConfig;
  }
  /**
   * @return NodePoolAutoConfig
   */
  public function getNodePoolAutoConfig()
  {
    return $this->nodePoolAutoConfig;
  }
  /**
   * Default NodePool settings for the entire cluster. These settings are
   * overridden if specified on the specific NodePool object.
   *
   * @param NodePoolDefaults $nodePoolDefaults
   */
  public function setNodePoolDefaults(NodePoolDefaults $nodePoolDefaults)
  {
    $this->nodePoolDefaults = $nodePoolDefaults;
  }
  /**
   * @return NodePoolDefaults
   */
  public function getNodePoolDefaults()
  {
    return $this->nodePoolDefaults;
  }
  /**
   * The node pools associated with this cluster. This field should not be set
   * if "node_config" or "initial_node_count" are specified.
   *
   * @param NodePool[] $nodePools
   */
  public function setNodePools($nodePools)
  {
    $this->nodePools = $nodePools;
  }
  /**
   * @return NodePool[]
   */
  public function getNodePools()
  {
    return $this->nodePools;
  }
  /**
   * Notification configuration of the cluster.
   *
   * @param NotificationConfig $notificationConfig
   */
  public function setNotificationConfig(NotificationConfig $notificationConfig)
  {
    $this->notificationConfig = $notificationConfig;
  }
  /**
   * @return NotificationConfig
   */
  public function getNotificationConfig()
  {
    return $this->notificationConfig;
  }
  /**
   * The configuration of the parent product of the cluster. This field is used
   * by Google internal products that are built on top of the GKE cluster and
   * take the ownership of the cluster.
   *
   * @param ParentProductConfig $parentProductConfig
   */
  public function setParentProductConfig(ParentProductConfig $parentProductConfig)
  {
    $this->parentProductConfig = $parentProductConfig;
  }
  /**
   * @return ParentProductConfig
   */
  public function getParentProductConfig()
  {
    return $this->parentProductConfig;
  }
  /**
   * The config for pod autoscaling.
   *
   * @param PodAutoscaling $podAutoscaling
   */
  public function setPodAutoscaling(PodAutoscaling $podAutoscaling)
  {
    $this->podAutoscaling = $podAutoscaling;
  }
  /**
   * @return PodAutoscaling
   */
  public function getPodAutoscaling()
  {
    return $this->podAutoscaling;
  }
  /**
   * Configuration for private cluster.
   *
   * @param PrivateClusterConfig $privateClusterConfig
   */
  public function setPrivateClusterConfig(PrivateClusterConfig $privateClusterConfig)
  {
    $this->privateClusterConfig = $privateClusterConfig;
  }
  /**
   * @return PrivateClusterConfig
   */
  public function getPrivateClusterConfig()
  {
    return $this->privateClusterConfig;
  }
  /**
   * RBACBindingConfig allows user to restrict ClusterRoleBindings an
   * RoleBindings that can be created.
   *
   * @param RBACBindingConfig $rbacBindingConfig
   */
  public function setRbacBindingConfig(RBACBindingConfig $rbacBindingConfig)
  {
    $this->rbacBindingConfig = $rbacBindingConfig;
  }
  /**
   * @return RBACBindingConfig
   */
  public function getRbacBindingConfig()
  {
    return $this->rbacBindingConfig;
  }
  /**
   * Release channel configuration. If left unspecified on cluster creation and
   * a version is specified, the cluster is enrolled in the most mature release
   * channel where the version is available (first checking STABLE, then
   * REGULAR, and finally RAPID). Otherwise, if no release channel configuration
   * and no version is specified, the cluster is enrolled in the REGULAR channel
   * with its default version.
   *
   * @param ReleaseChannel $releaseChannel
   */
  public function setReleaseChannel(ReleaseChannel $releaseChannel)
  {
    $this->releaseChannel = $releaseChannel;
  }
  /**
   * @return ReleaseChannel
   */
  public function getReleaseChannel()
  {
    return $this->releaseChannel;
  }
  /**
   * The resource labels for the cluster to use to annotate any related Google
   * Compute Engine resources.
   *
   * @param string[] $resourceLabels
   */
  public function setResourceLabels($resourceLabels)
  {
    $this->resourceLabels = $resourceLabels;
  }
  /**
   * @return string[]
   */
  public function getResourceLabels()
  {
    return $this->resourceLabels;
  }
  /**
   * Configuration for exporting resource usages. Resource usage export is
   * disabled when this config is unspecified.
   *
   * @param ResourceUsageExportConfig $resourceUsageExportConfig
   */
  public function setResourceUsageExportConfig(ResourceUsageExportConfig $resourceUsageExportConfig)
  {
    $this->resourceUsageExportConfig = $resourceUsageExportConfig;
  }
  /**
   * @return ResourceUsageExportConfig
   */
  public function getResourceUsageExportConfig()
  {
    return $this->resourceUsageExportConfig;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Secret CSI driver configuration.
   *
   * @param SecretManagerConfig $secretManagerConfig
   */
  public function setSecretManagerConfig(SecretManagerConfig $secretManagerConfig)
  {
    $this->secretManagerConfig = $secretManagerConfig;
  }
  /**
   * @return SecretManagerConfig
   */
  public function getSecretManagerConfig()
  {
    return $this->secretManagerConfig;
  }
  /**
   * Enable/Disable Security Posture API features for the cluster.
   *
   * @param SecurityPostureConfig $securityPostureConfig
   */
  public function setSecurityPostureConfig(SecurityPostureConfig $securityPostureConfig)
  {
    $this->securityPostureConfig = $securityPostureConfig;
  }
  /**
   * @return SecurityPostureConfig
   */
  public function getSecurityPostureConfig()
  {
    return $this->securityPostureConfig;
  }
  /**
   * Output only. Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. The IP address range of the Kubernetes services in this
   * cluster, in [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-
   * Domain_Routing) notation (e.g. `1.2.3.4/29`). Service addresses are
   * typically put in the last `/16` from the container CIDR.
   *
   * @param string $servicesIpv4Cidr
   */
  public function setServicesIpv4Cidr($servicesIpv4Cidr)
  {
    $this->servicesIpv4Cidr = $servicesIpv4Cidr;
  }
  /**
   * @return string
   */
  public function getServicesIpv4Cidr()
  {
    return $this->servicesIpv4Cidr;
  }
  /**
   * Shielded Nodes configuration.
   *
   * @param ShieldedNodes $shieldedNodes
   */
  public function setShieldedNodes(ShieldedNodes $shieldedNodes)
  {
    $this->shieldedNodes = $shieldedNodes;
  }
  /**
   * @return ShieldedNodes
   */
  public function getShieldedNodes()
  {
    return $this->shieldedNodes;
  }
  /**
   * Output only. The current status of this cluster.
   *
   * Accepted values: STATUS_UNSPECIFIED, PROVISIONING, RUNNING, RECONCILING,
   * STOPPING, ERROR, DEGRADED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. Deprecated. Use conditions instead. Additional information
   * about the current status of this cluster, if available.
   *
   * @deprecated
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * The name of the Google Compute Engine
   * [subnetwork](https://cloud.google.com/compute/docs/subnetworks) to which
   * the cluster is connected.
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
  /**
   * Output only. The IP address range of the Cloud TPUs in this cluster, in
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `1.2.3.4/29`). This field is deprecated due to the
   * deprecation of 2VM TPU. The end of life date for 2VM TPU is 2025-04-25.
   *
   * @deprecated
   * @param string $tpuIpv4CidrBlock
   */
  public function setTpuIpv4CidrBlock($tpuIpv4CidrBlock)
  {
    $this->tpuIpv4CidrBlock = $tpuIpv4CidrBlock;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTpuIpv4CidrBlock()
  {
    return $this->tpuIpv4CidrBlock;
  }
  /**
   * The Custom keys configuration for the cluster.
   *
   * @param UserManagedKeysConfig $userManagedKeysConfig
   */
  public function setUserManagedKeysConfig(UserManagedKeysConfig $userManagedKeysConfig)
  {
    $this->userManagedKeysConfig = $userManagedKeysConfig;
  }
  /**
   * @return UserManagedKeysConfig
   */
  public function getUserManagedKeysConfig()
  {
    return $this->userManagedKeysConfig;
  }
  /**
   * Cluster-level Vertical Pod Autoscaling configuration.
   *
   * @param VerticalPodAutoscaling $verticalPodAutoscaling
   */
  public function setVerticalPodAutoscaling(VerticalPodAutoscaling $verticalPodAutoscaling)
  {
    $this->verticalPodAutoscaling = $verticalPodAutoscaling;
  }
  /**
   * @return VerticalPodAutoscaling
   */
  public function getVerticalPodAutoscaling()
  {
    return $this->verticalPodAutoscaling;
  }
  /**
   * Configuration for the use of Kubernetes Service Accounts in IAM policies.
   *
   * @param WorkloadIdentityConfig $workloadIdentityConfig
   */
  public function setWorkloadIdentityConfig(WorkloadIdentityConfig $workloadIdentityConfig)
  {
    $this->workloadIdentityConfig = $workloadIdentityConfig;
  }
  /**
   * @return WorkloadIdentityConfig
   */
  public function getWorkloadIdentityConfig()
  {
    return $this->workloadIdentityConfig;
  }
  /**
   * Output only. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field is deprecated, use location instead.
   *
   * @deprecated
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cluster::class, 'Google_Service_Container_Cluster');
