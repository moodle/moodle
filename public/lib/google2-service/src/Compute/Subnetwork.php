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

class Subnetwork extends \Google\Collection
{
  /**
   * VMs on this subnet will be assigned IPv6 addresses that are accessible via
   * the Internet, as well as the VPC network.
   */
  public const IPV6_ACCESS_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * VMs on this subnet will be assigned IPv6 addresses that are only accessible
   * over the VPC network.
   */
  public const IPV6_ACCESS_TYPE_INTERNAL = 'INTERNAL';
  public const IPV6_GCE_ENDPOINT_VM_AND_FR = 'VM_AND_FR';
  public const IPV6_GCE_ENDPOINT_VM_ONLY = 'VM_ONLY';
  /**
   * Disable private IPv6 access to/from Google services.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_DISABLE_GOOGLE_ACCESS = 'DISABLE_GOOGLE_ACCESS';
  /**
   * Bidirectional private IPv6 access to/from Google services.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE = 'ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE';
  /**
   * Outbound private IPv6 access from VMs in this subnet to Google services.
   */
  public const PRIVATE_IPV6_GOOGLE_ACCESS_ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE = 'ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE';
  /**
   * Subnet reserved for Global Envoy-based Load Balancing.
   */
  public const PURPOSE_GLOBAL_MANAGED_PROXY = 'GLOBAL_MANAGED_PROXY';
  /**
   * Subnet reserved for Internal HTTP(S) Load Balancing. This is a legacy
   * purpose, please use REGIONAL_MANAGED_PROXY instead.
   */
  public const PURPOSE_INTERNAL_HTTPS_LOAD_BALANCER = 'INTERNAL_HTTPS_LOAD_BALANCER';
  /**
   * Subnetwork will be used for Migration from one peered VPC to another. (a
   * transient state of subnetwork while migrating resources from one project to
   * another).
   */
  public const PURPOSE_PEER_MIGRATION = 'PEER_MIGRATION';
  /**
   * Regular user created or automatically created subnet.
   */
  public const PURPOSE_PRIVATE = 'PRIVATE';
  /**
   * Subnetwork used as source range for Private NAT Gateways.
   */
  public const PURPOSE_PRIVATE_NAT = 'PRIVATE_NAT';
  /**
   * Regular user created or automatically created subnet.
   */
  public const PURPOSE_PRIVATE_RFC_1918 = 'PRIVATE_RFC_1918';
  /**
   * Subnetworks created for Private Service Connect in the producer network.
   */
  public const PURPOSE_PRIVATE_SERVICE_CONNECT = 'PRIVATE_SERVICE_CONNECT';
  /**
   * Subnetwork used for Regional Envoy-based Load Balancing.
   */
  public const PURPOSE_REGIONAL_MANAGED_PROXY = 'REGIONAL_MANAGED_PROXY';
  /**
   * The ACTIVE subnet that is currently used.
   */
  public const ROLE_ACTIVE = 'ACTIVE';
  /**
   * The BACKUP subnet that could be promoted to ACTIVE.
   */
  public const ROLE_BACKUP = 'BACKUP';
  /**
   * New VMs in this subnet can have both IPv4 and IPv6 addresses.
   */
  public const STACK_TYPE_IPV4_IPV6 = 'IPV4_IPV6';
  /**
   * New VMs in this subnet will only be assigned IPv4 addresses.
   */
  public const STACK_TYPE_IPV4_ONLY = 'IPV4_ONLY';
  /**
   * New VMs in this subnet will only  be assigned IPv6 addresses.
   */
  public const STACK_TYPE_IPV6_ONLY = 'IPV6_ONLY';
  /**
   * Subnetwork is being drained.
   */
  public const STATE_DRAINING = 'DRAINING';
  /**
   * Subnetwork is ready for use.
   */
  public const STATE_READY = 'READY';
  protected $collection_key = 'systemReservedInternalIpv6Ranges';
  /**
   * Whether this subnetwork's ranges can conflict with existing static routes.
   * Setting this to true allows this subnetwork's primary and secondary ranges
   * to overlap with (and contain) static routes that have already been
   * configured on the corresponding network.
   *
   * For example if a static route has range 10.1.0.0/16, a subnet range
   * 10.0.0.0/8 could only be created if allow_conflicting_routes=true.
   *
   * Overlapping is only allowed on subnetwork operations; routes whose ranges
   * conflict with this subnetwork's ranges won't be allowed unless
   * route.allow_conflicting_subnetworks is set to true.
   *
   * Typically packets destined to IPs within the subnetwork (which may contain
   * private/sensitive data) are prevented from leaving the virtual network.
   * Setting this field to true will disable this feature.
   *
   * The default value is false and applies to all existing subnetworks and
   * automatically created subnetworks.
   *
   * This field cannot be set to true at resource creation time.
   *
   * @var bool
   */
  public $allowSubnetCidrRoutesOverlap;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource. This field can be set only at resource creation time.
   *
   * @var string
   */
  public $description;
  /**
   * Whether to enable flow logging for this subnetwork. If this field is not
   * explicitly set, it will not appear in get listings. If not set the default
   * behavior is determined by the org policy, if there is no org policy
   * specified, then it will default to disabled. This field isn't supported if
   * the subnet purpose field is set toREGIONAL_MANAGED_PROXY. It is recommended
   * to uselogConfig.enable field instead.
   *
   * @var bool
   */
  public $enableFlowLogs;
  /**
   * The external IPv6 address range that is owned by this subnetwork.
   *
   * @var string
   */
  public $externalIpv6Prefix;
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a Subnetwork. An up-to-date fingerprint must be provided in order
   * to update the Subnetwork, otherwise the request will fail with error 412
   * conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * Subnetwork.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Output only. [Output Only] The gateway address for default routes to reach
   * destination addresses outside this subnetwork.
   *
   * @var string
   */
  public $gatewayAddress;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * The internal IPv6 address range that is owned by this subnetwork.
   *
   * @var string
   */
  public $internalIpv6Prefix;
  /**
   * The range of internal addresses that are owned by this subnetwork. Provide
   * this property when you create the subnetwork. For example,10.0.0.0/8 or
   * 100.64.0.0/10. Ranges must be unique and non-overlapping within a network.
   * Only IPv4 is supported. This field is set at resource creation time. The
   * range can be any range listed in theValid ranges list. The range can be
   * expanded after creation usingexpandIpCidrRange.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * Reference to the source of IP, like a PublicDelegatedPrefix (PDP) for
   * BYOIP. The PDP must be a sub-PDP in EXTERNAL_IPV6_SUBNETWORK_CREATION or
   * INTERNAL_IPV6_SUBNETWORK_CREATION mode.
   *
   * Use one of the following formats to specify a sub-PDP when creating a dual
   * stack or IPv6-only subnetwork with external access using BYOIP:        -
   * Full resource URL, as inhttps://www.googleapis.com/compute/v1/projects/proj
   * ectId/regions/region/publicDelegatedPrefixes/sub-pdp-name    -     Partial
   * URL, as in                        -
   * projects/projectId/regions/region/publicDelegatedPrefixes/sub-pdp-name
   * - regions/region/publicDelegatedPrefixes/sub-pdp-name
   *
   * @var string
   */
  public $ipCollection;
  /**
   * The access type of IPv6 address this subnet holds. It's immutable and can
   * only be specified during creation or the first time the subnet is updated
   * into IPV4_IPV6 dual stack.
   *
   * @var string
   */
  public $ipv6AccessType;
  /**
   * Output only. [Output Only] This field is for internal use.
   *
   * @var string
   */
  public $ipv6CidrRange;
  /**
   * Output only. [Output Only] Possible endpoints of this subnetwork. It can be
   * one of the following:        - VM_ONLY: The subnetwork can be used for
   * creating instances and    IPv6 addresses with VM endpoint type. Such a
   * subnetwork gets external IPv6    ranges from a public delegated prefix and
   * cannot be used to create NetLb.    - VM_AND_FR: The subnetwork can be used
   * for creating both VM    instances and Forwarding Rules. It can also be used
   * to reserve IPv6    addresses with both VM and FR endpoint types. Such a
   * subnetwork gets its    IPv6 range from Google IP Pool directly.
   *
   * @var string
   */
  public $ipv6GceEndpoint;
  /**
   * Output only. [Output Only] Type of the resource. Always compute#subnetwork
   * for Subnetwork resources.
   *
   * @var string
   */
  public $kind;
  protected $logConfigType = SubnetworkLogConfig::class;
  protected $logConfigDataType = '';
  /**
   * The name of the resource, provided by the client when initially creating
   * the resource. The name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * The URL of the network to which this subnetwork belongs, provided by the
   * client when initially creating the subnetwork. This field can be set only
   * at resource creation time.
   *
   * @var string
   */
  public $network;
  protected $paramsType = SubnetworkParams::class;
  protected $paramsDataType = '';
  /**
   * Whether the VMs in this subnet can access Google services without assigned
   * external IP addresses. This field can be both set at resource creation time
   * and updated using setPrivateIpGoogleAccess.
   *
   * @var bool
   */
  public $privateIpGoogleAccess;
  /**
   * This field is for internal use.
   *
   * This field can be both set at resource creation time and updated
   * usingpatch.
   *
   * @var string
   */
  public $privateIpv6GoogleAccess;
  /**
   * @var string
   */
  public $purpose;
  /**
   * URL of the region where the Subnetwork resides. This field can be set only
   * at resource creation time.
   *
   * @var string
   */
  public $region;
  /**
   * The URL of the reserved internal range.
   *
   * @var string
   */
  public $reservedInternalRange;
  /**
   * The role of subnetwork. Currently, this field is only used when purpose is
   * set to GLOBAL_MANAGED_PROXY orREGIONAL_MANAGED_PROXY. The value can be set
   * toACTIVE or BACKUP. An ACTIVE subnetwork is one that is currently being
   * used for Envoy-based load balancers in a region. A BACKUP subnetwork is one
   * that is ready to be promoted to ACTIVE or is currently draining. This field
   * can be updated with a patch request.
   *
   * @var string
   */
  public $role;
  protected $secondaryIpRangesType = SubnetworkSecondaryRange::class;
  protected $secondaryIpRangesDataType = 'array';
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The stack type for the subnet. If set to IPV4_ONLY, new VMs in the subnet
   * are assigned IPv4 addresses only. If set toIPV4_IPV6, new VMs in the subnet
   * can be assigned both IPv4 and IPv6 addresses. If not specified, IPV4_ONLY
   * is used.
   *
   * This field can be both set at resource creation time and updated
   * usingpatch.
   *
   * @var string
   */
  public $stackType;
  /**
   * Output only. [Output Only] The state of the subnetwork, which can be one of
   * the following values:READY: Subnetwork is created and ready to useDRAINING:
   * only applicable to subnetworks that have the purpose set to
   * INTERNAL_HTTPS_LOAD_BALANCER and indicates that connections to the load
   * balancer are being drained. A subnetwork that is draining cannot be used or
   * modified until it reaches a status ofREADY
   *
   * @var string
   */
  public $state;
  /**
   * Output only. [Output Only] The array of external IPv6 network ranges
   * reserved from the subnetwork's external IPv6 range for system use.
   *
   * @var string[]
   */
  public $systemReservedExternalIpv6Ranges;
  /**
   * Output only. [Output Only] The array of internal IPv6 network ranges
   * reserved from the subnetwork's internal IPv6 range for system use.
   *
   * @var string[]
   */
  public $systemReservedInternalIpv6Ranges;
  protected $utilizationDetailsType = SubnetworkUtilizationDetails::class;
  protected $utilizationDetailsDataType = '';

