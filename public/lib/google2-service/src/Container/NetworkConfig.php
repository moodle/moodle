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

class NetworkConfig extends \Google\Model
{
  /**
   * Default value.
   */
  public const DATAPATH_PROVIDER_DATAPATH_PROVIDER_UNSPECIFIED = 'DATAPATH_PROVIDER_UNSPECIFIED';
  /**
   * Use the IPTables implementation based on kube-proxy.
   */
  public const DATAPATH_PROVIDER_LEGACY_DATAPATH = 'LEGACY_DATAPATH';
  /**
   * Use the eBPF based GKE Dataplane V2 with additional features. See the [GKE
   * Dataplane V2 documentation](https://cloud.google.com/kubernetes-
   * engine/docs/how-to/dataplane-v2) for more.
   */
  public const DATAPATH_PROVIDER_ADVANCED_DATAPATH = 'ADVANCED_DATAPATH';
  /**
   * Unspecified, will be inferred as default -
   * IN_TRANSIT_ENCRYPTION_UNSPECIFIED.
   */
  public const IN_TRANSIT_ENCRYPTION_CONFIG_IN_TRANSIT_ENCRYPTION_CONFIG_UNSPECIFIED = 'IN_TRANSIT_ENCRYPTION_CONFIG_UNSPECIFIED';
  /**
   * In-transit encryption is disabled.
   */
  public const IN_TRANSIT_ENCRYPTION_CONFIG_IN_TRANSIT_ENCRYPTION_DISABLED = 'IN_TRANSIT_ENCRYPTION_DISABLED';
  /**
   * Data in-transit is encrypted using inter-node transparent encryption.
   */
  public const IN_TRANSIT_ENCRYPTION_CONFIG_IN_TRANSIT_ENCRYPTION_INTER_NODE_TRANSPARENT = 'IN_TRANSIT_ENCRYPTION_INTER_NODE_TRANSPARENT';
  /**
   * Default value. Same as DISABLED
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED = 'PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED';
  /**
   * No private access to or from Google Services
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_DISABLED = 'PRIVATE_IPV6_GOOGLE_ACCESS_DISABLED';
  /**
   * Enables private IPv6 access to Google Services from GKE
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_TO_GOOGLE = 'PRIVATE_IPV6_GOOGLE_ACCESS_TO_GOOGLE';
  /**
   * Enables private IPv6 access to and from Google Services
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_PRIVATE_IPV6_GOOGLE_ACCESS_BIDIRECTIONAL = 'PRIVATE_IPV6_GOOGLE_ACCESS_BIDIRECTIONAL';
  /**
   * The desired datapath provider for this cluster. By default, uses the
   * IPTables-based kube-proxy implementation.
   *
   * @var string
   */
  public $datapathProvider;
  /**
   * Controls whether by default nodes have private IP addresses only. It is
   * invalid to specify both PrivateClusterConfig.enablePrivateNodes and this
   * field at the same time. To update the default setting, use
   * ClusterUpdate.desired_default_enable_private_nodes
   *
   * @var bool
   */
  public $defaultEnablePrivateNodes;
  protected $defaultSnatStatusType = DefaultSnatStatus::class;
  protected $defaultSnatStatusDataType = '';
  /**
   * Disable L4 load balancer VPC firewalls to enable firewall policies.
   *
   * @var bool
   */
  public $disableL4LbFirewallReconciliation;
  protected $dnsConfigType = DNSConfig::class;
  protected $dnsConfigDataType = '';
  /**
   * Whether CiliumClusterwideNetworkPolicy is enabled on this cluster.
   *
   * @var bool
   */
  public $enableCiliumClusterwideNetworkPolicy;
  /**
   * Whether FQDN Network Policy is enabled on this cluster.
   *
   * @var bool
   */
  public $enableFqdnNetworkPolicy;
  /**
   * Whether Intra-node visibility is enabled for this cluster. This makes same
   * node pod to pod traffic visible for VPC network.
   *
   * @var bool
   */
  public $enableIntraNodeVisibility;
  /**
   * Whether L4ILB Subsetting is enabled for this cluster.
   *
   * @var bool
   */
  public $enableL4ilbSubsetting;
  /**
   * Whether multi-networking is enabled for this cluster.
   *
   * @var bool
   */
  public $enableMultiNetworking;
  protected $gatewayApiConfigType = GatewayAPIConfig::class;
  protected $gatewayApiConfigDataType = '';
  /**
   * Specify the details of in-transit encryption. Now named inter-node
   * transparent encryption.
   *
   * @var string
   */
  public $inTransitEncryptionConfig;
  /**
   * Output only. The relative name of the Google Compute Engine
   * [network](https://cloud.google.com/compute/docs/networks-and-
   * firewalls#networks) to which the cluster is connected. Example:
   * projects/my-project/global/networks/my-network
   *
   * @var string
   */
  public $network;
  protected $networkPerformanceConfigType = ClusterNetworkPerformanceConfig::class;
  protected $networkPerformanceConfigDataType = '';
  /**
   * The desired state of IPv6 connectivity to Google Services. By default, no
   * private IPv6 access to or from Google Services (all access will be via
   * IPv4)
   *
   * @var string
   */
  public $privateIpv6GoogleAccess;
  protected $serviceExternalIpsConfigType = ServiceExternalIPsConfig::class;
  protected $serviceExternalIpsConfigDataType = '';
  /**
   * Output only. The relative name of the Google Compute Engine
   * [subnetwork](https://cloud.google.com/compute/docs/vpc) to which the
   * cluster is connected. Example: projects/my-project/regions/us-
   * central1/subnetworks/my-subnet
   *
   * @var string
   */
  public $subnetwork;

