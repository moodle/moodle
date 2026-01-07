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

class NodeNetworkConfig extends \Google\Collection
{
  protected $collection_key = 'additionalPodNetworkConfigs';
  protected $additionalNodeNetworkConfigsType = AdditionalNodeNetworkConfig::class;
  protected $additionalNodeNetworkConfigsDataType = 'array';
  protected $additionalPodNetworkConfigsType = AdditionalPodNetworkConfig::class;
  protected $additionalPodNetworkConfigsDataType = 'array';
  /**
   * Input only. Whether to create a new range for pod IPs in this node pool.
   * Defaults are provided for `pod_range` and `pod_ipv4_cidr_block` if they are
   * not specified. If neither `create_pod_range` or `pod_range` are specified,
   * the cluster-level default (`ip_allocation_policy.cluster_ipv4_cidr_block`)
   * is used. Only applicable if `ip_allocation_policy.use_ip_aliases` is true.
   * This field cannot be changed after the node pool has been created.
   *
   * @var bool
   */
  public $createPodRange;
  /**
   * Whether nodes have internal IP addresses only. If enable_private_nodes is
   * not specified, then the value is derived from
   * Cluster.NetworkConfig.default_enable_private_nodes
   *
   * @var bool
   */
  public $enablePrivateNodes;
  protected $networkPerformanceConfigType = NetworkPerformanceConfig::class;
  protected $networkPerformanceConfigDataType = '';
  protected $networkTierConfigType = NetworkTierConfig::class;
  protected $networkTierConfigDataType = '';
  protected $podCidrOverprovisionConfigType = PodCIDROverprovisionConfig::class;
  protected $podCidrOverprovisionConfigDataType = '';
  /**
   * The IP address range for pod IPs in this node pool. Only applicable if
   * `create_pod_range` is true. Set to blank to have a range chosen with the
   * default size. Set to /netmask (e.g. `/14`) to have a range chosen with a
   * specific netmask. Set to a
   * [CIDR](https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) to pick a specific range to use. Only
   * applicable if `ip_allocation_policy.use_ip_aliases` is true. This field
   * cannot be changed after the node pool has been created.
   *
   * @var string
   */
  public $podIpv4CidrBlock;
  /**
   * Output only. The utilization of the IPv4 range for the pod. The ratio is
   * Usage/[Total number of IPs in the secondary range],
   * Usage=numNodes*numZones*podIPsPerNode.
   *
   * @var 
   */
  public $podIpv4RangeUtilization;
  /**
   * The ID of the secondary range for pod IPs. If `create_pod_range` is true,
   * this ID is used for the new range. If `create_pod_range` is false, uses an
   * existing secondary range with this ID. Only applicable if
   * `ip_allocation_policy.use_ip_aliases` is true. This field cannot be changed
   * after the node pool has been created.
   *
   * @var string
   */
  public $podRange;
  /**
   * The subnetwork path for the node pool. Format:
   * projects/{project}/regions/{region}/subnetworks/{subnetwork} If the cluster
   * is associated with multiple subnetworks, the subnetwork for the node pool
   * is picked based on the IP utilization during node pool creation and is
   * immutable.
   *
   * @var string
   */
  public $subnetwork;

