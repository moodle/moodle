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

class IPAllocationPolicy extends \Google\Collection
{
  /**
   * Default value, will be defaulted as type external.
   */
  public const IPV6_ACCESS_TYPE_IPV6_ACCESS_TYPE_UNSPECIFIED = 'IPV6_ACCESS_TYPE_UNSPECIFIED';
  /**
   * Access type internal (all v6 addresses are internal IPs)
   */
  public const IPV6_ACCESS_TYPE_INTERNAL = 'INTERNAL';
  /**
   * Access type external (all v6 addresses are external IPs)
   */
  public const IPV6_ACCESS_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * Default value, will be defaulted as IPV4 only
   */
  public const STACK_TYPE_STACK_TYPE_UNSPECIFIED = 'STACK_TYPE_UNSPECIFIED';
  /**
   * Cluster is IPV4 only
   */
  public const STACK_TYPE_IPV4 = 'IPV4';
  /**
   * Cluster can use both IPv4 and IPv6
   */
  public const STACK_TYPE_IPV4_IPV6 = 'IPV4_IPV6';
  protected $collection_key = 'additionalIpRangesConfigs';
  protected $additionalIpRangesConfigsType = AdditionalIPRangesConfig::class;
  protected $additionalIpRangesConfigsDataType = 'array';
  protected $additionalPodRangesConfigType = AdditionalPodRangesConfig::class;
  protected $additionalPodRangesConfigDataType = '';
  protected $autoIpamConfigType = AutoIpamConfig::class;
  protected $autoIpamConfigDataType = '';
  /**
   * This field is deprecated, use cluster_ipv4_cidr_block.
   *
   * @deprecated
   * @var string
   */
  public $clusterIpv4Cidr;
  /**
   * The IP address range for the cluster pod IPs. If this field is set, then
   * `cluster.cluster_ipv4_cidr` must be left blank. This field is only
   * applicable when `use_ip_aliases` is true. Set to blank to have a range
   * chosen with the default size. Set to /netmask (e.g. `/14`) to have a range
   * chosen with a specific netmask. Set to a
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @var string
   */
  public $clusterIpv4CidrBlock;
  /**
   * The name of the secondary range to be used for the cluster CIDR block. The
   * secondary range will be used for pod IP addresses. This must be an existing
   * secondary range associated with the cluster subnetwork. This field is only
   * applicable with use_ip_aliases is true and create_subnetwork is false.
   *
   * @var string
   */
  public $clusterSecondaryRangeName;
  /**
   * Whether a new subnetwork will be created automatically for the cluster.
   * This field is only applicable when `use_ip_aliases` is true.
   *
   * @var bool
   */
  public $createSubnetwork;
  /**
   * Output only. The utilization of the cluster default IPv4 range for the pod.
   * The ratio is Usage/[Total number of IPs in the secondary range],
   * Usage=numNodes*numZones*podIPsPerNode.
   *
   * @var 
   */
  public $defaultPodIpv4RangeUtilization;
  /**
   * The ipv6 access type (internal or external) when create_subnetwork is true
   *
   * @var string
   */
  public $ipv6AccessType;
  protected $networkTierConfigType = NetworkTierConfig::class;
  protected $networkTierConfigDataType = '';
  /**
   * This field is deprecated, use node_ipv4_cidr_block.
   *
   * @deprecated
   * @var string
   */
  public $nodeIpv4Cidr;
  /**
   * The IP address range of the instance IPs in this cluster. This is
   * applicable only if `create_subnetwork` is true. Set to blank to have a
   * range chosen with the default size. Set to /netmask (e.g. `/14`) to have a
   * range chosen with a specific netmask. Set to a
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @var string
   */
  public $nodeIpv4CidrBlock;
  protected $podCidrOverprovisionConfigType = PodCIDROverprovisionConfig::class;
  protected $podCidrOverprovisionConfigDataType = '';
  /**
   * This field is deprecated, use services_ipv4_cidr_block.
   *
   * @deprecated
   * @var string
   */
  public $servicesIpv4Cidr;
  /**
   * The IP address range of the services IPs in this cluster. If blank, a range
   * will be automatically chosen with the default size. This field is only
   * applicable when `use_ip_aliases` is true. Set to blank to have a range
   * chosen with the default size. Set to /netmask (e.g. `/14`) to have a range
   * chosen with a specific netmask. Set to a
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @var string
   */
  public $servicesIpv4CidrBlock;
  /**
   * Output only. The services IPv6 CIDR block for the cluster.
   *
   * @var string
   */
  public $servicesIpv6CidrBlock;
  /**
   * The name of the secondary range to be used as for the services CIDR block.
   * The secondary range will be used for service ClusterIPs. This must be an
   * existing secondary range associated with the cluster subnetwork. This field
   * is only applicable with use_ip_aliases is true and create_subnetwork is
   * false.
   *
   * @var string
   */
  public $servicesSecondaryRangeName;
  /**
   * The IP stack type of the cluster
   *
   * @var string
   */
  public $stackType;
  /**
   * Output only. The subnet's IPv6 CIDR block used by nodes and pods.
   *
   * @var string
   */
  public $subnetIpv6CidrBlock;
  /**
   * A custom subnetwork name to be used if `create_subnetwork` is true. If this
   * field is empty, then an automatic name will be chosen for the new
   * subnetwork.
   *
   * @var string
   */
  public $subnetworkName;
  /**
   * The IP address range of the Cloud TPUs in this cluster. If unspecified, a
   * range will be automatically chosen with the default size. This field is
   * only applicable when `use_ip_aliases` is true. If unspecified, the range
   * will use the default size. Set to /netmask (e.g. `/14`) to have a range
   * chosen with a specific netmask. Set to a
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use. This field is deprecated due to the deprecation of 2VM TPU. The end
   * of life date for 2VM TPU is 2025-04-25.
   *
   * @deprecated
   * @var string
   */
  public $tpuIpv4CidrBlock;
  /**
   * Whether alias IPs will be used for pod IPs in the cluster. This is used in
   * conjunction with use_routes. It cannot be true if use_routes is true. If
   * both use_ip_aliases and use_routes are false, then the server picks the
   * default IP allocation mode
   *
   * @var bool
   */
  public $useIpAliases;
  /**
   * Whether routes will be used for pod IPs in the cluster. This is used in
   * conjunction with use_ip_aliases. It cannot be true if use_ip_aliases is
   * true. If both use_ip_aliases and use_routes are false, then the server
   * picks the default IP allocation mode
   *
   * @var bool
   */
  public $useRoutes;

