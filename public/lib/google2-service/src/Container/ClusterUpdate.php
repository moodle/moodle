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

class ClusterUpdate extends \Google\Collection
{
  /**
   * Default value.
   */
  public const DESIRED_DATAPATH_PROVIDER_DATAPATH_PROVIDER_UNSPECIFIED = 'DATAPATH_PROVIDER_UNSPECIFIED';
  /**
   * Use the IPTables implementation based on kube-proxy.
   */
  public const DESIRED_DATAPATH_PROVIDER_LEGACY_DATAPATH = 'LEGACY_DATAPATH';
  /**
   * Use the eBPF based GKE Dataplane V2 with additional features. See the [GKE
   * Dataplane V2 documentation](https://cloud.google.com/kubernetes-
   * engine/docs/how-to/dataplane-v2) for more.
   */
  public const DESIRED_DATAPATH_PROVIDER_ADVANCED_DATAPATH = 'ADVANCED_DATAPATH';
  /**
   * Unspecified, will be inferred as default -
   * IN_TRANSIT_ENCRYPTION_UNSPECIFIED.
   */
  public const DESIRED_IN_TRANSIT_ENCRYPTION_CONFIG_IN_TRANSIT_ENCRYPTION_CONFIG_UNSPECIFIED = 'IN_TRANSIT_ENCRYPTION_CONFIG_UNSPECIFIED';
  /**
   * In-transit encryption is disabled.
   */
  public const DESIRED_IN_TRANSIT_ENCRYPTION_CONFIG_IN_TRANSIT_ENCRYPTION_DISABLED = 'IN_TRANSIT_ENCRYPTION_DISABLED';
  /**
   * Data in-transit is encrypted using inter-node transparent encryption.
   */
  public const DESIRED_IN_TRANSIT_ENCRYPTION_CONFIG_IN_TRANSIT_ENCRYPTION_INTER_NODE_TRANSPARENT = 'IN_TRANSIT_ENCRYPTION_INTER_NODE_TRANSPARENT';
  /**
   * Default value. Same as DISABLED
   */
  public const DESIRED_PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED = 'PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED';
  /**
   * No private access to or from Google Services
   */
  public const DESIRED_PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_DISABLED = 'PRIVATE_IPV6_GOOGLE_ACCESS_DISABLED';
  /**
   * Enables private IPv6 access to Google Services from GKE
   */
  public const DESIRED_PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_TO_GOOGLE = 'PRIVATE_IPV6_GOOGLE_ACCESS_TO_GOOGLE';
  /**
   * Enables private IPv6 access to and from Google Services
   */
  public const DESIRED_PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_BIDIRECTIONAL = 'PRIVATE_IPV6_GOOGLE_ACCESS_BIDIRECTIONAL';
  /**
   * Default value, will be defaulted as IPV4 only
   */
  public const DESIRED_STACK_TYPE_STACK_TYPE_UNSPECIFIED = 'STACK_TYPE_UNSPECIFIED';
  /**
   * Cluster is IPV4 only
   */
  public const DESIRED_STACK_TYPE_IPV4 = 'IPV4';
  /**
   * Cluster can use both IPv4 and IPv6
   */
  public const DESIRED_STACK_TYPE_IPV4_IPV6 = 'IPV4_IPV6';
  protected $collection_key = 'desiredLocations';
  protected $additionalPodRangesConfigType = AdditionalPodRangesConfig::class;
  protected $additionalPodRangesConfigDataType = '';
  protected $desiredAdditionalIpRangesConfigType = DesiredAdditionalIPRangesConfig::class;
  protected $desiredAdditionalIpRangesConfigDataType = '';
  protected $desiredAddonsConfigType = AddonsConfig::class;
  protected $desiredAddonsConfigDataType = '';
  protected $desiredAnonymousAuthenticationConfigType = AnonymousAuthenticationConfig::class;
  protected $desiredAnonymousAuthenticationConfigDataType = '';
  protected $desiredAuthenticatorGroupsConfigType = AuthenticatorGroupsConfig::class;
  protected $desiredAuthenticatorGroupsConfigDataType = '';
  protected $desiredAutoIpamConfigType = AutoIpamConfig::class;
  protected $desiredAutoIpamConfigDataType = '';
  protected $desiredAutopilotWorkloadPolicyConfigType = WorkloadPolicyConfig::class;
  protected $desiredAutopilotWorkloadPolicyConfigDataType = '';
  protected $desiredBinaryAuthorizationType = BinaryAuthorization::class;
  protected $desiredBinaryAuthorizationDataType = '';
  protected $desiredClusterAutoscalingType = ClusterAutoscaling::class;
  protected $desiredClusterAutoscalingDataType = '';
  protected $desiredCompliancePostureConfigType = CompliancePostureConfig::class;
  protected $desiredCompliancePostureConfigDataType = '';
  protected $desiredContainerdConfigType = ContainerdConfig::class;
  protected $desiredContainerdConfigDataType = '';
  protected $desiredControlPlaneEndpointsConfigType = ControlPlaneEndpointsConfig::class;
  protected $desiredControlPlaneEndpointsConfigDataType = '';
  protected $desiredCostManagementConfigType = CostManagementConfig::class;
  protected $desiredCostManagementConfigDataType = '';
  protected $desiredDatabaseEncryptionType = DatabaseEncryption::class;
  protected $desiredDatabaseEncryptionDataType = '';
  /**
   * The desired datapath provider for the cluster.
   *
   * @var string
   */
  public $desiredDatapathProvider;
  /**
   * Override the default setting of whether future created nodes have private
   * IP addresses only, namely NetworkConfig.default_enable_private_nodes
   *
   * @var bool
   */
  public $desiredDefaultEnablePrivateNodes;
  protected $desiredDefaultSnatStatusType = DefaultSnatStatus::class;
  protected $desiredDefaultSnatStatusDataType = '';
  /**
   * Enable/Disable L4 LB VPC firewall reconciliation for the cluster.
   *
   * @var bool
   */
  public $desiredDisableL4LbFirewallReconciliation;
  protected $desiredDnsConfigType = DNSConfig::class;
  protected $desiredDnsConfigDataType = '';
  /**
   * Enable/Disable Cilium Clusterwide Network Policy for the cluster.
   *
   * @var bool
   */
  public $desiredEnableCiliumClusterwideNetworkPolicy;
  /**
   * Enable/Disable FQDN Network Policy for the cluster.
   *
   * @var bool
   */
  public $desiredEnableFqdnNetworkPolicy;
  /**
   * Enable/Disable Multi-Networking for the cluster
   *
   * @var bool
   */
  public $desiredEnableMultiNetworking;
  /**
   * Enable/Disable private endpoint for the cluster's master. Deprecated: Use d
   * esired_control_plane_endpoints_config.ip_endpoints_config.enable_public_end
   * point instead. Note that the value of enable_public_endpoint is reversed:
   * if enable_private_endpoint is false, then enable_public_endpoint will be
   * true.
   *
   * @deprecated
   * @var bool
   */
  public $desiredEnablePrivateEndpoint;
  protected $desiredEnterpriseConfigType = DesiredEnterpriseConfig::class;
  protected $desiredEnterpriseConfigDataType = '';
  protected $desiredFleetType = Fleet::class;
  protected $desiredFleetDataType = '';
  protected $desiredGatewayApiConfigType = GatewayAPIConfig::class;
  protected $desiredGatewayApiConfigDataType = '';
  protected $desiredGcfsConfigType = GcfsConfig::class;
  protected $desiredGcfsConfigDataType = '';
  protected $desiredIdentityServiceConfigType = IdentityServiceConfig::class;
  protected $desiredIdentityServiceConfigDataType = '';
  /**
   * The desired image type for the node pool. NOTE: Set the "desired_node_pool"
   * field as well.
   *
   * @var string
   */
  public $desiredImageType;
  /**
   * Specify the details of in-transit encryption.
   *
   * @var string
   */
  public $desiredInTransitEncryptionConfig;
  protected $desiredIntraNodeVisibilityConfigType = IntraNodeVisibilityConfig::class;
  protected $desiredIntraNodeVisibilityConfigDataType = '';
  protected $desiredK8sBetaApisType = K8sBetaAPIConfig::class;
  protected $desiredK8sBetaApisDataType = '';
  protected $desiredL4ilbSubsettingConfigType = ILBSubsettingConfig::class;
  protected $desiredL4ilbSubsettingConfigDataType = '';
  /**
   * The desired list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster's nodes should be located. This list must always include the
   * cluster's primary zone. Warning: changing cluster locations will update the
   * locations of all node pools and will result in nodes being added and/or
   * removed.
   *
   * @var string[]
   */
  public $desiredLocations;
  protected $desiredLoggingConfigType = LoggingConfig::class;
  protected $desiredLoggingConfigDataType = '';
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
  public $desiredLoggingService;
  protected $desiredManagedOpentelemetryConfigType = ManagedOpenTelemetryConfig::class;
  protected $desiredManagedOpentelemetryConfigDataType = '';
  protected $desiredMasterAuthorizedNetworksConfigType = MasterAuthorizedNetworksConfig::class;
  protected $desiredMasterAuthorizedNetworksConfigDataType = '';
  /**
   * The Kubernetes version to change the master to. Users may specify either
   * explicit versions offered by Kubernetes Engine or version aliases, which
   * have the following behavior: - "latest": picks the highest valid Kubernetes
   * version - "1.X": picks the highest valid patch+gke.N patch in the 1.X
   * version - "1.X.Y": picks the highest valid gke.N patch in the 1.X.Y version
   * - "1.X.Y-gke.N": picks an explicit Kubernetes version - "-": picks the
   * default Kubernetes version
   *
   * @var string
   */
  public $desiredMasterVersion;
  protected $desiredMeshCertificatesType = MeshCertificates::class;
  protected $desiredMeshCertificatesDataType = '';
  protected $desiredMonitoringConfigType = MonitoringConfig::class;
  protected $desiredMonitoringConfigDataType = '';
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
  public $desiredMonitoringService;
  protected $desiredNetworkPerformanceConfigType = ClusterNetworkPerformanceConfig::class;
  protected $desiredNetworkPerformanceConfigDataType = '';
  protected $desiredNetworkTierConfigType = NetworkTierConfig::class;
  protected $desiredNetworkTierConfigDataType = '';
  protected $desiredNodeKubeletConfigType = NodeKubeletConfig::class;
  protected $desiredNodeKubeletConfigDataType = '';
  protected $desiredNodePoolAutoConfigKubeletConfigType = NodeKubeletConfig::class;
  protected $desiredNodePoolAutoConfigKubeletConfigDataType = '';
  protected $desiredNodePoolAutoConfigLinuxNodeConfigType = LinuxNodeConfig::class;
  protected $desiredNodePoolAutoConfigLinuxNodeConfigDataType = '';
  protected $desiredNodePoolAutoConfigNetworkTagsType = NetworkTags::class;
  protected $desiredNodePoolAutoConfigNetworkTagsDataType = '';
  protected $desiredNodePoolAutoConfigResourceManagerTagsType = ResourceManagerTags::class;
  protected $desiredNodePoolAutoConfigResourceManagerTagsDataType = '';
  protected $desiredNodePoolAutoscalingType = NodePoolAutoscaling::class;
  protected $desiredNodePoolAutoscalingDataType = '';
  /**
   * The node pool to be upgraded. This field is mandatory if
   * "desired_node_version", "desired_image_family" or
   * "desired_node_pool_autoscaling" is specified and there is more than one
   * node pool on the cluster.
   *
   * @var string
   */
  public $desiredNodePoolId;
  protected $desiredNodePoolLoggingConfigType = NodePoolLoggingConfig::class;
  protected $desiredNodePoolLoggingConfigDataType = '';
  /**
   * The Kubernetes version to change the nodes to (typically an upgrade). Users
   * may specify either explicit versions offered by Kubernetes Engine or
   * version aliases, which have the following behavior: - "latest": picks the
   * highest valid Kubernetes version - "1.X": picks the highest valid
   * patch+gke.N patch in the 1.X version - "1.X.Y": picks the highest valid
   * gke.N patch in the 1.X.Y version - "1.X.Y-gke.N": picks an explicit
   * Kubernetes version - "-": picks the Kubernetes master version
   *
   * @var string
   */
  public $desiredNodeVersion;
  protected $desiredNotificationConfigType = NotificationConfig::class;
  protected $desiredNotificationConfigDataType = '';
  protected $desiredParentProductConfigType = ParentProductConfig::class;
  protected $desiredParentProductConfigDataType = '';
  protected $desiredPodAutoscalingType = PodAutoscaling::class;
  protected $desiredPodAutoscalingDataType = '';
  protected $desiredPrivateClusterConfigType = PrivateClusterConfig::class;
  protected $desiredPrivateClusterConfigDataType = '';
  /**
   * The desired state of IPv6 connectivity to Google Services.
   *
   * @var string
   */
  public $desiredPrivateIpv6GoogleAccess;
  protected $desiredPrivilegedAdmissionConfigType = PrivilegedAdmissionConfig::class;
  protected $desiredPrivilegedAdmissionConfigDataType = '';
  protected $desiredRbacBindingConfigType = RBACBindingConfig::class;
  protected $desiredRbacBindingConfigDataType = '';
  protected $desiredReleaseChannelType = ReleaseChannel::class;
  protected $desiredReleaseChannelDataType = '';
  protected $desiredResourceUsageExportConfigType = ResourceUsageExportConfig::class;
  protected $desiredResourceUsageExportConfigDataType = '';
  protected $desiredSecretManagerConfigType = SecretManagerConfig::class;
  protected $desiredSecretManagerConfigDataType = '';
  protected $desiredSecurityPostureConfigType = SecurityPostureConfig::class;
  protected $desiredSecurityPostureConfigDataType = '';
  protected $desiredServiceExternalIpsConfigType = ServiceExternalIPsConfig::class;
  protected $desiredServiceExternalIpsConfigDataType = '';
  protected $desiredShieldedNodesType = ShieldedNodes::class;
  protected $desiredShieldedNodesDataType = '';
  /**
   * The desired stack type of the cluster. If a stack type is provided and does
   * not match the current stack type of the cluster, update will attempt to
   * change the stack type to the new type.
   *
   * @var string
   */
  public $desiredStackType;
  protected $desiredUserManagedKeysConfigType = UserManagedKeysConfig::class;
  protected $desiredUserManagedKeysConfigDataType = '';
  protected $desiredVerticalPodAutoscalingType = VerticalPodAutoscaling::class;
  protected $desiredVerticalPodAutoscalingDataType = '';
  protected $desiredWorkloadIdentityConfigType = WorkloadIdentityConfig::class;
  protected $desiredWorkloadIdentityConfigDataType = '';
  protected $enableK8sBetaApisType = K8sBetaAPIConfig::class;
  protected $enableK8sBetaApisDataType = '';
  /**
   * The current etag of the cluster. If an etag is provided and does not match
   * the current etag of the cluster, update will be blocked and an ABORTED
   * error will be returned.
   *
   * @var string
   */
  public $etag;
  protected $gkeAutoUpgradeConfigType = GkeAutoUpgradeConfig::class;
  protected $gkeAutoUpgradeConfigDataType = '';
  protected $removedAdditionalPodRangesConfigType = AdditionalPodRangesConfig::class;
  protected $removedAdditionalPodRangesConfigDataType = '';
  protected $userManagedKeysConfigType = UserManagedKeysConfig::class;
  protected $userManagedKeysConfigDataType = '';