  /**
   * The desired datapath provider for this cluster. By default, uses the
   * IPTables-based kube-proxy implementation.
   *
   * Accepted values: DATAPATH_PROVIDER_UNSPECIFIED, LEGACY_DATAPATH,
   * ADVANCED_DATAPATH
   *
   * @param self::DATAPATH_PROVIDER_* $datapathProvider
   */
  public function setDatapathProvider($datapathProvider)
  {
    $this->datapathProvider = $datapathProvider;
  }
  /**
   * @return self::DATAPATH_PROVIDER_*
   */
  public function getDatapathProvider()
  {
    return $this->datapathProvider;
  }
  /**
   * Controls whether by default nodes have private IP addresses only. It is
   * invalid to specify both PrivateClusterConfig.enablePrivateNodes and this
   * field at the same time. To update the default setting, use
   * ClusterUpdate.desired_default_enable_private_nodes
   *
   * @param bool $defaultEnablePrivateNodes
   */
  public function setDefaultEnablePrivateNodes($defaultEnablePrivateNodes)
  {
    $this->defaultEnablePrivateNodes = $defaultEnablePrivateNodes;
  }
  /**
   * @return bool
   */
  public function getDefaultEnablePrivateNodes()
  {
    return $this->defaultEnablePrivateNodes;
  }
  /**
   * Whether the cluster disables default in-node sNAT rules. In-node sNAT rules
   * will be disabled when default_snat_status is disabled. When disabled is set
   * to false, default IP masquerade rules will be applied to the nodes to
   * prevent sNAT on cluster internal traffic.
   *
   * @param DefaultSnatStatus $defaultSnatStatus
   */
  public function setDefaultSnatStatus(DefaultSnatStatus $defaultSnatStatus)
  {
    $this->defaultSnatStatus = $defaultSnatStatus;
  }
  /**
   * @return DefaultSnatStatus
   */
  public function getDefaultSnatStatus()
  {
    return $this->defaultSnatStatus;
  }
  /**
   * Disable L4 load balancer VPC firewalls to enable firewall policies.
   *
   * @param bool $disableL4LbFirewallReconciliation
   */
  public function setDisableL4LbFirewallReconciliation($disableL4LbFirewallReconciliation)
  {
    $this->disableL4LbFirewallReconciliation = $disableL4LbFirewallReconciliation;
  }
  /**
   * @return bool
   */
  public function getDisableL4LbFirewallReconciliation()
  {
    return $this->disableL4LbFirewallReconciliation;
  }
  /**
   * DNSConfig contains clusterDNS config for this cluster.
   *
   * @param DNSConfig $dnsConfig
   */
  public function setDnsConfig(DNSConfig $dnsConfig)
  {
    $this->dnsConfig = $dnsConfig;
  }
  /**
   * @return DNSConfig
   */
  public function getDnsConfig()
  {
    return $this->dnsConfig;
  }
  /**
   * Whether CiliumClusterwideNetworkPolicy is enabled on this cluster.
   *
   * @param bool $enableCiliumClusterwideNetworkPolicy
   */
  public function setEnableCiliumClusterwideNetworkPolicy($enableCiliumClusterwideNetworkPolicy)
  {
    $this->enableCiliumClusterwideNetworkPolicy = $enableCiliumClusterwideNetworkPolicy;
  }
  /**
   * @return bool
   */
  public function getEnableCiliumClusterwideNetworkPolicy()
  {
    return $this->enableCiliumClusterwideNetworkPolicy;
  }
  /**
   * Whether FQDN Network Policy is enabled on this cluster.
   *
   * @param bool $enableFqdnNetworkPolicy
   */
  public function setEnableFqdnNetworkPolicy($enableFqdnNetworkPolicy)
  {
    $this->enableFqdnNetworkPolicy = $enableFqdnNetworkPolicy;
  }
  /**
   * @return bool
   */
  public function getEnableFqdnNetworkPolicy()
  {
    return $this->enableFqdnNetworkPolicy;
  }
  /**
   * Whether Intra-node visibility is enabled for this cluster. This makes same
   * node pod to pod traffic visible for VPC network.
   *
   * @param bool $enableIntraNodeVisibility
   */
  public function setEnableIntraNodeVisibility($enableIntraNodeVisibility)
  {
    $this->enableIntraNodeVisibility = $enableIntraNodeVisibility;
  }
  /**
   * @return bool
   */
  public function getEnableIntraNodeVisibility()
  {
    return $this->enableIntraNodeVisibility;
  }
  /**
   * Whether L4ILB Subsetting is enabled for this cluster.
   *
   * @param bool $enableL4ilbSubsetting
   */
  public function setEnableL4ilbSubsetting($enableL4ilbSubsetting)
  {
    $this->enableL4ilbSubsetting = $enableL4ilbSubsetting;
  }
  /**
   * @return bool
   */
  public function getEnableL4ilbSubsetting()
  {
    return $this->enableL4ilbSubsetting;
  }
  /**
   * Whether multi-networking is enabled for this cluster.
   *
   * @param bool $enableMultiNetworking
   */
  public function setEnableMultiNetworking($enableMultiNetworking)
  {
    $this->enableMultiNetworking = $enableMultiNetworking;
  }
  /**
   * @return bool
   */
  public function getEnableMultiNetworking()
  {
    return $this->enableMultiNetworking;
  }
  /**
   * GatewayAPIConfig contains the desired config of Gateway API on this
   * cluster.
   *
   * @param GatewayAPIConfig $gatewayApiConfig
   */
  public function setGatewayApiConfig(GatewayAPIConfig $gatewayApiConfig)
  {
    $this->gatewayApiConfig = $gatewayApiConfig;
  }
  /**
   * @return GatewayAPIConfig
   */
  public function getGatewayApiConfig()
  {
    return $this->gatewayApiConfig;
  }
  /**
   * Specify the details of in-transit encryption. Now named inter-node
   * transparent encryption.
   *
   * Accepted values: IN_TRANSIT_ENCRYPTION_CONFIG_UNSPECIFIED,
   * IN_TRANSIT_ENCRYPTION_DISABLED,
   * IN_TRANSIT_ENCRYPTION_INTER_NODE_TRANSPARENT
   *
   * @param self::IN_TRANSIT_ENCRYPTION_CONFIG_* $inTransitEncryptionConfig
   */
  public function setInTransitEncryptionConfig($inTransitEncryptionConfig)
  {
    $this->inTransitEncryptionConfig = $inTransitEncryptionConfig;
  }
  /**
   * @return self::IN_TRANSIT_ENCRYPTION_CONFIG_*
   */
  public function getInTransitEncryptionConfig()
  {
    return $this->inTransitEncryptionConfig;
  }
  /**
   * Output only. The relative name of the Google Compute Engine
   * [network](https://cloud.google.com/compute/docs/networks-and-
   * firewalls#networks) to which the cluster is connected. Example:
   * projects/my-project/global/networks/my-network
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
   * Network bandwidth tier configuration.
   *
   * @param ClusterNetworkPerformanceConfig $networkPerformanceConfig
   */
  public function setNetworkPerformanceConfig(ClusterNetworkPerformanceConfig $networkPerformanceConfig)
  {
    $this->networkPerformanceConfig = $networkPerformanceConfig;
  }
  /**
   * @return ClusterNetworkPerformanceConfig
   */
  public function getNetworkPerformanceConfig()
  {
    return $this->networkPerformanceConfig;
  }
  /**
   * The desired state of IPv6 connectivity to Google Services. By default, no
   * private IPv6 access to or from Google Services (all access will be via
   * IPv4)
   *
   * Accepted values: PRIVATE_IPV6_GOOGLE_ACCESS_UNSPECIFIED,
   * PRIVATE_IPV6_GOOGLE_ACCESS_DISABLED, PRIVATE_IPV6_GOOGLE_ACCESS_TO_GOOGLE,
   * PRIVATE_IPV6_GOOGLE_ACCESS_BIDIRECTIONAL
   *
   * @param self::PRIVATE_IPV6_GOOGLE_ACCESS_* $privateIpv6GoogleAccess
   */
  public function setPrivateIpv6GoogleAccess($privateIpv6GoogleAccess)
  {
    $this->privateIpv6GoogleAccess = $privateIpv6GoogleAccess;
  }
  /**
   * @return self::PRIVATE_IPV6_GOOGLE_ACCESS_*
   */
  public function getPrivateIpv6GoogleAccess()
  {
    return $this->privateIpv6GoogleAccess;
  }
  /**
   * ServiceExternalIPsConfig specifies if services with externalIPs field are
   * blocked or not.
   *
   * @param ServiceExternalIPsConfig $serviceExternalIpsConfig
   */
  public function setServiceExternalIpsConfig(ServiceExternalIPsConfig $serviceExternalIpsConfig)
  {
    $this->serviceExternalIpsConfig = $serviceExternalIpsConfig;
  }
  /**
   * @return ServiceExternalIPsConfig
   */
  public function getServiceExternalIpsConfig()
  {
    return $this->serviceExternalIpsConfig;
  }
  /**
   * Output only. The relative name of the Google Compute Engine
   * [subnetwork](https://cloud.google.com/compute/docs/vpc) to which the
   * cluster is connected. Example: projects/my-project/regions/us-
   * central1/subnetworks/my-subnet
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConfig::class, 'Google_Service_Container_NetworkConfig');