  /**
   * Whether this subnetwork's ranges can conflict with existing static routes.
   * Setting this to true allows this subnetwork's primary and secondary ranges
   * to overlap with (and contain) static routes that have already been
   * configured on the corresponding network.
   *
   * For example if a static route has range 10.1.0.0/16, a subnet range
   * 10.0.0.0/8 could only be created if allow_conflicting_routes=true.
   *
   * Overlapping is only allowed on subnetwork operations; routes whose ranges
   * conflict with this subnetwork's ranges won't be allowed unless
   * route.allow_conflicting_subnetworks is set to true.
   *
   * Typically packets destined to IPs within the subnetwork (which may contain
   * private/sensitive data) are prevented from leaving the virtual network.
   * Setting this field to true will disable this feature.
   *
   * The default value is false and applies to all existing subnetworks and
   * automatically created subnetworks.
   *
   * This field cannot be set to true at resource creation time.
   *
   * @param bool $allowSubnetCidrRoutesOverlap
   */
  public function setAllowSubnetCidrRoutesOverlap($allowSubnetCidrRoutesOverlap)
  {
    $this->allowSubnetCidrRoutesOverlap = $allowSubnetCidrRoutesOverlap;
  }
  /**
   * @return bool
   */
  public function getAllowSubnetCidrRoutesOverlap()
  {
    return $this->allowSubnetCidrRoutesOverlap;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource. This field can be set only at resource creation time.
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
   * Whether to enable flow logging for this subnetwork. If this field is not
   * explicitly set, it will not appear in get listings. If not set the default
   * behavior is determined by the org policy, if there is no org policy
   * specified, then it will default to disabled. This field isn't supported if
   * the subnet purpose field is set toREGIONAL_MANAGED_PROXY. It is recommended
   * to uselogConfig.enable field instead.
   *
   * @param bool $enableFlowLogs
   */
  public function setEnableFlowLogs($enableFlowLogs)
  {
    $this->enableFlowLogs = $enableFlowLogs;
  }
  /**
   * @return bool
   */
  public function getEnableFlowLogs()
  {
    return $this->enableFlowLogs;
  }
  /**
   * The external IPv6 address range that is owned by this subnetwork.
   *
   * @param string $externalIpv6Prefix
   */
  public function setExternalIpv6Prefix($externalIpv6Prefix)
  {
    $this->externalIpv6Prefix = $externalIpv6Prefix;
  }
  /**
   * @return string
   */
  public function getExternalIpv6Prefix()
  {
    return $this->externalIpv6Prefix;
  }
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a Subnetwork. An up-to-date fingerprint must be provided in order
   * to update the Subnetwork, otherwise the request will fail with error 412
   * conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * Subnetwork.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Output only. [Output Only] The gateway address for default routes to reach
   * destination addresses outside this subnetwork.
   *
   * @param string $gatewayAddress
   */
  public function setGatewayAddress($gatewayAddress)
  {
    $this->gatewayAddress = $gatewayAddress;
  }
  /**
   * @return string
   */
  public function getGatewayAddress()
  {
    return $this->gatewayAddress;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
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
   * The internal IPv6 address range that is owned by this subnetwork.
   *
   * @param string $internalIpv6Prefix
   */
  public function setInternalIpv6Prefix($internalIpv6Prefix)
  {
    $this->internalIpv6Prefix = $internalIpv6Prefix;
  }
  /**
   * @return string
   */
  public function getInternalIpv6Prefix()
  {
    return $this->internalIpv6Prefix;
  }
  /**
   * The range of internal addresses that are owned by this subnetwork. Provide
   * this property when you create the subnetwork. For example,10.0.0.0/8 or
   * 100.64.0.0/10. Ranges must be unique and non-overlapping within a network.
   * Only IPv4 is supported. This field is set at resource creation time. The
   * range can be any range listed in theValid ranges list. The range can be
   * expanded after creation usingexpandIpCidrRange.
   *
   * @param string $ipCidrRange
   */
  public function setIpCidrRange($ipCidrRange)
  {
    $this->ipCidrRange = $ipCidrRange;
  }
  /**
   * @return string
   */
  public function getIpCidrRange()
  {
    return $this->ipCidrRange;
  }
  /**
   * Reference to the source of IP, like a PublicDelegatedPrefix (PDP) for
   * BYOIP. The PDP must be a sub-PDP in EXTERNAL_IPV6_SUBNETWORK_CREATION or
   * INTERNAL_IPV6_SUBNETWORK_CREATION mode.
   *
   * Use one of the following formats to specify a sub-PDP when creating a dual
   * stack or IPv6-only subnetwork with external access using BYOIP:        -
   * Full resource URL, as inhttps://www.googleapis.com/compute/v1/projects/proj
   * ectId/regions/region/publicDelegatedPrefixes/sub-pdp-name    -     Partial
   * URL, as in                        -
   * projects/projectId/regions/region/publicDelegatedPrefixes/sub-pdp-name
   * - regions/region/publicDelegatedPrefixes/sub-pdp-name
   *
   * @param string $ipCollection
   */
  public function setIpCollection($ipCollection)
  {
    $this->ipCollection = $ipCollection;
  }
  /**
   * @return string
   */
  public function getIpCollection()
  {
    return $this->ipCollection;
  }
  /**
   * The access type of IPv6 address this subnet holds. It's immutable and can
   * only be specified during creation or the first time the subnet is updated
   * into IPV4_IPV6 dual stack.
   *
   * Accepted values: EXTERNAL, INTERNAL
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
   * Output only. [Output Only] This field is for internal use.
   *
   * @param string $ipv6CidrRange
   */
  public function setIpv6CidrRange($ipv6CidrRange)
  {
    $this->ipv6CidrRange = $ipv6CidrRange;
  }
  /**
   * @return string
   */
  public function getIpv6CidrRange()
  {
    return $this->ipv6CidrRange;
  }
  /**
   * Output only. [Output Only] Possible endpoints of this subnetwork. It can be
   * one of the following:        - VM_ONLY: The subnetwork can be used for
   * creating instances and    IPv6 addresses with VM endpoint type. Such a
   * subnetwork gets external IPv6    ranges from a public delegated prefix and
   * cannot be used to create NetLb.    - VM_AND_FR: The subnetwork can be used
   * for creating both VM    instances and Forwarding Rules. It can also be used
   * to reserve IPv6    addresses with both VM and FR endpoint types. Such a
   * subnetwork gets its    IPv6 range from Google IP Pool directly.
   *
   * Accepted values: VM_AND_FR, VM_ONLY
   *
   * @param self::IPV6_GCE_ENDPOINT_* $ipv6GceEndpoint
   */
  public function setIpv6GceEndpoint($ipv6GceEndpoint)
  {
    $this->ipv6GceEndpoint = $ipv6GceEndpoint;
  }
  /**
   * @return self::IPV6_GCE_ENDPOINT_*
   */
  public function getIpv6GceEndpoint()
  {
    return $this->ipv6GceEndpoint;
  }
  /**
   * Output only. [Output Only] Type of the resource. Always compute#subnetwork
   * for Subnetwork resources.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * This field denotes the VPC flow logging options for this subnetwork. If
   * logging is enabled, logs are exported to Cloud Logging.
   *
   * @param SubnetworkLogConfig $logConfig
   */
  public function setLogConfig(SubnetworkLogConfig $logConfig)
  {
    $this->logConfig = $logConfig;
  }
  /**
   * @return SubnetworkLogConfig
   */
  public function getLogConfig()
  {
    return $this->logConfig;
  }
  /**
   * The name of the resource, provided by the client when initially creating
   * the resource. The name must be 1-63 characters long, and comply
   * withRFC1035. Specifically, the name must be 1-63 characters long and match
   * the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first
   * character must be a lowercase letter, and all following characters must be
   * a dash, lowercase letter, or digit, except the last character, which cannot
   * be a dash.
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
   * The URL of the network to which this subnetwork belongs, provided by the
   * client when initially creating the subnetwork. This field can be set only
   * at resource creation time.
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
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param SubnetworkParams $params
   */
  public function setParams(SubnetworkParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return SubnetworkParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Whether the VMs in this subnet can access Google services without assigned
   * external IP addresses. This field can be both set at resource creation time
   * and updated using setPrivateIpGoogleAccess.
   *
   * @param bool $privateIpGoogleAccess
   */
  public function setPrivateIpGoogleAccess($privateIpGoogleAccess)
  {
    $this->privateIpGoogleAccess = $privateIpGoogleAccess;
  }
  /**
   * @return bool
   */
  public function getPrivateIpGoogleAccess()
  {
    return $this->privateIpGoogleAccess;
  }
  /**
   * This field is for internal use.
   *
   * This field can be both set at resource creation time and updated
   * usingpatch.
   *
   * Accepted values: DISABLE_GOOGLE_ACCESS,
   * ENABLE_BIDIRECTIONAL_ACCESS_TO_GOOGLE, ENABLE_OUTBOUND_VM_ACCESS_TO_GOOGLE
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
   * @param self::PURPOSE_* $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return self::PURPOSE_*
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
  /**
   * URL of the region where the Subnetwork resides. This field can be set only
   * at resource creation time.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * The URL of the reserved internal range.
   *
   * @param string $reservedInternalRange
   */
  public function setReservedInternalRange($reservedInternalRange)
  {
    $this->reservedInternalRange = $reservedInternalRange;
  }
  /**
   * @return string
   */
  public function getReservedInternalRange()
  {
    return $this->reservedInternalRange;
  }
  /**
   * The role of subnetwork. Currently, this field is only used when purpose is
   * set to GLOBAL_MANAGED_PROXY orREGIONAL_MANAGED_PROXY. The value can be set
   * toACTIVE or BACKUP. An ACTIVE subnetwork is one that is currently being
   * used for Envoy-based load balancers in a region. A BACKUP subnetwork is one
   * that is ready to be promoted to ACTIVE or is currently draining. This field
   * can be updated with a patch request.
   *
   * Accepted values: ACTIVE, BACKUP
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * An array of configurations for secondary IP ranges for VM instances
   * contained in this subnetwork. The primary IP of such VM must belong to the
   * primary ipCidrRange of the subnetwork. The alias IPs may belong to either
   * primary or secondary ranges. This field can be updated with apatch request.
   *
   * @param SubnetworkSecondaryRange[] $secondaryIpRanges
   */
  public function setSecondaryIpRanges($secondaryIpRanges)
  {
    $this->secondaryIpRanges = $secondaryIpRanges;
  }
  /**
   * @return SubnetworkSecondaryRange[]
   */
  public function getSecondaryIpRanges()
  {
    return $this->secondaryIpRanges;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
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
   * The stack type for the subnet. If set to IPV4_ONLY, new VMs in the subnet
   * are assigned IPv4 addresses only. If set toIPV4_IPV6, new VMs in the subnet
   * can be assigned both IPv4 and IPv6 addresses. If not specified, IPV4_ONLY
   * is used.
   *
   * This field can be both set at resource creation time and updated
   * usingpatch.
   *
   * Accepted values: IPV4_IPV6, IPV4_ONLY, IPV6_ONLY
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
   * Output only. [Output Only] The state of the subnetwork, which can be one of
   * the following values:READY: Subnetwork is created and ready to useDRAINING:
   * only applicable to subnetworks that have the purpose set to
   * INTERNAL_HTTPS_LOAD_BALANCER and indicates that connections to the load
   * balancer are being drained. A subnetwork that is draining cannot be used or
   * modified until it reaches a status ofREADY
   *
   * Accepted values: DRAINING, READY
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. [Output Only] The array of external IPv6 network ranges
   * reserved from the subnetwork's external IPv6 range for system use.
   *
   * @param string[] $systemReservedExternalIpv6Ranges
   */
  public function setSystemReservedExternalIpv6Ranges($systemReservedExternalIpv6Ranges)
  {
    $this->systemReservedExternalIpv6Ranges = $systemReservedExternalIpv6Ranges;
  }
  /**
   * @return string[]
   */
  public function getSystemReservedExternalIpv6Ranges()
  {
    return $this->systemReservedExternalIpv6Ranges;
  }
  /**
   * Output only. [Output Only] The array of internal IPv6 network ranges
   * reserved from the subnetwork's internal IPv6 range for system use.
   *
   * @param string[] $systemReservedInternalIpv6Ranges
   */
  public function setSystemReservedInternalIpv6Ranges($systemReservedInternalIpv6Ranges)
  {
    $this->systemReservedInternalIpv6Ranges = $systemReservedInternalIpv6Ranges;
  }
  /**
   * @return string[]
   */
  public function getSystemReservedInternalIpv6Ranges()
  {
    return $this->systemReservedInternalIpv6Ranges;
  }
  /**
   * Output only. [Output Only] The current IP utilization of all subnetwork
   * ranges. Contains the total number of allocated and free IPs in each range.
   *
   * @param SubnetworkUtilizationDetails $utilizationDetails
   */
  public function setUtilizationDetails(SubnetworkUtilizationDetails $utilizationDetails)
  {
    $this->utilizationDetails = $utilizationDetails;
  }
  /**
   * @return SubnetworkUtilizationDetails
   */
  public function getUtilizationDetails()
  {
    return $this->utilizationDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subnetwork::class, 'Google_Service_Compute_Subnetwork');