  /**
   * Output only. The additional IP ranges that are added to the cluster. These
   * IP ranges can be used by new node pools to allocate node and pod IPs
   * automatically. Each AdditionalIPRangesConfig corresponds to a single
   * subnetwork. Once a range is removed it will not show up in
   * IPAllocationPolicy.
   *
   * @param AdditionalIPRangesConfig[] $additionalIpRangesConfigs
   */
  public function setAdditionalIpRangesConfigs($additionalIpRangesConfigs)
  {
    $this->additionalIpRangesConfigs = $additionalIpRangesConfigs;
  }
  /**
   * @return AdditionalIPRangesConfig[]
   */
  public function getAdditionalIpRangesConfigs()
  {
    return $this->additionalIpRangesConfigs;
  }
  /**
   * Output only. The additional pod ranges that are added to the cluster. These
   * pod ranges can be used by new node pools to allocate pod IPs automatically.
   * Once the range is removed it will not show up in IPAllocationPolicy.
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
   * Optional. AutoIpamConfig contains all information related to Auto IPAM
   *
   * @param AutoIpamConfig $autoIpamConfig
   */
  public function setAutoIpamConfig(AutoIpamConfig $autoIpamConfig)
  {
    $this->autoIpamConfig = $autoIpamConfig;
  }
  /**
   * @return AutoIpamConfig
   */
  public function getAutoIpamConfig()
  {
    return $this->autoIpamConfig;
  }
  /**
   * This field is deprecated, use cluster_ipv4_cidr_block.
   *
   * @deprecated
   * @param string $clusterIpv4Cidr
   */
  public function setClusterIpv4Cidr($clusterIpv4Cidr)
  {
    $this->clusterIpv4Cidr = $clusterIpv4Cidr;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getClusterIpv4Cidr()
  {
    return $this->clusterIpv4Cidr;
  }
  /**
   * The IP address range for the cluster pod IPs. If this field is set, then
   * `cluster.cluster_ipv4_cidr` must be left blank. This field is only
   * applicable when `use_ip_aliases` is true. Set to blank to have a range
   * chosen with the default size. Set to /netmask (e.g. `/14`) to have a range
   * chosen with a specific netmask. Set to a
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @param string $clusterIpv4CidrBlock
   */
  public function setClusterIpv4CidrBlock($clusterIpv4CidrBlock)
  {
    $this->clusterIpv4CidrBlock = $clusterIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getClusterIpv4CidrBlock()
  {
    return $this->clusterIpv4CidrBlock;
  }
  /**
   * The name of the secondary range to be used for the cluster CIDR block. The
   * secondary range will be used for pod IP addresses. This must be an existing
   * secondary range associated with the cluster subnetwork. This field is only
   * applicable with use_ip_aliases is true and create_subnetwork is false.
   *
   * @param string $clusterSecondaryRangeName
   */
  public function setClusterSecondaryRangeName($clusterSecondaryRangeName)
  {
    $this->clusterSecondaryRangeName = $clusterSecondaryRangeName;
  }
  /**
   * @return string
   */
  public function getClusterSecondaryRangeName()
  {
    return $this->clusterSecondaryRangeName;
  }
  /**
   * Whether a new subnetwork will be created automatically for the cluster.
   * This field is only applicable when `use_ip_aliases` is true.
   *
   * @param bool $createSubnetwork
   */
  public function setCreateSubnetwork($createSubnetwork)
  {
    $this->createSubnetwork = $createSubnetwork;
  }
  /**
   * @return bool
   */
  public function getCreateSubnetwork()
  {
    return $this->createSubnetwork;
  }
  public function setDefaultPodIpv4RangeUtilization($defaultPodIpv4RangeUtilization)
  {
    $this->defaultPodIpv4RangeUtilization = $defaultPodIpv4RangeUtilization;
  }
  public function getDefaultPodIpv4RangeUtilization()
  {
    return $this->defaultPodIpv4RangeUtilization;
  }
  /**
   * The ipv6 access type (internal or external) when create_subnetwork is true
   *
   * Accepted values: IPV6_ACCESS_TYPE_UNSPECIFIED, INTERNAL, EXTERNAL
   *
   * @param self::IPV6_ACCESS_TYPE_* $ipv6AccessType
   */
  public function setIpv6AccessType($ipv6AccessType)
  {
    $this->ipv6AccessType = $ipv6AccessType;
  }
  /**
   * @return self::IPV6_ACCESS_TYPE_*
   */
  public function getIpv6AccessType()
  {
    return $this->ipv6AccessType;
  }
  /**
   * Cluster-level network tier configuration is used to determine the default
   * network tier for external IP addresses on cluster resources, such as node
   * pools and load balancers.
   *
   * @param NetworkTierConfig $networkTierConfig
   */
  public function setNetworkTierConfig(NetworkTierConfig $networkTierConfig)
  {
    $this->networkTierConfig = $networkTierConfig;
  }
  /**
   * @return NetworkTierConfig
   */
  public function getNetworkTierConfig()
  {
    return $this->networkTierConfig;
  }
  /**
   * This field is deprecated, use node_ipv4_cidr_block.
   *
   * @deprecated
   * @param string $nodeIpv4Cidr
   */
  public function setNodeIpv4Cidr($nodeIpv4Cidr)
  {
    $this->nodeIpv4Cidr = $nodeIpv4Cidr;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getNodeIpv4Cidr()
  {
    return $this->nodeIpv4Cidr;
  }
  /**
   * The IP address range of the instance IPs in this cluster. This is
   * applicable only if `create_subnetwork` is true. Set to blank to have a
   * range chosen with the default size. Set to /netmask (e.g. `/14`) to have a
   * range chosen with a specific netmask. Set to a
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @param string $nodeIpv4CidrBlock
   */
  public function setNodeIpv4CidrBlock($nodeIpv4CidrBlock)
  {
    $this->nodeIpv4CidrBlock = $nodeIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getNodeIpv4CidrBlock()
  {
    return $this->nodeIpv4CidrBlock;
  }
  /**
   * [PRIVATE FIELD] Pod CIDR size overprovisioning config for the cluster. Pod
   * CIDR size per node depends on max_pods_per_node. By default, the value of
   * max_pods_per_node is doubled and then rounded off to next power of 2 to get
   * the size of pod CIDR block per node. Example: max_pods_per_node of 30 would
   * result in 64 IPs (/26). This config can disable the doubling of IPs (we
   * still round off to next power of 2) Example: max_pods_per_node of 30 will
   * result in 32 IPs (/27) when overprovisioning is disabled.
   *
   * @param PodCIDROverprovisionConfig $podCidrOverprovisionConfig
   */
  public function setPodCidrOverprovisionConfig(PodCIDROverprovisionConfig $podCidrOverprovisionConfig)
  {
    $this->podCidrOverprovisionConfig = $podCidrOverprovisionConfig;
  }
  /**
   * @return PodCIDROverprovisionConfig
   */
  public function getPodCidrOverprovisionConfig()
  {
    return $this->podCidrOverprovisionConfig;
  }
  /**
   * This field is deprecated, use services_ipv4_cidr_block.
   *
   * @deprecated
   * @param string $servicesIpv4Cidr
   */
  public function setServicesIpv4Cidr($servicesIpv4Cidr)
  {
    $this->servicesIpv4Cidr = $servicesIpv4Cidr;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getServicesIpv4Cidr()
  {
    return $this->servicesIpv4Cidr;
  }
  /**
   * The IP address range of the services IPs in this cluster. If blank, a range
   * will be automatically chosen with the default size. This field is only
   * applicable when `use_ip_aliases` is true. Set to blank to have a range
   * chosen with the default size. Set to /netmask (e.g. `/14`) to have a range
   * chosen with a specific netmask. Set to a
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use.
   *
   * @param string $servicesIpv4CidrBlock
   */
  public function setServicesIpv4CidrBlock($servicesIpv4CidrBlock)
  {
    $this->servicesIpv4CidrBlock = $servicesIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getServicesIpv4CidrBlock()
  {
    return $this->servicesIpv4CidrBlock;
  }
  /**
   * Output only. The services IPv6 CIDR block for the cluster.
   *
   * @param string $servicesIpv6CidrBlock
   */
  public function setServicesIpv6CidrBlock($servicesIpv6CidrBlock)
  {
    $this->servicesIpv6CidrBlock = $servicesIpv6CidrBlock;
  }
  /**
   * @return string
   */
  public function getServicesIpv6CidrBlock()
  {
    return $this->servicesIpv6CidrBlock;
  }
  /**
   * The name of the secondary range to be used as for the services CIDR block.
   * The secondary range will be used for service ClusterIPs. This must be an
   * existing secondary range associated with the cluster subnetwork. This field
   * is only applicable with use_ip_aliases is true and create_subnetwork is
   * false.
   *
   * @param string $servicesSecondaryRangeName
   */
  public function setServicesSecondaryRangeName($servicesSecondaryRangeName)
  {
    $this->servicesSecondaryRangeName = $servicesSecondaryRangeName;
  }
  /**
   * @return string
   */
  public function getServicesSecondaryRangeName()
  {
    return $this->servicesSecondaryRangeName;
  }
  /**
   * The IP stack type of the cluster
   *
   * Accepted values: STACK_TYPE_UNSPECIFIED, IPV4, IPV4_IPV6
   *
   * @param self::STACK_TYPE_* $stackType
   */
  public function setStackType($stackType)
  {
    $this->stackType = $stackType;
  }
  /**
   * @return self::STACK_TYPE_*
   */
  public function getStackType()
  {
    return $this->stackType;
  }
  /**
   * Output only. The subnet's IPv6 CIDR block used by nodes and pods.
   *
   * @param string $subnetIpv6CidrBlock
   */
  public function setSubnetIpv6CidrBlock($subnetIpv6CidrBlock)
  {
    $this->subnetIpv6CidrBlock = $subnetIpv6CidrBlock;
  }
  /**
   * @return string
   */
  public function getSubnetIpv6CidrBlock()
  {
    return $this->subnetIpv6CidrBlock;
  }
  /**
   * A custom subnetwork name to be used if `create_subnetwork` is true. If this
   * field is empty, then an automatic name will be chosen for the new
   * subnetwork.
   *
   * @param string $subnetworkName
   */
  public function setSubnetworkName($subnetworkName)
  {
    $this->subnetworkName = $subnetworkName;
  }
  /**
   * @return string
   */
  public function getSubnetworkName()
  {
    return $this->subnetworkName;
  }
  /**
   * The IP address range of the Cloud TPUs in this cluster. If unspecified, a
   * range will be automatically chosen with the default size. This field is
   * only applicable when `use_ip_aliases` is true. If unspecified, the range
   * will use the default size. Set to /netmask (e.g. `/14`) to have a range
   * chosen with a specific netmask. Set to a
   * [CIDR](http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) from the RFC-1918 private networks (e.g.
   * `10.0.0.0/8`, `172.16.0.0/12`, `192.168.0.0/16`) to pick a specific range
   * to use. This field is deprecated due to the deprecation of 2VM TPU. The end
   * of life date for 2VM TPU is 2025-04-25.
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
   * Whether alias IPs will be used for pod IPs in the cluster. This is used in
   * conjunction with use_routes. It cannot be true if use_routes is true. If
   * both use_ip_aliases and use_routes are false, then the server picks the
   * default IP allocation mode
   *
   * @param bool $useIpAliases
   */
  public function setUseIpAliases($useIpAliases)
  {
    $this->useIpAliases = $useIpAliases;
  }
  /**
   * @return bool
   */
  public function getUseIpAliases()
  {
    return $this->useIpAliases;
  }
  /**
   * Whether routes will be used for pod IPs in the cluster. This is used in
   * conjunction with use_ip_aliases. It cannot be true if use_ip_aliases is
   * true. If both use_ip_aliases and use_routes are false, then the server
   * picks the default IP allocation mode
   *
   * @param bool $useRoutes
   */
  public function setUseRoutes($useRoutes)
  {
    $this->useRoutes = $useRoutes;
  }
  /**
   * @return bool
   */
  public function getUseRoutes()
  {
    return $this->useRoutes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IPAllocationPolicy::class, 'Google_Service_Container_IPAllocationPolicy');
