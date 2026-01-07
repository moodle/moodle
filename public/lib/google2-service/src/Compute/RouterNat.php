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

namespace Google\Service\Compute;

class RouterNat extends \Google\Collection
{
  /**
   * Public internet quality with fixed bandwidth.
   */
  public const AUTO_NETWORK_TIER_FIXED_STANDARD = 'FIXED_STANDARD';
  /**
   * High quality, Google-grade network tier, support for all networking
   * products.
   */
  public const AUTO_NETWORK_TIER_PREMIUM = 'PREMIUM';
  /**
   * Public internet quality, only limited support for other networking
   * products.
   */
  public const AUTO_NETWORK_TIER_STANDARD = 'STANDARD';
  /**
   * (Output only) Temporary tier for FIXED_STANDARD when fixed standard tier is
   * expired or not configured.
   */
  public const AUTO_NETWORK_TIER_STANDARD_OVERRIDES_FIXED_STANDARD = 'STANDARD_OVERRIDES_FIXED_STANDARD';
  /**
   * Nat IPs are allocated by GCP; customers can not specify any Nat IPs.
   */
  public const NAT_IP_ALLOCATE_OPTION_AUTO_ONLY = 'AUTO_ONLY';
  /**
   * Only use Nat IPs provided by customers. When specified Nat IPs are not
   * enough then the Nat service fails for new VMs.
   */
  public const NAT_IP_ALLOCATE_OPTION_MANUAL_ONLY = 'MANUAL_ONLY';
  /**
   * All the IP ranges in every Subnetwork are allowed to Nat.
   */
  public const SOURCE_SUBNETWORK_IP_RANGES_TO_NAT_ALL_SUBNETWORKS_ALL_IP_RANGES = 'ALL_SUBNETWORKS_ALL_IP_RANGES';
  /**
   * All the primary IP ranges in every Subnetwork are allowed to Nat.
   */
  public const SOURCE_SUBNETWORK_IP_RANGES_TO_NAT_ALL_SUBNETWORKS_ALL_PRIMARY_IP_RANGES = 'ALL_SUBNETWORKS_ALL_PRIMARY_IP_RANGES';
  /**
   * A list of Subnetworks are allowed to Nat (specified in the field subnetwork
   * below)
   */
  public const SOURCE_SUBNETWORK_IP_RANGES_TO_NAT_LIST_OF_SUBNETWORKS = 'LIST_OF_SUBNETWORKS';
  /**
   * NAT64 is enabled for all the IPv6 subnet ranges. In dual stack subnets,
   * NAT64 will only be enabled for IPv6-only VMs.
   */
  public const SOURCE_SUBNETWORK_IP_RANGES_TO_NAT64_ALL_IPV6_SUBNETWORKS = 'ALL_IPV6_SUBNETWORKS';
  /**
   * NAT64 is enabled for a list of IPv6 subnet ranges. In dual stack subnets,
   * NAT64 will only be enabled for IPv6-only VMs. If this option is used, the
   * nat64_subnetworks field must be specified.
   */
  public const SOURCE_SUBNETWORK_IP_RANGES_TO_NAT64_LIST_OF_IPV6_SUBNETWORKS = 'LIST_OF_IPV6_SUBNETWORKS';
  /**
   * NAT used for private IP translation.
   */
  public const TYPE_PRIVATE = 'PRIVATE';
  /**
   * NAT used for public IP translation. This is the default.
   */
  public const TYPE_PUBLIC = 'PUBLIC';
  protected $collection_key = 'subnetworks';
  /**
   * The network tier to use when automatically reserving NAT IP addresses. Must
   * be one of: PREMIUM, STANDARD. If not specified, then the current  project-
   * level default tier is used.
   *
   * @var string
   */
  public $autoNetworkTier;
  /**
   * A list of URLs of the IP resources to be drained. These IPs must be valid
   * static external IPs that have been assigned to the NAT. These IPs should be
   * used for updating/patching a NAT only.
   *
   * @var string[]
   */
  public $drainNatIps;
  /**
   * Enable Dynamic Port Allocation.
   *
   * If not specified, it is disabled by default.
   *
   * If set to true,        - Dynamic Port Allocation will be enabled on this
   * NAT    config.    - enableEndpointIndependentMapping cannot be set to true.
   * - If minPorts is set, minPortsPerVm must be set to a    power of two
   * greater than or equal to 32. If minPortsPerVm is not set, a    minimum of
   * 32 ports will be allocated to a VM from this NAT    config.
   *
   * @var bool
   */
  public $enableDynamicPortAllocation;
  /**
   * @var bool
   */
  public $enableEndpointIndependentMapping;
  /**
   * List of NAT-ted endpoint types supported by the Nat Gateway. If the list is
   * empty, then it will be equivalent to include ENDPOINT_TYPE_VM
   *
   * @var string[]
   */
  public $endpointTypes;
  /**
   * Timeout (in seconds) for ICMP connections. Defaults to 30s if not set.
   *
   * @var int
   */
  public $icmpIdleTimeoutSec;
  protected $logConfigType = RouterNatLogConfig::class;
  protected $logConfigDataType = '';
  /**
   * Maximum number of ports allocated to a VM from this NAT config when Dynamic
   * Port Allocation is enabled.
   *
   * If Dynamic Port Allocation is not enabled, this field has no effect.
   *
   * If Dynamic Port Allocation is enabled, and this field is set, it must be
   * set to a power of two greater than minPortsPerVm, or 64 if minPortsPerVm is
   * not set.
   *
   * If Dynamic Port Allocation is enabled and this field is not set, a maximum
   * of 65536 ports will be allocated to a VM from this NAT config.
   *
   * @var int
   */
  public $maxPortsPerVm;
  /**
   * Minimum number of ports allocated to a VM from this NAT config. If not set,
   * a default number of ports is allocated to a VM. This is rounded up to the
   * nearest power of 2. For example, if the value of this field is 50, at least
   * 64 ports are allocated to a VM.
   *
   * @var int
   */
  public $minPortsPerVm;
  /**
   * Unique name of this Nat service. The name must be 1-63 characters long and
   * comply withRFC1035.
   *
   * @var string
   */
  public $name;
  protected $nat64SubnetworksType = RouterNatSubnetworkToNat64::class;
  protected $nat64SubnetworksDataType = 'array';
  /**
   * Specify the NatIpAllocateOption, which can take one of the following
   * values:         - MANUAL_ONLY: Uses only Nat IP addresses provided by
   * customers. When there are not enough specified Nat IPs, the Nat service
   * fails for new VMs.    - AUTO_ONLY: Nat IPs are allocated by Google Cloud
   * Platform; customers    can't specify any Nat IPs. When choosing AUTO_ONLY,
   * then nat_ip should    be empty.
   *
   * @var string
   */
  public $natIpAllocateOption;
  /**
   * A list of URLs of the IP resources used for this Nat service. These IP
   * addresses must be valid static external IP addresses assigned to the
   * project.
   *
   * @var string[]
   */
  public $natIps;
  protected $rulesType = RouterNatRule::class;
  protected $rulesDataType = 'array';
  /**
   * Specify the Nat option, which can take one of the following values:
   * - ALL_SUBNETWORKS_ALL_IP_RANGES: All of the IP ranges in every
   * Subnetwork are allowed to Nat.    - ALL_SUBNETWORKS_ALL_PRIMARY_IP_RANGES:
   * All of the primary IP ranges    in every Subnetwork are allowed to Nat.
   * - LIST_OF_SUBNETWORKS: A list of Subnetworks are allowed to Nat
   * (specified in the field subnetwork below)
   *
   * The default is SUBNETWORK_IP_RANGE_TO_NAT_OPTION_UNSPECIFIED. Note that if
   * this field contains ALL_SUBNETWORKS_ALL_IP_RANGES then there should not be
   * any other Router.Nat section in any Router for this network in this region.
   *
   * @var string
   */
  public $sourceSubnetworkIpRangesToNat;
  /**
   * Specify the Nat option for NAT64, which can take one of the following
   * values:         - ALL_IPV6_SUBNETWORKS: All of the IP ranges in    every
   * Subnetwork are allowed to Nat.    - LIST_OF_IPV6_SUBNETWORKS: A list of
   * Subnetworks are allowed to Nat    (specified in the field nat64_subnetwork
   * below)
   *
   * The default is NAT64_OPTION_UNSPECIFIED. Note that if this field contains
   * NAT64_ALL_V6_SUBNETWORKS no other Router.Nat section in this region can
   * also enable NAT64 for any Subnetworks in this network. Other Router.Nat
   * sections can still be present to enable NAT44 only.
   *
   * @var string
   */
  public $sourceSubnetworkIpRangesToNat64;
  protected $subnetworksType = RouterNatSubnetworkToNat::class;
  protected $subnetworksDataType = 'array';
  /**
   * Timeout (in seconds) for TCP established connections. Defaults to 1200s if
   * not set.
   *
   * @var int
   */
  public $tcpEstablishedIdleTimeoutSec;
  /**
   * Timeout (in seconds) for TCP connections that are in TIME_WAIT state.
   * Defaults to 120s if not set.
   *
   * @var int
   */
  public $tcpTimeWaitTimeoutSec;
  /**
   * Timeout (in seconds) for TCP transitory connections. Defaults to 30s if not
   * set.
   *
   * @var int
   */
  public $tcpTransitoryIdleTimeoutSec;
  /**
   * Indicates whether this NAT is used for public or private IP translation. If
   * unspecified, it defaults to PUBLIC.
   *
   * @var string
   */
  public $type;
  /**
   * Timeout (in seconds) for UDP connections. Defaults to 30s if not set.
   *
   * @var int
   */
  public $udpIdleTimeoutSec;