  /**
   * The additional pod ranges to be added to the cluster. These pod ranges can
   * be used by node pools to allocate pod IPs.
   *
   * @param AdditionalPodRangesConfig $additionalPodRangesConfig
   */
  public function setAdditionalPodRangesConfig(AdditionalPodRangesConfig $additionalPodRangesConfig)
  {
    $this->additionalPodRangesConfig = $additionalPodRangesConfig;
  }
  /**
   * @return AdditionalPodRangesConfig
   */
  public function getAdditionalPodRangesConfig()
  {
    return $this->additionalPodRangesConfig;
  }
  /**
   * The desired config for additional subnetworks attached to the cluster.
   *
   * @param DesiredAdditionalIPRangesConfig $desiredAdditionalIpRangesConfig
   */
  public function setDesiredAdditionalIpRangesConfig(DesiredAdditionalIPRangesConfig $desiredAdditionalIpRangesConfig)
  {
    $this->desiredAdditionalIpRangesConfig = $desiredAdditionalIpRangesConfig;
  }
  /**
   * @return DesiredAdditionalIPRangesConfig
   */
  public function getDesiredAdditionalIpRangesConfig()
  {
    return $this->desiredAdditionalIpRangesConfig;
  }
  /**
   * Configurations for the various addons available to run in the cluster.
   *
   * @param AddonsConfig $desiredAddonsConfig
   */
  public function setDesiredAddonsConfig(AddonsConfig $desiredAddonsConfig)
  {
    $this->desiredAddonsConfig = $desiredAddonsConfig;
  }
  /**
   * @return AddonsConfig
   */
  public function getDesiredAddonsConfig()
  {
    return $this->desiredAddonsConfig;
  }
  /**
   * Configuration for limiting anonymous access to all endpoints except the
   * health checks.
   *
   * @param AnonymousAuthenticationConfig $desiredAnonymousAuthenticationConfig
   */
  public function setDesiredAnonymousAuthenticationConfig(AnonymousAuthenticationConfig $desiredAnonymousAuthenticationConfig)
  {
    $this->desiredAnonymousAuthenticationConfig = $desiredAnonymousAuthenticationConfig;
  }
  /**
   * @return AnonymousAuthenticationConfig
   */
  public function getDesiredAnonymousAuthenticationConfig()
  {
    return $this->desiredAnonymousAuthenticationConfig;
  }
  /**
   * The desired authenticator groups config for the cluster.
   *
   * @param AuthenticatorGroupsConfig $desiredAuthenticatorGroupsConfig
   */
  public function setDesiredAuthenticatorGroupsConfig(AuthenticatorGroupsConfig $desiredAuthenticatorGroupsConfig)
  {
    $this->desiredAuthenticatorGroupsConfig = $desiredAuthenticatorGroupsConfig;
  }
  /**
   * @return AuthenticatorGroupsConfig
   */
  public function getDesiredAuthenticatorGroupsConfig()
  {
    return $this->desiredAuthenticatorGroupsConfig;
  }
  /**
   * AutoIpamConfig contains all information related to Auto IPAM
   *
   * @param AutoIpamConfig $desiredAutoIpamConfig
   */
  public function setDesiredAutoIpamConfig(AutoIpamConfig $desiredAutoIpamConfig)
  {
    $this->desiredAutoIpamConfig = $desiredAutoIpamConfig;
  }
  /**
   * @return AutoIpamConfig
   */
  public function getDesiredAutoIpamConfig()
  {
    return $this->desiredAutoIpamConfig;
  }
  /**
   * WorkloadPolicyConfig is the configuration related to GCW workload policy
   *
   * @param WorkloadPolicyConfig $desiredAutopilotWorkloadPolicyConfig
   */
  public function setDesiredAutopilotWorkloadPolicyConfig(WorkloadPolicyConfig $desiredAutopilotWorkloadPolicyConfig)
  {
    $this->desiredAutopilotWorkloadPolicyConfig = $desiredAutopilotWorkloadPolicyConfig;
  }
  /**
   * @return WorkloadPolicyConfig
   */
  public function getDesiredAutopilotWorkloadPolicyConfig()
  {
    return $this->desiredAutopilotWorkloadPolicyConfig;
  }
  /**
   * The desired configuration options for the Binary Authorization feature.
   *
   * @param BinaryAuthorization $desiredBinaryAuthorization
   */
  public function setDesiredBinaryAuthorization(BinaryAuthorization $desiredBinaryAuthorization)
  {
    $this->desiredBinaryAuthorization = $desiredBinaryAuthorization;
  }
  /**
   * @return BinaryAuthorization
   */
  public function getDesiredBinaryAuthorization()
  {
    return $this->desiredBinaryAuthorization;
  }
  /**
   * Cluster-level autoscaling configuration.
   *
   * @param ClusterAutoscaling $desiredClusterAutoscaling
   */
  public function setDesiredClusterAutoscaling(ClusterAutoscaling $desiredClusterAutoscaling)
  {
    $this->desiredClusterAutoscaling = $desiredClusterAutoscaling;
  }
  /**
   * @return ClusterAutoscaling
   */
  public function getDesiredClusterAutoscaling()
  {
    return $this->desiredClusterAutoscaling;
  }
  /**
   * Enable/Disable Compliance Posture features for the cluster.
   *
   * @param CompliancePostureConfig $desiredCompliancePostureConfig
   */
  public function setDesiredCompliancePostureConfig(CompliancePostureConfig $desiredCompliancePostureConfig)
  {
    $this->desiredCompliancePostureConfig = $desiredCompliancePostureConfig;
  }
  /**
   * @return CompliancePostureConfig
   */
  public function getDesiredCompliancePostureConfig()
  {
    return $this->desiredCompliancePostureConfig;
  }
  /**
   * The desired containerd config for the cluster.
   *
   * @param ContainerdConfig $desiredContainerdConfig
   */
  public function setDesiredContainerdConfig(ContainerdConfig $desiredContainerdConfig)
  {
    $this->desiredContainerdConfig = $desiredContainerdConfig;
  }
  /**
   * @return ContainerdConfig
   */
  public function getDesiredContainerdConfig()
  {
    return $this->desiredContainerdConfig;
  }
  /**
   * Control plane endpoints configuration.
   *
   * @param ControlPlaneEndpointsConfig $desiredControlPlaneEndpointsConfig
   */
  public function setDesiredControlPlaneEndpointsConfig(ControlPlaneEndpointsConfig $desiredControlPlaneEndpointsConfig)
  {
    $this->desiredControlPlaneEndpointsConfig = $desiredControlPlaneEndpointsConfig;
  }
  /**
   * @return ControlPlaneEndpointsConfig
   */
  public function getDesiredControlPlaneEndpointsConfig()
  {
    return $this->desiredControlPlaneEndpointsConfig;
  }
  /**
   * The desired configuration for the fine-grained cost management feature.
   *
   * @param CostManagementConfig $desiredCostManagementConfig
   */
  public function setDesiredCostManagementConfig(CostManagementConfig $desiredCostManagementConfig)
  {
    $this->desiredCostManagementConfig = $desiredCostManagementConfig;
  }
  /**
   * @return CostManagementConfig
   */
  public function getDesiredCostManagementConfig()
  {
    return $this->desiredCostManagementConfig;
  }
  /**
   * Configuration of etcd encryption.
   *
   * @param DatabaseEncryption $desiredDatabaseEncryption
   */
  public function setDesiredDatabaseEncryption(DatabaseEncryption $desiredDatabaseEncryption)
  {
    $this->desiredDatabaseEncryption = $desiredDatabaseEncryption;
  }
  /**
   * @return DatabaseEncryption
   */
  public function getDesiredDatabaseEncryption()
  {
    return $this->desiredDatabaseEncryption;
  }
  /**
   * The desired datapath provider for the cluster.
   *
   * Accepted values: DATAPATH_PROVIDER_UNSPECIFIED, LEGACY_DATAPATH,
   * ADVANCED_DATAPATH
   *
   * @param self::DESIRED_DATAPATH_PROVIDER_* $desiredDatapathProvider
   */
  public function setDesiredDatapathProvider($desiredDatapathProvider)
  {
    $this->desiredDatapathProvider = $desiredDatapathProvider;
  }
  /**
   * @return self::DESIRED_DATAPATH_PROVIDER_*
   */
  public function getDesiredDatapathProvider()
  {
    return $this->desiredDatapathProvider;
  }
  /**
   * Override the default setting of whether future created nodes have private
   * IP addresses only, namely NetworkConfig.default_enable_private_nodes
   *
   * @param bool $desiredDefaultEnablePrivateNodes
   */
  public function setDesiredDefaultEnablePrivateNodes($desiredDefaultEnablePrivateNodes)
  {
    $this->desiredDefaultEnablePrivateNodes = $desiredDefaultEnablePrivateNodes;
  }
  /**
   * @return bool
   */
  public function getDesiredDefaultEnablePrivateNodes()
  {
    return $this->desiredDefaultEnablePrivateNodes;
  }
  /**
   * The desired status of whether to disable default sNAT for this cluster.
   *
   * @param DefaultSnatStatus $desiredDefaultSnatStatus
   */
  public function setDesiredDefaultSnatStatus(DefaultSnatStatus $desiredDefaultSnatStatus)
  {
    $this->desiredDefaultSnatStatus = $desiredDefaultSnatStatus;
  }
  /**
   * @return DefaultSnatStatus
   */
  public function getDesiredDefaultSnatStatus()
  {
    return $this->desiredDefaultSnatStatus;
  }
  /**
   * Enable/Disable L4 LB VPC firewall reconciliation for the cluster.
   *
   * @param bool $desiredDisableL4LbFirewallReconciliation
   */
  public function setDesiredDisableL4LbFirewallReconciliation($desiredDisableL4LbFirewallReconciliation)
  {
    $this->desiredDisableL4LbFirewallReconciliation = $desiredDisableL4LbFirewallReconciliation;
  }
  /**
   * @return bool
   */
  public function getDesiredDisableL4LbFirewallReconciliation()
  {
    return $this->desiredDisableL4LbFirewallReconciliation;
  }
  /**
   * DNSConfig contains clusterDNS config for this cluster.
   *
   * @param DNSConfig $desiredDnsConfig
   */
  public function setDesiredDnsConfig(DNSConfig $desiredDnsConfig)
  {
    $this->desiredDnsConfig = $desiredDnsConfig;
  }
  /**
   * @return DNSConfig
   */
  public function getDesiredDnsConfig()
  {
    return $this->desiredDnsConfig;
  }
  /**
   * Enable/Disable Cilium Clusterwide Network Policy for the cluster.
   *
   * @param bool $desiredEnableCiliumClusterwideNetworkPolicy
   */
  public function setDesiredEnableCiliumClusterwideNetworkPolicy($desiredEnableCiliumClusterwideNetworkPolicy)
  {
    $this->desiredEnableCiliumClusterwideNetworkPolicy = $desiredEnableCiliumClusterwideNetworkPolicy;
  }
  /**
   * @return bool
   */
  public function getDesiredEnableCiliumClusterwideNetworkPolicy()
  {
    return $this->desiredEnableCiliumClusterwideNetworkPolicy;
  }
  /**
   * Enable/Disable FQDN Network Policy for the cluster.
   *
   * @param bool $desiredEnableFqdnNetworkPolicy
   */
  public function setDesiredEnableFqdnNetworkPolicy($desiredEnableFqdnNetworkPolicy)
  {
    $this->desiredEnableFqdnNetworkPolicy = $desiredEnableFqdnNetworkPolicy;
  }
  /**
   * @return bool
   */
  public function getDesiredEnableFqdnNetworkPolicy()
  {
    return $this->desiredEnableFqdnNetworkPolicy;
  }
  /**
   * Enable/Disable Multi-Networking for the cluster
   *
   * @param bool $desiredEnableMultiNetworking
   */
  public function setDesiredEnableMultiNetworking($desiredEnableMultiNetworking)
  {
    $this->desiredEnableMultiNetworking = $desiredEnableMultiNetworking;
  }
  /**
   * @return bool
   */
  public function getDesiredEnableMultiNetworking()
  {
    return $this->desiredEnableMultiNetworking;
  }
  /**
   * Enable/Disable private endpoint for the cluster's master. Deprecated: Use d
   * esired_control_plane_endpoints_config.ip_endpoints_config.enable_public_end
   * point instead. Note that the value of enable_public_endpoint is reversed:
   * if enable_private_endpoint is false, then enable_public_endpoint will be
   * true.
   *
   * @deprecated
   * @param bool $desiredEnablePrivateEndpoint
   */
  public function setDesiredEnablePrivateEndpoint($desiredEnablePrivateEndpoint)
  {
    $this->desiredEnablePrivateEndpoint = $desiredEnablePrivateEndpoint;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getDesiredEnablePrivateEndpoint()
  {
    return $this->desiredEnablePrivateEndpoint;
  }
  /**
   * The desired enterprise configuration for the cluster. Deprecated: GKE
   * Enterprise features are now available without an Enterprise tier.
   *
   * @deprecated
   * @param DesiredEnterpriseConfig $desiredEnterpriseConfig
   */
  public function setDesiredEnterpriseConfig(DesiredEnterpriseConfig $desiredEnterpriseConfig)
  {
    $this->desiredEnterpriseConfig = $desiredEnterpriseConfig;
  }
  /**
   * @deprecated
   * @return DesiredEnterpriseConfig
   */
  public function getDesiredEnterpriseConfig()
  {
    return $this->desiredEnterpriseConfig;
  }
  /**
   * The desired fleet configuration for the cluster.
   *
   * @param Fleet $desiredFleet
   */
  public function setDesiredFleet(Fleet $desiredFleet)
  {
    $this->desiredFleet = $desiredFleet;
  }
  /**
   * @return Fleet
   */
  public function getDesiredFleet()
  {
    return $this->desiredFleet;
  }
  /**
   * The desired config of Gateway API on this cluster.
   *
   * @param GatewayAPIConfig $desiredGatewayApiConfig
   */
  public function setDesiredGatewayApiConfig(GatewayAPIConfig $desiredGatewayApiConfig)
  {
    $this->desiredGatewayApiConfig = $desiredGatewayApiConfig;
  }
  /**
   * @return GatewayAPIConfig
   */
  public function getDesiredGatewayApiConfig()
  {
    return $this->desiredGatewayApiConfig;
  }
  /**
   * The desired GCFS config for the cluster
   *
   * @param GcfsConfig $desiredGcfsConfig
   */
  public function setDesiredGcfsConfig(GcfsConfig $desiredGcfsConfig)
  {
    $this->desiredGcfsConfig = $desiredGcfsConfig;
  }
  /**
   * @return GcfsConfig
   */
  public function getDesiredGcfsConfig()
  {
    return $this->desiredGcfsConfig;
  }
  /**
   * The desired Identity Service component configuration.
   *
   * @param IdentityServiceConfig $desiredIdentityServiceConfig
   */
  public function setDesiredIdentityServiceConfig(IdentityServiceConfig $desiredIdentityServiceConfig)
  {
    $this->desiredIdentityServiceConfig = $desiredIdentityServiceConfig;
  }
  /**
   * @return IdentityServiceConfig
   */
  public function getDesiredIdentityServiceConfig()
  {
    return $this->desiredIdentityServiceConfig;
  }
  /**
   * The desired image type for the node pool. NOTE: Set the "desired_node_pool"
   * field as well.
   *
   * @param string $desiredImageType
   */
  public function setDesiredImageType($desiredImageType)
  {
    $this->desiredImageType = $desiredImageType;
  }
  /**
   * @return string
   */
  public function getDesiredImageType()
  {
    return $this->desiredImageType;
  }
  /**
   * Specify the details of in-transit encryption.
   *
   * Accepted values: IN_TRANSIT_ENCRYPTION_CONFIG_UNSPECIFIED,
   * IN_TRANSIT_ENCRYPTION_DISABLED,
   * IN_TRANSIT_ENCRYPTION_INTER_NODE_TRANSPARENT
   *
   * @param self::DESIRED_IN_TRANSIT_ENCRYPTION_CONFIG_* $desiredInTransitEncryptionConfig
   */
  public function setDesiredInTransitEncryptionConfig($desiredInTransitEncryptionConfig)
  {
    $this->desiredInTransitEncryptionConfig = $desiredInTransitEncryptionConfig;
  }
  /**
   * @return self::DESIRED_IN_TRANSIT_ENCRYPTION_CONFIG_*
   */
  public function getDesiredInTransitEncryptionConfig()
  {
    return $this->desiredInTransitEncryptionConfig;
  }
  /**
   * The desired config of Intra-node visibility.
   *
   * @param IntraNodeVisibilityConfig $desiredIntraNodeVisibilityConfig
   */
  public function setDesiredIntraNodeVisibilityConfig(IntraNodeVisibilityConfig $desiredIntraNodeVisibilityConfig)
  {
    $this->desiredIntraNodeVisibilityConfig = $desiredIntraNodeVisibilityConfig;
  }
  /**
   * @return IntraNodeVisibilityConfig
   */
  public function getDesiredIntraNodeVisibilityConfig()
  {
    return $this->desiredIntraNodeVisibilityConfig;
  }
  /**
   * Desired Beta APIs to be enabled for cluster.
   *
   * @param K8sBetaAPIConfig $desiredK8sBetaApis
   */
  public function setDesiredK8sBetaApis(K8sBetaAPIConfig $desiredK8sBetaApis)
  {
    $this->desiredK8sBetaApis = $desiredK8sBetaApis;
  }
  /**
   * @return K8sBetaAPIConfig
   */
  public function getDesiredK8sBetaApis()
  {
    return $this->desiredK8sBetaApis;
  }
  /**
   * The desired L4 Internal Load Balancer Subsetting configuration.
   *
   * @param ILBSubsettingConfig $desiredL4ilbSubsettingConfig
   */
  public function setDesiredL4ilbSubsettingConfig(ILBSubsettingConfig $desiredL4ilbSubsettingConfig)
  {
    $this->desiredL4ilbSubsettingConfig = $desiredL4ilbSubsettingConfig;
  }
  /**
   * @return ILBSubsettingConfig
   */
  public function getDesiredL4ilbSubsettingConfig()
  {
    return $this->desiredL4ilbSubsettingConfig;
  }
  /**
   * The desired list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster's nodes should be located. This list must always include the
   * cluster's primary zone. Warning: changing cluster locations will update the
   * locations of all node pools and will result in nodes being added and/or
   * removed.
   *
   * @param string[] $desiredLocations
   */
  public function setDesiredLocations($desiredLocations)
  {
    $this->desiredLocations = $desiredLocations;
  }
  /**
   * @return string[]
   */
  public function getDesiredLocations()
  {
    return $this->desiredLocations;
  }
  /**
   * The desired logging configuration.
   *
   * @param LoggingConfig $desiredLoggingConfig
   */
  public function setDesiredLoggingConfig(LoggingConfig $desiredLoggingConfig)
  {
    $this->desiredLoggingConfig = $desiredLoggingConfig;
  }
  /**
   * @return LoggingConfig
   */
  public function getDesiredLoggingConfig()
  {
    return $this->desiredLoggingConfig;
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
   * @param string $desiredLoggingService
   */
  public function setDesiredLoggingService($desiredLoggingService)
  {
    $this->desiredLoggingService = $desiredLoggingService;
  }
  /**
   * @return string
   */
  public function getDesiredLoggingService()
  {
    return $this->desiredLoggingService;
  }
  /**
   * The desired managed open telemetry configuration.
   *
   * @param ManagedOpenTelemetryConfig $desiredManagedOpentelemetryConfig
   */
  public function setDesiredManagedOpentelemetryConfig(ManagedOpenTelemetryConfig $desiredManagedOpentelemetryConfig)
  {
    $this->desiredManagedOpentelemetryConfig = $desiredManagedOpentelemetryConfig;
  }
  /**
   * @return ManagedOpenTelemetryConfig
   */
  public function getDesiredManagedOpentelemetryConfig()
  {
    return $this->desiredManagedOpentelemetryConfig;
  }
  /**
   * The desired configuration options for master authorized networks feature.
   * Deprecated: Use desired_control_plane_endpoints_config.ip_endpoints_config.
   * authorized_networks_config instead.
   *
   * @deprecated
   * @param MasterAuthorizedNetworksConfig $desiredMasterAuthorizedNetworksConfig
   */
  public function setDesiredMasterAuthorizedNetworksConfig(MasterAuthorizedNetworksConfig $desiredMasterAuthorizedNetworksConfig)
  {
    $this->desiredMasterAuthorizedNetworksConfig = $desiredMasterAuthorizedNetworksConfig;
  }
  /**
   * @deprecated
   * @return MasterAuthorizedNetworksConfig
   */
  public function getDesiredMasterAuthorizedNetworksConfig()
  {
    return $this->desiredMasterAuthorizedNetworksConfig;
  }
  /**
   * The Kubernetes version to change the master to. Users may specify either
   * explicit versions offered by Kubernetes Engine or version aliases, which
   * have the following behavior: - "latest": picks the highest valid Kubernetes
   * version - "1.X": picks the highest valid patch+gke.N patch in the 1.X
   * version - "1.X.Y": picks the highest valid gke.N patch in the 1.X.Y version
   * - "1.X.Y-gke.N": picks an explicit Kubernetes version - "-": picks the
   * default Kubernetes version
   *
   * @param string $desiredMasterVersion
   */
  public function setDesiredMasterVersion($desiredMasterVersion)
  {
    $this->desiredMasterVersion = $desiredMasterVersion;
  }
  /**
   * @return string
   */
  public function getDesiredMasterVersion()
  {
    return $this->desiredMasterVersion;
  }
  /**
   * Configuration for issuance of mTLS keys and certificates to Kubernetes
   * pods.
   *
   * @param MeshCertificates $desiredMeshCertificates
   */
  public function setDesiredMeshCertificates(MeshCertificates $desiredMeshCertificates)
  {
    $this->desiredMeshCertificates = $desiredMeshCertificates;
  }
  /**
   * @return MeshCertificates
   */
  public function getDesiredMeshCertificates()
  {
    return $this->desiredMeshCertificates;
  }
  /**
   * The desired monitoring configuration.
   *
   * @param MonitoringConfig $desiredMonitoringConfig
   */
  public function setDesiredMonitoringConfig(MonitoringConfig $desiredMonitoringConfig)
  {
    $this->desiredMonitoringConfig = $desiredMonitoringConfig;
  }
  /**
   * @return MonitoringConfig
   */
  public function getDesiredMonitoringConfig()
  {
    return $this->desiredMonitoringConfig;
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
   * @param string $desiredMonitoringService
   */
  public function setDesiredMonitoringService($desiredMonitoringService)
  {
    $this->desiredMonitoringService = $desiredMonitoringService;
  }
  /**
   * @return string
   */
  public function getDesiredMonitoringService()
  {
    return $this->desiredMonitoringService;
  }
  /**
   * The desired network performance config.
   *
   * @param ClusterNetworkPerformanceConfig $desiredNetworkPerformanceConfig
   */
  public function setDesiredNetworkPerformanceConfig(ClusterNetworkPerformanceConfig $desiredNetworkPerformanceConfig)
  {
    $this->desiredNetworkPerformanceConfig = $desiredNetworkPerformanceConfig;
  }
  /**
   * @return ClusterNetworkPerformanceConfig
   */
  public function getDesiredNetworkPerformanceConfig()
  {
    return $this->desiredNetworkPerformanceConfig;
  }
  /**
   * The desired network tier configuration for the cluster.
   *
   * @param NetworkTierConfig $desiredNetworkTierConfig
   */
  public function setDesiredNetworkTierConfig(NetworkTierConfig $desiredNetworkTierConfig)
  {
    $this->desiredNetworkTierConfig = $desiredNetworkTierConfig;
  }
  /**
   * @return NetworkTierConfig
   */
  public function getDesiredNetworkTierConfig()
  {
    return $this->desiredNetworkTierConfig;
  }
  /**
   * The desired node kubelet config for the cluster.
   *
   * @param NodeKubeletConfig $desiredNodeKubeletConfig
   */
  public function setDesiredNodeKubeletConfig(NodeKubeletConfig $desiredNodeKubeletConfig)
  {
    $this->desiredNodeKubeletConfig = $desiredNodeKubeletConfig;
  }
  /**
   * @return NodeKubeletConfig
   */
  public function getDesiredNodeKubeletConfig()
  {
    return $this->desiredNodeKubeletConfig;
  }
  /**
   * The desired node kubelet config for all auto-provisioned node pools in
   * autopilot clusters and node auto-provisioning enabled clusters.
   *
   * @param NodeKubeletConfig $desiredNodePoolAutoConfigKubeletConfig
   */
  public function setDesiredNodePoolAutoConfigKubeletConfig(NodeKubeletConfig $desiredNodePoolAutoConfigKubeletConfig)
  {
    $this->desiredNodePoolAutoConfigKubeletConfig = $desiredNodePoolAutoConfigKubeletConfig;
  }
  /**
   * @return NodeKubeletConfig
   */
  public function getDesiredNodePoolAutoConfigKubeletConfig()
  {
    return $this->desiredNodePoolAutoConfigKubeletConfig;
  }
  /**
   * The desired Linux node config for all auto-provisioned node pools in
   * autopilot clusters and node auto-provisioning enabled clusters. Currently
   * only `cgroup_mode` can be set here.
   *
   * @param LinuxNodeConfig $desiredNodePoolAutoConfigLinuxNodeConfig
   */
  public function setDesiredNodePoolAutoConfigLinuxNodeConfig(LinuxNodeConfig $desiredNodePoolAutoConfigLinuxNodeConfig)
  {
    $this->desiredNodePoolAutoConfigLinuxNodeConfig = $desiredNodePoolAutoConfigLinuxNodeConfig;
  }
  /**
   * @return LinuxNodeConfig
   */
  public function getDesiredNodePoolAutoConfigLinuxNodeConfig()
  {
    return $this->desiredNodePoolAutoConfigLinuxNodeConfig;
  }
  /**
   * The desired network tags that apply to all auto-provisioned node pools in
   * autopilot clusters and node auto-provisioning enabled clusters.
   *
   * @param NetworkTags $desiredNodePoolAutoConfigNetworkTags
   */
  public function setDesiredNodePoolAutoConfigNetworkTags(NetworkTags $desiredNodePoolAutoConfigNetworkTags)
  {
    $this->desiredNodePoolAutoConfigNetworkTags = $desiredNodePoolAutoConfigNetworkTags;
  }
  /**
   * @return NetworkTags
   */
  public function getDesiredNodePoolAutoConfigNetworkTags()
  {
    return $this->desiredNodePoolAutoConfigNetworkTags;
  }
  /**
   * The desired resource manager tags that apply to all auto-provisioned node
   * pools in autopilot clusters and node auto-provisioning enabled clusters.
   *
   * @param ResourceManagerTags $desiredNodePoolAutoConfigResourceManagerTags
   */
  public function setDesiredNodePoolAutoConfigResourceManagerTags(ResourceManagerTags $desiredNodePoolAutoConfigResourceManagerTags)
  {
    $this->desiredNodePoolAutoConfigResourceManagerTags = $desiredNodePoolAutoConfigResourceManagerTags;
  }
  /**
   * @return ResourceManagerTags
   */
  public function getDesiredNodePoolAutoConfigResourceManagerTags()
  {
    return $this->desiredNodePoolAutoConfigResourceManagerTags;
  }
  /**
   * Autoscaler configuration for the node pool specified in
   * desired_node_pool_id. If there is only one pool in the cluster and
   * desired_node_pool_id is not provided then the change applies to that single
   * node pool.
   *
   * @param NodePoolAutoscaling $desiredNodePoolAutoscaling
   */
  public function setDesiredNodePoolAutoscaling(NodePoolAutoscaling $desiredNodePoolAutoscaling)
  {
    $this->desiredNodePoolAutoscaling = $desiredNodePoolAutoscaling;
  }
  /**
   * @return NodePoolAutoscaling
   */
  public function getDesiredNodePoolAutoscaling()
  {
    return $this->desiredNodePoolAutoscaling;
  }
  /**
   * The node pool to be upgraded. This field is mandatory if
   * "desired_node_version", "desired_image_family" or
   * "desired_node_pool_autoscaling" is specified and there is more than one
   * node pool on the cluster.
   *
   * @param string $desiredNodePoolId
   */
  public function setDesiredNodePoolId($desiredNodePoolId)
  {
    $this->desiredNodePoolId = $desiredNodePoolId;
  }
  /**
   * @return string
   */
  public function getDesiredNodePoolId()
  {
    return $this->desiredNodePoolId;
  }
  /**
   * The desired node pool logging configuration defaults for the cluster.
   *
   * @param NodePoolLoggingConfig $desiredNodePoolLoggingConfig
   */
  public function setDesiredNodePoolLoggingConfig(NodePoolLoggingConfig $desiredNodePoolLoggingConfig)
  {
    $this->desiredNodePoolLoggingConfig = $desiredNodePoolLoggingConfig;
  }
  /**
   * @return NodePoolLoggingConfig
   */
  public function getDesiredNodePoolLoggingConfig()
  {
    return $this->desiredNodePoolLoggingConfig;
  }
  /**
   * The Kubernetes version to change the nodes to (typically an upgrade). Users
   * may specify either explicit versions offered by Kubernetes Engine or
   * version aliases, which have the following behavior: - "latest": picks the
   * highest valid Kubernetes version - "1.X": picks the highest valid
   * patch+gke.N patch in the 1.X version - "1.X.Y": picks the highest valid
   * gke.N patch in the 1.X.Y version - "1.X.Y-gke.N": picks an explicit
   * Kubernetes version - "-": picks the Kubernetes master version
   *
   * @param string $desiredNodeVersion
   */
  public function setDesiredNodeVersion($desiredNodeVersion)
  {
    $this->desiredNodeVersion = $desiredNodeVersion;
  }
  /**
   * @return string
   */
  public function getDesiredNodeVersion()
  {
    return $this->desiredNodeVersion;
  }
  /**
   * The desired notification configuration.
   *
   * @param NotificationConfig $desiredNotificationConfig
   */
  public function setDesiredNotificationConfig(NotificationConfig $desiredNotificationConfig)
  {
    $this->desiredNotificationConfig = $desiredNotificationConfig;
  }
  /**
   * @return NotificationConfig
   */
  public function getDesiredNotificationConfig()
  {
    return $this->desiredNotificationConfig;
  }
  /**
   * The desired parent product config for the cluster.
   *
   * @param ParentProductConfig $desiredParentProductConfig
   */
  public function setDesiredParentProductConfig(ParentProductConfig $desiredParentProductConfig)
  {
    $this->desiredParentProductConfig = $desiredParentProductConfig;
  }
  /**
   * @return ParentProductConfig
   */
  public function getDesiredParentProductConfig()
  {
    return $this->desiredParentProductConfig;
  }
  /**
   * The desired config for pod autoscaling.
   *
   * @param PodAutoscaling $desiredPodAutoscaling
   */
  public function setDesiredPodAutoscaling(PodAutoscaling $desiredPodAutoscaling)
  {
    $this->desiredPodAutoscaling = $desiredPodAutoscaling;
  }
  /**
   * @return PodAutoscaling
   */
  public function getDesiredPodAutoscaling()
  {
    return $this->desiredPodAutoscaling;
  }
  /**
   * The desired private cluster configuration. master_global_access_config is
   * the only field that can be changed via this field. See also
   * ClusterUpdate.desired_enable_private_endpoint for modifying other fields
   * within PrivateClusterConfig. Deprecated: Use
   * desired_control_plane_endpoints_config.ip_endpoints_config.global_access
   * instead.
   *
   * @deprecated
   * @param PrivateClusterConfig $desiredPrivateClusterConfig
   */
  public function setDesiredPrivateClusterConfig(PrivateClusterConfig $desiredPrivateClusterConfig)
  {
    $this->desiredPrivateClusterConfig = $desiredPrivateClusterConfig;
  }
  /**
   * @deprecated
   * @return PrivateClusterConfig
   */
  public function getDesiredPrivateClusterConfig()
  {
    return $this->desiredPrivateClusterConfig;
  }
  /**
   * The desired state of IPv6 connectivity to Google Services.
   *
   * Accepted values: PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED,
   * PRIVATE_IPV6_GOOGLE_ACCESS_DISABLED, PRIVATE_IPV6_GOOGLE_ACCESS_TO_GOOGLE,
   * PRIVATE_IPV6_GOOGLE_ACCESS_BIDIRECTIONAL
   *
   * @param self::DESIRED_PRIVATE_IPV6_GOOGLE_ACCESS_* $desiredPrivateIpv6GoogleAccess
   */
  public function setDesiredPrivateIpv6GoogleAccess($desiredPrivateIpv6GoogleAccess)
  {
    $this->desiredPrivateIpv6GoogleAccess = $desiredPrivateIpv6GoogleAccess;
  }
  /**
   * @return self::DESIRED_PRIVATE_IPV6_GOOGLE_ACCESS_*
   */
  public function getDesiredPrivateIpv6GoogleAccess()
  {
    return $this->desiredPrivateIpv6GoogleAccess;
  }
  /**
   * The desired privileged admission config for the cluster.
   *
   * @param PrivilegedAdmissionConfig $desiredPrivilegedAdmissionConfig
   */
  public function setDesiredPrivilegedAdmissionConfig(PrivilegedAdmissionConfig $desiredPrivilegedAdmissionConfig)
  {
    $this->desiredPrivilegedAdmissionConfig = $desiredPrivilegedAdmissionConfig;
  }
  /**
   * @return PrivilegedAdmissionConfig
   */
  public function getDesiredPrivilegedAdmissionConfig()
  {
    return $this->desiredPrivilegedAdmissionConfig;
  }
  /**
   * RBACBindingConfig allows user to restrict ClusterRoleBindings an
   * RoleBindings that can be created.
   *
   * @param RBACBindingConfig $desiredRbacBindingConfig
   */
  public function setDesiredRbacBindingConfig(RBACBindingConfig $desiredRbacBindingConfig)
  {
    $this->desiredRbacBindingConfig = $desiredRbacBindingConfig;
  }
  /**
   * @return RBACBindingConfig
   */
  public function getDesiredRbacBindingConfig()
  {
    return $this->desiredRbacBindingConfig;
  }
  /**
   * The desired release channel configuration.
   *
   * @param ReleaseChannel $desiredReleaseChannel
   */
  public function setDesiredReleaseChannel(ReleaseChannel $desiredReleaseChannel)
  {
    $this->desiredReleaseChannel = $desiredReleaseChannel;
  }
  /**
   * @return ReleaseChannel
   */
  public function getDesiredReleaseChannel()
  {
    return $this->desiredReleaseChannel;
  }
  /**
   * The desired configuration for exporting resource usage.
   *
   * @param ResourceUsageExportConfig $desiredResourceUsageExportConfig
   */
  public function setDesiredResourceUsageExportConfig(ResourceUsageExportConfig $desiredResourceUsageExportConfig)
  {
    $this->desiredResourceUsageExportConfig = $desiredResourceUsageExportConfig;
  }
  /**
   * @return ResourceUsageExportConfig
   */
  public function getDesiredResourceUsageExportConfig()
  {
    return $this->desiredResourceUsageExportConfig;
  }
  /**
   * Enable/Disable Secret Manager Config.
   *
   * @param SecretManagerConfig $desiredSecretManagerConfig
   */
  public function setDesiredSecretManagerConfig(SecretManagerConfig $desiredSecretManagerConfig)
  {
    $this->desiredSecretManagerConfig = $desiredSecretManagerConfig;
  }
  /**
   * @return SecretManagerConfig
   */
  public function getDesiredSecretManagerConfig()
  {
    return $this->desiredSecretManagerConfig;
  }
  /**
   * Enable/Disable Security Posture API features for the cluster.
   *
   * @param SecurityPostureConfig $desiredSecurityPostureConfig
   */
  public function setDesiredSecurityPostureConfig(SecurityPostureConfig $desiredSecurityPostureConfig)
  {
    $this->desiredSecurityPostureConfig = $desiredSecurityPostureConfig;
  }
  /**
   * @return SecurityPostureConfig
   */
  public function getDesiredSecurityPostureConfig()
  {
    return $this->desiredSecurityPostureConfig;
  }
  /**
   * ServiceExternalIPsConfig specifies the config for the use of Services with
   * ExternalIPs field.
   *
   * @param ServiceExternalIPsConfig $desiredServiceExternalIpsConfig
   */
  public function setDesiredServiceExternalIpsConfig(ServiceExternalIPsConfig $desiredServiceExternalIpsConfig)
  {
    $this->desiredServiceExternalIpsConfig = $desiredServiceExternalIpsConfig;
  }
  /**
   * @return ServiceExternalIPsConfig
   */
  public function getDesiredServiceExternalIpsConfig()
  {
    return $this->desiredServiceExternalIpsConfig;
  }
  /**
   * Configuration for Shielded Nodes.
   *
   * @param ShieldedNodes $desiredShieldedNodes
   */
  public function setDesiredShieldedNodes(ShieldedNodes $desiredShieldedNodes)
  {
    $this->desiredShieldedNodes = $desiredShieldedNodes;
  }
  /**
   * @return ShieldedNodes
   */
  public function getDesiredShieldedNodes()
  {
    return $this->desiredShieldedNodes;
  }
  /**
   * The desired stack type of the cluster. If a stack type is provided and does
   * not match the current stack type of the cluster, update will attempt to
   * change the stack type to the new type.
   *
   * Accepted values: STACK_TYPE_UNSPECIFIED, IPV4, IPV4_IPV6
   *
   * @param self::DESIRED_STACK_TYPE_* $desiredStackType
   */
  public function setDesiredStackType($desiredStackType)
  {
    $this->desiredStackType = $desiredStackType;
  }
  /**
   * @return self::DESIRED_STACK_TYPE_*
   */
  public function getDesiredStackType()
  {
    return $this->desiredStackType;
  }
  /**
   * The desired user managed keys config for the cluster.
   *
   * @param UserManagedKeysConfig $desiredUserManagedKeysConfig
   */
  public function setDesiredUserManagedKeysConfig(UserManagedKeysConfig $desiredUserManagedKeysConfig)
  {
    $this->desiredUserManagedKeysConfig = $desiredUserManagedKeysConfig;
  }
  /**
   * @return UserManagedKeysConfig
   */
  public function getDesiredUserManagedKeysConfig()
  {
    return $this->desiredUserManagedKeysConfig;
  }
  /**
   * Cluster-level Vertical Pod Autoscaling configuration.
   *
   * @param VerticalPodAutoscaling $desiredVerticalPodAutoscaling
   */
  public function setDesiredVerticalPodAutoscaling(VerticalPodAutoscaling $desiredVerticalPodAutoscaling)
  {
    $this->desiredVerticalPodAutoscaling = $desiredVerticalPodAutoscaling;
  }
  /**
   * @return VerticalPodAutoscaling
   */
  public function getDesiredVerticalPodAutoscaling()
  {
    return $this->desiredVerticalPodAutoscaling;
  }
  /**
   * Configuration for Workload Identity.
   *
   * @param WorkloadIdentityConfig $desiredWorkloadIdentityConfig
   */
  public function setDesiredWorkloadIdentityConfig(WorkloadIdentityConfig $desiredWorkloadIdentityConfig)
  {
    $this->desiredWorkloadIdentityConfig = $desiredWorkloadIdentityConfig;
  }
  /**
   * @return WorkloadIdentityConfig
   */
  public function getDesiredWorkloadIdentityConfig()
  {
    return $this->desiredWorkloadIdentityConfig;
  }
  /**
   * Kubernetes open source beta apis enabled on the cluster. Only beta apis
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
   * The current etag of the cluster. If an etag is provided and does not match
   * the current etag of the cluster, update will be blocked and an ABORTED
   * error will be returned.
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
   * Configuration for GKE auto upgrade.
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
   * The additional pod ranges that are to be removed from the cluster. The pod
   * ranges specified here must have been specified earlier in the
   * 'additional_pod_ranges_config' argument.
   *
   * @param AdditionalPodRangesConfig $removedAdditionalPodRangesConfig
   */
  public function setRemovedAdditionalPodRangesConfig(AdditionalPodRangesConfig $removedAdditionalPodRangesConfig)
  {
    $this->removedAdditionalPodRangesConfig = $removedAdditionalPodRangesConfig;
  }
  /**
   * @return AdditionalPodRangesConfig
   */
  public function getRemovedAdditionalPodRangesConfig()
  {
    return $this->removedAdditionalPodRangesConfig;
  }
  /**
   * The Custom keys configuration for the cluster. This field is deprecated.
   * Use ClusterUpdate.desired_user_managed_keys_config instead.
   *
   * @deprecated
   * @param UserManagedKeysConfig $userManagedKeysConfig
   */
  public function setUserManagedKeysConfig(UserManagedKeysConfig $userManagedKeysConfig)
  {
    $this->userManagedKeysConfig = $userManagedKeysConfig;
  }
  /**
   * @deprecated
   * @return UserManagedKeysConfig
   */
  public function getUserManagedKeysConfig()
  {
    return $this->userManagedKeysConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterUpdate::class, 'Google_Service_Container_ClusterUpdate');