  /**
   * We specify the additional node networks for this node pool using this list.
   * Each node network corresponds to an additional interface
   *
   * @param AdditionalNodeNetworkConfig[] $additionalNodeNetworkConfigs
   */
  public function setAdditionalNodeNetworkConfigs($additionalNodeNetworkConfigs)
  {
    $this->additionalNodeNetworkConfigs = $additionalNodeNetworkConfigs;
  }
  /**
   * @return AdditionalNodeNetworkConfig[]
   */
  public function getAdditionalNodeNetworkConfigs()
  {
    return $this->additionalNodeNetworkConfigs;
  }
  /**
   * We specify the additional pod networks for this node pool using this list.
   * Each pod network corresponds to an additional alias IP range for the node
   *
   * @param AdditionalPodNetworkConfig[] $additionalPodNetworkConfigs
   */
  public function setAdditionalPodNetworkConfigs($additionalPodNetworkConfigs)
  {
    $this->additionalPodNetworkConfigs = $additionalPodNetworkConfigs;
  }
  /**
   * @return AdditionalPodNetworkConfig[]
   */
  public function getAdditionalPodNetworkConfigs()
  {
    return $this->additionalPodNetworkConfigs;
  }
  /**
   * Input only. Whether to create a new range for pod IPs in this node pool.
   * Defaults are provided for `pod_range` and `pod_ipv4_cidr_block` if they are
   * not specified. If neither `create_pod_range` or `pod_range` are specified,
   * the cluster-level default (`ip_allocation_policy.cluster_ipv4_cidr_block`)
   * is used. Only applicable if `ip_allocation_policy.use_ip_aliases` is true.
   * This field cannot be changed after the node pool has been created.
   *
   * @param bool $createPodRange
   */
  public function setCreatePodRange($createPodRange)
  {
    $this->createPodRange = $createPodRange;
  }
  /**
   * @return bool
   */
  public function getCreatePodRange()
  {
    return $this->createPodRange;
  }
  /**
   * Whether nodes have internal IP addresses only. If enable_private_nodes is
   * not specified, then the value is derived from
   * Cluster.NetworkConfig.default_enable_private_nodes
   *
   * @param bool $enablePrivateNodes
   */
  public function setEnablePrivateNodes($enablePrivateNodes)
  {
    $this->enablePrivateNodes = $enablePrivateNodes;
  }
  /**
   * @return bool
   */
  public function getEnablePrivateNodes()
  {
    return $this->enablePrivateNodes;
  }
  /**
   * Network bandwidth tier configuration.
   *
   * @param NetworkPerformanceConfig $networkPerformanceConfig
   */
  public function setNetworkPerformanceConfig(NetworkPerformanceConfig $networkPerformanceConfig)
  {
    $this->networkPerformanceConfig = $networkPerformanceConfig;
  }
  /**
   * @return NetworkPerformanceConfig
   */
  public function getNetworkPerformanceConfig()
  {
    return $this->networkPerformanceConfig;
  }
  /**
   * Output only. The network tier configuration for the node pool inherits from
   * the cluster-level configuration and remains immutable throughout the node
   * pool's lifecycle, including during upgrades.
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
   * [PRIVATE FIELD] Pod CIDR size overprovisioning config for the nodepool. Pod
   * CIDR size per node depends on max_pods_per_node. By default, the value of
   * max_pods_per_node is rounded off to next power of 2 and we then double that
   * to get the size of pod CIDR block per node. Example: max_pods_per_node of
   * 30 would result in 64 IPs (/26). This config can disable the doubling of
   * IPs (we still round off to next power of 2) Example: max_pods_per_node of
   * 30 will result in 32 IPs (/27) when overprovisioning is disabled.
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
   * The IP address range for pod IPs in this node pool. Only applicable if
   * `create_pod_range` is true. Set to blank to have a range chosen with the
   * default size. Set to /netmask (e.g. `/14`) to have a range chosen with a
   * specific netmask. Set to a
   * [CIDR](https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing)
   * notation (e.g. `10.96.0.0/14`) to pick a specific range to use. Only
   * applicable if `ip_allocation_policy.use_ip_aliases` is true. This field
   * cannot be changed after the node pool has been created.
   *
   * @param string $podIpv4CidrBlock
   */
  public function setPodIpv4CidrBlock($podIpv4CidrBlock)
  {
    $this->podIpv4CidrBlock = $podIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getPodIpv4CidrBlock()
  {
    return $this->podIpv4CidrBlock;
  }
  public function setPodIpv4RangeUtilization($podIpv4RangeUtilization)
  {
    $this->podIpv4RangeUtilization = $podIpv4RangeUtilization;
  }
  public function getPodIpv4RangeUtilization()
  {
    return $this->podIpv4RangeUtilization;
  }
  /**
   * The ID of the secondary range for pod IPs. If `create_pod_range` is true,
   * this ID is used for the new range. If `create_pod_range` is false, uses an
   * existing secondary range with this ID. Only applicable if
   * `ip_allocation_policy.use_ip_aliases` is true. This field cannot be changed
   * after the node pool has been created.
   *
   * @param string $podRange
   */
  public function setPodRange($podRange)
  {
    $this->podRange = $podRange;
  }
  /**
   * @return string
   */
  public function getPodRange()
  {
    return $this->podRange;
  }
  /**
   * The subnetwork path for the node pool. Format:
   * projects/{project}/regions/{region}/subnetworks/{subnetwork} If the cluster
   * is associated with multiple subnetworks, the subnetwork for the node pool
   * is picked based on the IP utilization during node pool creation and is
   * immutable.
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
class_alias(NodeNetworkConfig::class, 'Google_Service_Container_NodeNetworkConfig');