  /**
   * The network tier to use when automatically reserving NAT IP addresses. Must
   * be one of: PREMIUM, STANDARD. If not specified, then the current  project-
   * level default tier is used.
   *
   * Accepted values: FIXED_STANDARD, PREMIUM, STANDARD,
   * STANDARD_OVERRIDES_FIXED_STANDARD
   *
   * @param self::AUTO_NETWORK_TIER_* $autoNetworkTier
   */
  public function setAutoNetworkTier($autoNetworkTier)
  {
    $this->autoNetworkTier = $autoNetworkTier;
  }
  /**
   * @return self::AUTO_NETWORK_TIER_*
   */
  public function getAutoNetworkTier()
  {
    return $this->autoNetworkTier;
  }
  /**
   * A list of URLs of the IP resources to be drained. These IPs must be valid
   * static external IPs that have been assigned to the NAT. These IPs should be
   * used for updating/patching a NAT only.
   *
   * @param string[] $drainNatIps
   */
  public function setDrainNatIps($drainNatIps)
  {
    $this->drainNatIps = $drainNatIps;
  }
  /**
   * @return string[]
   */
  public function getDrainNatIps()
  {
    return $this->drainNatIps;
  }
  /**
   * Enable Dynamic Port Allocation.
   *
   * If not specified, it is disabled by default.
   *
   * If set to true,        - Dynamic Port Allocation will be enabled on this
   * NAT    config.    - enableEndpointIndependentMapping cannot be set to true.
   * - If minPorts is set, minPortsPerVm must be set to a    power of two
   * greater than or equal to 32. If minPortsPerVm is not set, a    minimum of
   * 32 ports will be allocated to a VM from this NAT    config.
   *
   * @param bool $enableDynamicPortAllocation
   */
  public function setEnableDynamicPortAllocation($enableDynamicPortAllocation)
  {
    $this->enableDynamicPortAllocation = $enableDynamicPortAllocation;
  }
  /**
   * @return bool
   */
  public function getEnableDynamicPortAllocation()
  {
    return $this->enableDynamicPortAllocation;
  }
  /**
   * @param bool $enableEndpointIndependentMapping
   */
  public function setEnableEndpointIndependentMapping($enableEndpointIndependentMapping)
  {
    $this->enableEndpointIndependentMapping = $enableEndpointIndependentMapping;
  }
  /**
   * @return bool
   */
  public function getEnableEndpointIndependentMapping()
  {
    return $this->enableEndpointIndependentMapping;
  }
  /**
   * List of NAT-ted endpoint types supported by the Nat Gateway. If the list is
   * empty, then it will be equivalent to include ENDPOINT_TYPE_VM
   *
   * @param string[] $endpointTypes
   */
  public function setEndpointTypes($endpointTypes)
  {
    $this->endpointTypes = $endpointTypes;
  }
  /**
   * @return string[]
   */
  public function getEndpointTypes()
  {
    return $this->endpointTypes;
  }
  /**
   * Timeout (in seconds) for ICMP connections. Defaults to 30s if not set.
   *
   * @param int $icmpIdleTimeoutSec
   */
  public function setIcmpIdleTimeoutSec($icmpIdleTimeoutSec)
  {
    $this->icmpIdleTimeoutSec = $icmpIdleTimeoutSec;
  }
  /**
   * @return int
   */
  public function getIcmpIdleTimeoutSec()
  {
    return $this->icmpIdleTimeoutSec;
  }
  /**
   * Configure logging on this NAT.
   *
   * @param RouterNatLogConfig $logConfig
   */
  public function setLogConfig(RouterNatLogConfig $logConfig)
  {
    $this->logConfig = $logConfig;
  }
  /**
   * @return RouterNatLogConfig
   */
  public function getLogConfig()
  {
    return $this->logConfig;
  }
  /**
   * Maximum number of ports allocated to a VM from this NAT config when Dynamic
   * Port Allocation is enabled.
   *
   * If Dynamic Port Allocation is not enabled, this field has no effect.
   *
   * If Dynamic Port Allocation is enabled, and this field is set, it must be
   * set to a power of two greater than minPortsPerVm, or 64 if minPortsPerVm is
   * not set.
   *
   * If Dynamic Port Allocation is enabled and this field is not set, a maximum
   * of 65536 ports will be allocated to a VM from this NAT config.
   *
   * @param int $maxPortsPerVm
   */
  public function setMaxPortsPerVm($maxPortsPerVm)
  {
    $this->maxPortsPerVm = $maxPortsPerVm;
  }
  /**
   * @return int
   */
  public function getMaxPortsPerVm()
  {
    return $this->maxPortsPerVm;
  }
  /**
   * Minimum number of ports allocated to a VM from this NAT config. If not set,
   * a default number of ports is allocated to a VM. This is rounded up to the
   * nearest power of 2. For example, if the value of this field is 50, at least
   * 64 ports are allocated to a VM.
   *
   * @param int $minPortsPerVm
   */
  public function setMinPortsPerVm($minPortsPerVm)
  {
    $this->minPortsPerVm = $minPortsPerVm;
  }
  /**
   * @return int
   */
  public function getMinPortsPerVm()
  {
    return $this->minPortsPerVm;
  }
  /**
   * Unique name of this Nat service. The name must be 1-63 characters long and
   * comply withRFC1035.
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
   * List of Subnetwork resources whose traffic should be translated by NAT64
   * Gateway. It is used only when LIST_OF_IPV6_SUBNETWORKS is selected for the
   * SubnetworkIpRangeToNat64Option above.
   *
   * @param RouterNatSubnetworkToNat64[] $nat64Subnetworks
   */
  public function setNat64Subnetworks($nat64Subnetworks)
  {
    $this->nat64Subnetworks = $nat64Subnetworks;
  }
  /**
   * @return RouterNatSubnetworkToNat64[]
   */
  public function getNat64Subnetworks()
  {
    return $this->nat64Subnetworks;
  }
  /**
   * Specify the NatIpAllocateOption, which can take one of the following
   * values:         - MANUAL_ONLY: Uses only Nat IP addresses provided by
   * customers. When there are not enough specified Nat IPs, the Nat service
   * fails for new VMs.    - AUTO_ONLY: Nat IPs are allocated by Google Cloud
   * Platform; customers    can't specify any Nat IPs. When choosing AUTO_ONLY,
   * then nat_ip should    be empty.
   *
   * Accepted values: AUTO_ONLY, MANUAL_ONLY
   *
   * @param self::NAT_IP_ALLOCATE_OPTION_* $natIpAllocateOption
   */
  public function setNatIpAllocateOption($natIpAllocateOption)
  {
    $this->natIpAllocateOption = $natIpAllocateOption;
  }
  /**
   * @return self::NAT_IP_ALLOCATE_OPTION_*
   */
  public function getNatIpAllocateOption()
  {
    return $this->natIpAllocateOption;
  }
  /**
   * A list of URLs of the IP resources used for this Nat service. These IP
   * addresses must be valid static external IP addresses assigned to the
   * project.
   *
   * @param string[] $natIps
   */
  public function setNatIps($natIps)
  {
    $this->natIps = $natIps;
  }
  /**
   * @return string[]
   */
  public function getNatIps()
  {
    return $this->natIps;
  }
  /**
   * A list of rules associated with this NAT.
   *
   * @param RouterNatRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return RouterNatRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Specify the Nat option, which can take one of the following values:
   * - ALL_SUBNETWORKS_ALL_IP_RANGES: All of the IP ranges in every
   * Subnetwork are allowed to Nat.    - ALL_SUBNETWORKS_ALL_PRIMARY_IP_RANGES:
   * All of the primary IP ranges    in every Subnetwork are allowed to Nat.
   * - LIST_OF_SUBNETWORKS: A list of Subnetworks are allowed to Nat
   * (specified in the field subnetwork below)
   *
   * The default is SUBNETWORK_IP_RANGE_TO_NAT_OPTION_UNSPECIFIED. Note that if
   * this field contains ALL_SUBNETWORKS_ALL_IP_RANGES then there should not be
   * any other Router.Nat section in any Router for this network in this region.
   *
   * Accepted values: ALL_SUBNETWORKS_ALL_IP_RANGES,
   * ALL_SUBNETWORKS_ALL_PRIMARY_IP_RANGES, LIST_OF_SUBNETWORKS
   *
   * @param self::SOURCE_SUBNETWORK_IP_RANGES_TO_NAT_* $sourceSubnetworkIpRangesToNat
   */
  public function setSourceSubnetworkIpRangesToNat($sourceSubnetworkIpRangesToNat)
  {
    $this->sourceSubnetworkIpRangesToNat = $sourceSubnetworkIpRangesToNat;
  }
  /**
   * @return self::SOURCE_SUBNETWORK_IP_RANGES_TO_NAT_*
   */
  public function getSourceSubnetworkIpRangesToNat()
  {
    return $this->sourceSubnetworkIpRangesToNat;
  }
  /**
   * Specify the Nat option for NAT64, which can take one of the following
   * values:         - ALL_IPV6_SUBNETWORKS: All of the IP ranges in    every
   * Subnetwork are allowed to Nat.    - LIST_OF_IPV6_SUBNETWORKS: A list of
   * Subnetworks are allowed to Nat    (specified in the field nat64_subnetwork
   * below)
   *
   * The default is NAT64_OPTION_UNSPECIFIED. Note that if this field contains
   * NAT64_ALL_V6_SUBNETWORKS no other Router.Nat section in this region can
   * also enable NAT64 for any Subnetworks in this network. Other Router.Nat
   * sections can still be present to enable NAT44 only.
   *
   * Accepted values: ALL_IPV6_SUBNETWORKS, LIST_OF_IPV6_SUBNETWORKS
   *
   * @param self::SOURCE_SUBNETWORK_IP_RANGES_TO_NAT64_* $sourceSubnetworkIpRangesToNat64
   */
  public function setSourceSubnetworkIpRangesToNat64($sourceSubnetworkIpRangesToNat64)
  {
    $this->sourceSubnetworkIpRangesToNat64 = $sourceSubnetworkIpRangesToNat64;
  }
  /**
   * @return self::SOURCE_SUBNETWORK_IP_RANGES_TO_NAT64_*
   */
  public function getSourceSubnetworkIpRangesToNat64()
  {
    return $this->sourceSubnetworkIpRangesToNat64;
  }
  /**
   * A list of Subnetwork resources whose traffic should be translated by NAT
   * Gateway. It is used only when LIST_OF_SUBNETWORKS is selected for the
   * SubnetworkIpRangeToNatOption above.
   *
   * @param RouterNatSubnetworkToNat[] $subnetworks
   */
  public function setSubnetworks($subnetworks)
  {
    $this->subnetworks = $subnetworks;
  }
  /**
   * @return RouterNatSubnetworkToNat[]
   */
  public function getSubnetworks()
  {
    return $this->subnetworks;
  }
  /**
   * Timeout (in seconds) for TCP established connections. Defaults to 1200s if
   * not set.
   *
   * @param int $tcpEstablishedIdleTimeoutSec
   */
  public function setTcpEstablishedIdleTimeoutSec($tcpEstablishedIdleTimeoutSec)
  {
    $this->tcpEstablishedIdleTimeoutSec = $tcpEstablishedIdleTimeoutSec;
  }
  /**
   * @return int
   */
  public function getTcpEstablishedIdleTimeoutSec()
  {
    return $this->tcpEstablishedIdleTimeoutSec;
  }
  /**
   * Timeout (in seconds) for TCP connections that are in TIME_WAIT state.
   * Defaults to 120s if not set.
   *
   * @param int $tcpTimeWaitTimeoutSec
   */
  public function setTcpTimeWaitTimeoutSec($tcpTimeWaitTimeoutSec)
  {
    $this->tcpTimeWaitTimeoutSec = $tcpTimeWaitTimeoutSec;
  }
  /**
   * @return int
   */
  public function getTcpTimeWaitTimeoutSec()
  {
    return $this->tcpTimeWaitTimeoutSec;
  }
  /**
   * Timeout (in seconds) for TCP transitory connections. Defaults to 30s if not
   * set.
   *
   * @param int $tcpTransitoryIdleTimeoutSec
   */
  public function setTcpTransitoryIdleTimeoutSec($tcpTransitoryIdleTimeoutSec)
  {
    $this->tcpTransitoryIdleTimeoutSec = $tcpTransitoryIdleTimeoutSec;
  }
  /**
   * @return int
   */
  public function getTcpTransitoryIdleTimeoutSec()
  {
    return $this->tcpTransitoryIdleTimeoutSec;
  }
  /**
   * Indicates whether this NAT is used for public or private IP translation. If
   * unspecified, it defaults to PUBLIC.
   *
   * Accepted values: PRIVATE, PUBLIC
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Timeout (in seconds) for UDP connections. Defaults to 30s if not set.
   *
   * @param int $udpIdleTimeoutSec
   */
  public function setUdpIdleTimeoutSec($udpIdleTimeoutSec)
  {
    $this->udpIdleTimeoutSec = $udpIdleTimeoutSec;
  }
  /**
   * @return int
   */
  public function getUdpIdleTimeoutSec()
  {
    return $this->udpIdleTimeoutSec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterNat::class, 'Google_Service_Compute_RouterNat');
