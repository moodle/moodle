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

class ForwardingRule extends \Google\Collection
{
  public const IPP_ROTOCOL_AH = 'AH';
  public const IPP_ROTOCOL_ESP = 'ESP';
  public const IPP_ROTOCOL_ICMP = 'ICMP';
  public const IPP_ROTOCOL_L3_DEFAULT = 'L3_DEFAULT';
  public const IPP_ROTOCOL_SCTP = 'SCTP';
  public const IPP_ROTOCOL_TCP = 'TCP';
  public const IPP_ROTOCOL_UDP = 'UDP';
  public const EXTERNAL_MANAGED_BACKEND_BUCKET_MIGRATION_STATE_PREPARE = 'PREPARE';
  public const EXTERNAL_MANAGED_BACKEND_BUCKET_MIGRATION_STATE_TEST_ALL_TRAFFIC = 'TEST_ALL_TRAFFIC';
  public const EXTERNAL_MANAGED_BACKEND_BUCKET_MIGRATION_STATE_TEST_BY_PERCENTAGE = 'TEST_BY_PERCENTAGE';
  public const IP_VERSION_IPV4 = 'IPV4';
  public const IP_VERSION_IPV6 = 'IPV6';
  public const IP_VERSION_UNSPECIFIED_VERSION = 'UNSPECIFIED_VERSION';
  public const LOAD_BALANCING_SCHEME_EXTERNAL = 'EXTERNAL';
  public const LOAD_BALANCING_SCHEME_EXTERNAL_MANAGED = 'EXTERNAL_MANAGED';
  public const LOAD_BALANCING_SCHEME_INTERNAL = 'INTERNAL';
  public const LOAD_BALANCING_SCHEME_INTERNAL_MANAGED = 'INTERNAL_MANAGED';
  public const LOAD_BALANCING_SCHEME_INTERNAL_SELF_MANAGED = 'INTERNAL_SELF_MANAGED';
  public const LOAD_BALANCING_SCHEME_INVALID = 'INVALID';
  /**
   * Public internet quality with fixed bandwidth.
   */
  public const NETWORK_TIER_FIXED_STANDARD = 'FIXED_STANDARD';
  /**
   * High quality, Google-grade network tier, support for all networking
   * products.
   */
  public const NETWORK_TIER_PREMIUM = 'PREMIUM';
  /**
   * Public internet quality, only limited support for other networking
   * products.
   */
  public const NETWORK_TIER_STANDARD = 'STANDARD';
  /**
   * (Output only) Temporary tier for FIXED_STANDARD when fixed standard tier is
   * expired or not configured.
   */
  public const NETWORK_TIER_STANDARD_OVERRIDES_FIXED_STANDARD = 'STANDARD_OVERRIDES_FIXED_STANDARD';
  /**
   * The connection has been accepted by the producer.
   */
  public const PSC_CONNECTION_STATUS_ACCEPTED = 'ACCEPTED';
  /**
   * The connection has been closed by the producer and will not serve traffic
   * going forward.
   */
  public const PSC_CONNECTION_STATUS_CLOSED = 'CLOSED';
  /**
   * The connection has been accepted by the producer, but the producer needs to
   * take further action before the forwarding rule can serve traffic.
   */
  public const PSC_CONNECTION_STATUS_NEEDS_ATTENTION = 'NEEDS_ATTENTION';
  /**
   * The connection is pending acceptance by the producer.
   */
  public const PSC_CONNECTION_STATUS_PENDING = 'PENDING';
  /**
   * The connection has been rejected by the producer.
   */
  public const PSC_CONNECTION_STATUS_REJECTED = 'REJECTED';
  public const PSC_CONNECTION_STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  protected $collection_key = 'sourceIpRanges';
  protected $internal_gapi_mappings = [
        "iPAddress" => "IPAddress",
        "iPProtocol" => "IPProtocol",
  ];
  /**
   * IP address for which this forwarding rule accepts traffic. When a client
   * sends traffic to this IP address, the forwarding rule directs the traffic
   * to the referenced target or backendService. While creating a forwarding
   * rule, specifying an IPAddress is required under the following
   * circumstances:
   *
   *        - When the target is set to targetGrpcProxy andvalidateForProxyless
   * is set to true, theIPAddress should be set to 0.0.0.0.    - When the target
   * is a Private Service Connect Google APIs    bundle, you must specify an
   * IPAddress.
   *
   * Otherwise, you can optionally specify an IP address that references an
   * existing static (reserved) IP address resource. When omitted, Google Cloud
   * assigns an ephemeral IP address.
   *
   * Use one of the following formats to specify an IP address while creating a
   * forwarding rule:
   *
   * * IP address number, as in `100.1.2.3` * IPv6 address range, as in
   * `2600:1234::/96` * Full resource URL, as inhttps://www.googleapis.com/compu
   * te/v1/projects/project_id/regions/region/addresses/address-name * Partial
   * URL or by name, as in:        -
   * projects/project_id/regions/region/addresses/address-name    -
   * regions/region/addresses/address-name    - global/addresses/address-name
   * - address-name
   *
   * The forwarding rule's target or backendService, and in most cases, also the
   * loadBalancingScheme, determine the type of IP address that you can use. For
   * detailed information, see [IP address
   * specifications](https://cloud.google.com/load-balancing/docs/forwarding-
   * rule-concepts#ip_address_specifications).
   *
   * When reading an IPAddress, the API always returns the IP address number.
   *
   * @var string
   */
  public $iPAddress;
  /**
   * The IP protocol to which this rule applies.
   *
   * For protocol forwarding, valid options are TCP, UDP, ESP,AH, SCTP, ICMP
   * andL3_DEFAULT.
   *
   * The valid IP protocols are different for different load balancing products
   * as described in [Load balancing features](https://cloud.google.com/load-
   * balancing/docs/features#protocols_from_the_load_balancer_to_the_backends).
   *
   * @var string
   */
  public $iPProtocol;
  /**
   * The ports, portRange, and allPorts fields are mutually exclusive. Only
   * packets addressed to ports in the specified range will be forwarded to the
   * backends configured with this forwarding rule.
   *
   * The allPorts field has the following limitations:        - It requires that
   * the forwarding rule IPProtocol be TCP,    UDP, SCTP, or L3_DEFAULT.    -
   * It's applicable only to the following products: internal passthrough
   * Network Load Balancers, backend service-based external passthrough Network
   * Load Balancers, and internal and external protocol forwarding.    - Set
   * this field to true to allow packets addressed to any port or    packets
   * lacking destination port information (for example, UDP fragments    after
   * the first fragment) to be forwarded to the backends configured with    this
   * forwarding rule. The L3_DEFAULT protocol requiresallPorts be set to true.
   *
   * @var bool
   */
  public $allPorts;
  /**
   * If set to true, clients can access the internal passthrough Network Load
   * Balancers, the regional internal Application Load Balancer, and the
   * regional internal proxy Network Load Balancer from all regions. If false,
   * only allows access from the local region the load balancer is located at.
   * Note that for INTERNAL_MANAGED forwarding rules, this field cannot be
   * changed after the forwarding rule is created.
   *
   * @var bool
   */
  public $allowGlobalAccess;
  /**
   * This is used in PSC consumer ForwardingRule to control whether the PSC
   * endpoint can be accessed from another region.
   *
   * @var bool
   */
  public $allowPscGlobalAccess;
  /**
   * Identifies the backend service to which the forwarding rule sends traffic.
   * Required for internal and external passthrough Network Load Balancers; must
   * be omitted for all other load balancer types.
   *
   * @var string
   */
  public $backendService;
  /**
   * Output only. [Output Only] The URL for the corresponding base forwarding
   * rule. By base forwarding rule, we mean the forwarding rule that has the
   * same IP address, protocol, and port settings with the current forwarding
   * rule, but without sourceIPRanges specified. Always empty if the current
   * forwarding rule does not have sourceIPRanges specified.
   *
   * @var string
   */
  public $baseForwardingRule;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Specifies the canary migration state for the backend buckets attached to
   * this forwarding rule. Possible values are PREPARE, TEST_BY_PERCENTAGE, and
   * TEST_ALL_TRAFFIC.
   *
   * To begin the migration from EXTERNAL to EXTERNAL_MANAGED, the state must be
   * changed to PREPARE. The state must be changed to TEST_ALL_TRAFFIC before
   * the loadBalancingScheme can be changed to EXTERNAL_MANAGED. Optionally, the
   * TEST_BY_PERCENTAGE state can be used to migrate traffic to backend buckets
   * attached to this forwarding rule by percentage using
   * externalManagedBackendBucketMigrationTestingPercentage.
   *
   * Rolling back a migration requires the states to be set in reverse order. So
   * changing the scheme from EXTERNAL_MANAGED to EXTERNAL requires the state to
   * be set to TEST_ALL_TRAFFIC at the same time. Optionally, the
   * TEST_BY_PERCENTAGE state can be used to migrate some traffic back to
   * EXTERNAL or PREPARE can be used to migrate all traffic back to EXTERNAL.
   *
   * @var string
   */
  public $externalManagedBackendBucketMigrationState;
  /**
   * Determines the fraction of requests to backend buckets that should be
   * processed by the global external Application Load Balancer.
   *
   * The value of this field must be in the range [0, 100].
   *
   * This value can only be set if the loadBalancingScheme in the BackendService
   * is set to EXTERNAL (when using the classic Application Load Balancer) and
   * the migration state is TEST_BY_PERCENTAGE.
   *
   * @var float
   */
  public $externalManagedBackendBucketMigrationTestingPercentage;
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a ForwardingRule. Include the fingerprint in patch request to
   * ensure that you do not overwrite changes that were applied from another
   * concurrent request.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * ForwardingRule.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Resource reference of a PublicDelegatedPrefix. The PDP must be a sub-PDP in
   * EXTERNAL_IPV6_FORWARDING_RULE_CREATION mode.
   *
   * Use one of the following formats to specify a sub-PDP when creating an IPv6
   * NetLB forwarding rule using BYOIP: Full resource URL, as inhttps://www.goog
   * leapis.com/compute/v1/projects/project_id/regions/region/publicDelegatedPre
   * fixes/sub-pdp-name Partial URL, as in:        -
   * projects/project_id/regions/region/publicDelegatedPrefixes/sub-pdp-name
   * - regions/region/publicDelegatedPrefixes/sub-pdp-name
   *
   * @var string
   */
  public $ipCollection;
  /**
   * The IP Version that will be used by this forwarding rule.  Valid options
   * are IPV4 or IPV6.
   *
   * @var string
   */
  public $ipVersion;
  /**
   * Indicates whether or not this load balancer can be used as a collector for
   * packet mirroring. To prevent mirroring loops, instances behind this load
   * balancer will not have their traffic mirrored even if aPacketMirroring rule
   * applies to them. This can only be set to true for load balancers that have
   * theirloadBalancingScheme set to INTERNAL.
   *
   * @var bool
   */
  public $isMirroringCollector;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#forwardingRule for forwarding rule resources.
   *
   * @var string
   */
  public $kind;
  /**
   * A fingerprint for the labels being applied to this resource, which is
   * essentially a hash of the labels set used for optimistic locking. The
   * fingerprint is initially generated by Compute Engine and changes after
   * every request to modify or update labels. You must always provide an up-to-
   * date fingerprint hash in order to update or change labels, otherwise the
   * request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * ForwardingRule.
   *
   * @var string
   */
  public $labelFingerprint;
  /**
   * Labels for this resource. These can only be added or modified by
   * thesetLabels method. Each label key/value pair must comply withRFC1035.
   * Label values may be empty.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Specifies the forwarding rule type.
   *
   * For more information about forwarding rules, refer to Forwarding rule
   * concepts.
   *
   * @var string
   */
  public $loadBalancingScheme;
  protected $metadataFiltersType = MetadataFilter::class;
  protected $metadataFiltersDataType = 'array';
  /**
   * Name of the resource; provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * For Private Service Connect forwarding rules that forward traffic to Google
   * APIs, the forwarding rule name must be a 1-20 characters string with
   * lowercase letters and numbers and must start with a letter.
   *
   * @var string
   */
  public $name;
  /**
   * This field is not used for global external load balancing.
   *
   * For internal passthrough Network Load Balancers, this field identifies the
   * network that the load balanced IP should belong to for this forwarding
   * rule. If the subnetwork is specified, the network of the subnetwork will be
   * used. If neither subnetwork nor this field is specified, the default
   * network will be used.
   *
   * For Private Service Connect forwarding rules that forward traffic to Google
   * APIs, a network must be provided.
   *
   * @var string
   */
  public $network;
  /**
   * This signifies the networking tier used for configuring this load balancer
   * and can only take the following values:PREMIUM, STANDARD.
   *
   * For regional ForwardingRule, the valid values are PREMIUM andSTANDARD. For
   * GlobalForwardingRule, the valid value isPREMIUM.
   *
   * If this field is not specified, it is assumed to be PREMIUM. If IPAddress
   * is specified, this value must be equal to the networkTier of the Address.
   *
   * @var string
   */
  public $networkTier;
  /**
   * This is used in PSC consumer ForwardingRule to control whether it should
   * try to auto-generate a DNS zone or not. Non-PSC forwarding rules do not use
   * this field. Once set, this field is not mutable.
   *
   * @var bool
   */
  public $noAutomateDnsZone;
  /**
   * The ports, portRange, and allPorts fields are mutually exclusive. Only
   * packets addressed to ports in the specified range will be forwarded to the
   * backends configured with this forwarding rule.
   *
   * The portRange field has the following limitations:        - It requires
   * that the forwarding rule IPProtocol be TCP,    UDP, or SCTP, and    - It's
   * applicable only to the following products: external passthrough    Network
   * Load Balancers, internal and external proxy Network Load Balancers,
   * internal and external Application Load Balancers, external protocol
   * forwarding, and Classic VPN.    - Some products have restrictions on what
   * ports can be used. See     port specifications for details.
   *
   * For external forwarding rules, two or more forwarding rules cannot use the
   * same [IPAddress, IPProtocol] pair, and cannot have overlappingportRanges.
   *
   * For internal forwarding rules within the same VPC network, two or more
   * forwarding rules cannot use the same [IPAddress, IPProtocol] pair, and
   * cannot have overlapping portRanges.
   *
   * @pattern: \\d+(?:-\\d+)?
   *
   * @var string
   */
  public $portRange;
  /**
   * The ports, portRange, and allPorts fields are mutually exclusive. Only
   * packets addressed to ports in the specified range will be forwarded to the
   * backends configured with this forwarding rule.
   *
   * The ports field has the following limitations:        - It requires that
   * the forwarding rule IPProtocol be TCP,    UDP, or SCTP, and    - It's
   * applicable only to the following products: internal passthrough    Network
   * Load Balancers, backend service-based external passthrough Network    Load
   * Balancers, and internal protocol forwarding.    - You can specify a list of
   * up to five ports by number, separated by    commas. The ports can be
   * contiguous or discontiguous.
   *
   * For external forwarding rules, two or more forwarding rules cannot use the
   * same [IPAddress, IPProtocol] pair if they share at least one port number.
   *
   * For internal forwarding rules within the same VPC network, two or more
   * forwarding rules cannot use the same [IPAddress, IPProtocol] pair if they
   * share at least one port number.
   *
   * @pattern: \\d+(?:-\\d+)?
   *
   * @var string[]
   */
  public $ports;
  /**
   * [Output Only] The PSC connection id of the PSC forwarding rule.
   *
   * @var string
   */
  public $pscConnectionId;
  /**
   * @var string
   */
  public $pscConnectionStatus;
  /**
   * Output only. [Output Only] URL of the region where the regional forwarding
   * rule resides. This field is not applicable to global forwarding rules. You
   * must specify this field as part of the HTTP request URL. It is not settable
   * as a field in the request body.
   *
   * @var string
   */
  public $region;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @var string
   */
  public $selfLinkWithId;
  protected $serviceDirectoryRegistrationsType = ForwardingRuleServiceDirectoryRegistration::class;
  protected $serviceDirectoryRegistrationsDataType = 'array';
  /**
   * An optional prefix to the service name for this forwarding rule. If
   * specified, the prefix is the first label of the fully qualified service
   * name.
   *
   * The label must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the label must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * This field is only used for internal load balancing.
   *
   * @var string
   */
  public $serviceLabel;
  /**
   * [Output Only] The internal fully qualified service name for this forwarding
   * rule.
   *
   * This field is only used for internal load balancing.
   *
   * @var string
   */
  public $serviceName;
  /**
   * If not empty, this forwarding rule will only forward the traffic when the
   * source IP address matches one of the IP addresses or CIDR ranges set here.
   * Note that a forwarding rule can only have up to 64 source IP ranges, and
   * this field can only be used with a regional forwarding rule whose scheme
   * isEXTERNAL. Each source_ip_range entry should be either an IP address (for
   * example, 1.2.3.4) or a CIDR range (for example, 1.2.3.0/24).
   *
   * @var string[]
   */
  public $sourceIpRanges;
  /**
   * This field identifies the subnetwork that the load balanced IP should
   * belong to for this forwarding rule, used with internal load balancers and
   * external passthrough Network Load Balancers with IPv6.
   *
   * If the network specified is in auto subnet mode, this field is optional.
   * However, a subnetwork must be specified if the network is in custom subnet
   * mode or when creating external forwarding rule with IPv6.
   *
   * @var string
   */
  public $subnetwork;
  /**
   * The URL of the target resource to receive the matched traffic.  For
   * regional forwarding rules, this target must be in the same region as the
   * forwarding rule. For global forwarding rules, this target must be a global
   * load balancing resource.
   *
   * The forwarded traffic must be of a type appropriate to the target object.
   * -  For load balancers, see the "Target" column in [Port
   * specifications](https://cloud.google.com/load-balancing/docs/forwarding-
   * rule-concepts#ip_address_specifications).      -  For Private Service
   * Connect forwarding rules that forward traffic to Google APIs, provide the
   * name of a supported Google API bundle:                            -  vpc-sc
   * -  APIs that support VPC Service Controls.              -  all-apis - All
   * supported Google APIs.                        -  For Private Service
   * Connect forwarding rules that forward traffic to managed services, the
   * target must be a service attachment. The target is not mutable once set as
   * a service attachment.
   *
   * @var string
   */
  public $target;

  /**
   * IP address for which this forwarding rule accepts traffic. When a client
   * sends traffic to this IP address, the forwarding rule directs the traffic
   * to the referenced target or backendService. While creating a forwarding
   * rule, specifying an IPAddress is required under the following
   * circumstances:
   *
   *        - When the target is set to targetGrpcProxy andvalidateForProxyless
   * is set to true, theIPAddress should be set to 0.0.0.0.    - When the target
   * is a Private Service Connect Google APIs    bundle, you must specify an
   * IPAddress.
   *
   * Otherwise, you can optionally specify an IP address that references an
   * existing static (reserved) IP address resource. When omitted, Google Cloud
   * assigns an ephemeral IP address.
   *
   * Use one of the following formats to specify an IP address while creating a
   * forwarding rule:
   *
   * * IP address number, as in `100.1.2.3` * IPv6 address range, as in
   * `2600:1234::/96` * Full resource URL, as inhttps://www.googleapis.com/compu
   * te/v1/projects/project_id/regions/region/addresses/address-name * Partial
   * URL or by name, as in:        -
   * projects/project_id/regions/region/addresses/address-name    -
   * regions/region/addresses/address-name    - global/addresses/address-name
   * - address-name
   *
   * The forwarding rule's target or backendService, and in most cases, also the
   * loadBalancingScheme, determine the type of IP address that you can use. For
   * detailed information, see [IP address
   * specifications](https://cloud.google.com/load-balancing/docs/forwarding-
   * rule-concepts#ip_address_specifications).
   *
   * When reading an IPAddress, the API always returns the IP address number.
   *
   * @param string $iPAddress
   */
  public function setIPAddress($iPAddress)
  {
    $this->iPAddress = $iPAddress;
  }
  /**
   * @return string
   */
  public function getIPAddress()
  {
    return $this->iPAddress;
  }
  /**
   * The IP protocol to which this rule applies.
   *
   * For protocol forwarding, valid options are TCP, UDP, ESP,AH, SCTP, ICMP
   * andL3_DEFAULT.
   *
   * The valid IP protocols are different for different load balancing products
   * as described in [Load balancing features](https://cloud.google.com/load-
   * balancing/docs/features#protocols_from_the_load_balancer_to_the_backends).
   *
   * Accepted values: AH, ESP, ICMP, L3_DEFAULT, SCTP, TCP, UDP
   *
   * @param self::IPP_ROTOCOL_* $iPProtocol
   */
  public function setIPProtocol($iPProtocol)
  {
    $this->iPProtocol = $iPProtocol;
  }
  /**
   * @return self::IPP_ROTOCOL_*
   */
  public function getIPProtocol()
  {
    return $this->iPProtocol;
  }
  /**
   * The ports, portRange, and allPorts fields are mutually exclusive. Only
   * packets addressed to ports in the specified range will be forwarded to the
   * backends configured with this forwarding rule.
   *
   * The allPorts field has the following limitations:        - It requires that
   * the forwarding rule IPProtocol be TCP,    UDP, SCTP, or L3_DEFAULT.    -
   * It's applicable only to the following products: internal passthrough
   * Network Load Balancers, backend service-based external passthrough Network
   * Load Balancers, and internal and external protocol forwarding.    - Set
   * this field to true to allow packets addressed to any port or    packets
   * lacking destination port information (for example, UDP fragments    after
   * the first fragment) to be forwarded to the backends configured with    this
   * forwarding rule. The L3_DEFAULT protocol requiresallPorts be set to true.
   *
   * @param bool $allPorts
   */
  public function setAllPorts($allPorts)
  {
    $this->allPorts = $allPorts;
  }
  /**
   * @return bool
   */
  public function getAllPorts()
  {
    return $this->allPorts;
  }
  /**
   * If set to true, clients can access the internal passthrough Network Load
   * Balancers, the regional internal Application Load Balancer, and the
   * regional internal proxy Network Load Balancer from all regions. If false,
   * only allows access from the local region the load balancer is located at.
   * Note that for INTERNAL_MANAGED forwarding rules, this field cannot be
   * changed after the forwarding rule is created.
   *
   * @param bool $allowGlobalAccess
   */
  public function setAllowGlobalAccess($allowGlobalAccess)
  {
    $this->allowGlobalAccess = $allowGlobalAccess;
  }
  /**
   * @return bool
   */
  public function getAllowGlobalAccess()
  {
    return $this->allowGlobalAccess;
  }
  /**
   * This is used in PSC consumer ForwardingRule to control whether the PSC
   * endpoint can be accessed from another region.
   *
   * @param bool $allowPscGlobalAccess
   */
  public function setAllowPscGlobalAccess($allowPscGlobalAccess)
  {
    $this->allowPscGlobalAccess = $allowPscGlobalAccess;
  }
  /**
   * @return bool
   */
  public function getAllowPscGlobalAccess()
  {
    return $this->allowPscGlobalAccess;
  }
  /**
   * Identifies the backend service to which the forwarding rule sends traffic.
   * Required for internal and external passthrough Network Load Balancers; must
   * be omitted for all other load balancer types.
   *
   * @param string $backendService
   */
  public function setBackendService($backendService)
  {
    $this->backendService = $backendService;
  }
  /**
   * @return string
   */
  public function getBackendService()
  {
    return $this->backendService;
  }
  /**
   * Output only. [Output Only] The URL for the corresponding base forwarding
   * rule. By base forwarding rule, we mean the forwarding rule that has the
   * same IP address, protocol, and port settings with the current forwarding
   * rule, but without sourceIPRanges specified. Always empty if the current
   * forwarding rule does not have sourceIPRanges specified.
   *
   * @param string $baseForwardingRule
   */
  public function setBaseForwardingRule($baseForwardingRule)
  {
    $this->baseForwardingRule = $baseForwardingRule;
  }
  /**
   * @return string
   */
  public function getBaseForwardingRule()
  {
    return $this->baseForwardingRule;
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
   * create the resource.
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
   * Specifies the canary migration state for the backend buckets attached to
   * this forwarding rule. Possible values are PREPARE, TEST_BY_PERCENTAGE, and
   * TEST_ALL_TRAFFIC.
   *
   * To begin the migration from EXTERNAL to EXTERNAL_MANAGED, the state must be
   * changed to PREPARE. The state must be changed to TEST_ALL_TRAFFIC before
   * the loadBalancingScheme can be changed to EXTERNAL_MANAGED. Optionally, the
   * TEST_BY_PERCENTAGE state can be used to migrate traffic to backend buckets
   * attached to this forwarding rule by percentage using
   * externalManagedBackendBucketMigrationTestingPercentage.
   *
   * Rolling back a migration requires the states to be set in reverse order. So
   * changing the scheme from EXTERNAL_MANAGED to EXTERNAL requires the state to
   * be set to TEST_ALL_TRAFFIC at the same time. Optionally, the
   * TEST_BY_PERCENTAGE state can be used to migrate some traffic back to
   * EXTERNAL or PREPARE can be used to migrate all traffic back to EXTERNAL.
   *
   * Accepted values: PREPARE, TEST_ALL_TRAFFIC, TEST_BY_PERCENTAGE
   *
   * @param self::EXTERNAL_MANAGED_BACKEND_BUCKET_MIGRATION_STATE_* $externalManagedBackendBucketMigrationState
   */
  public function setExternalManagedBackendBucketMigrationState($externalManagedBackendBucketMigrationState)
  {
    $this->externalManagedBackendBucketMigrationState = $externalManagedBackendBucketMigrationState;
  }
  /**
   * @return self::EXTERNAL_MANAGED_BACKEND_BUCKET_MIGRATION_STATE_*
   */
  public function getExternalManagedBackendBucketMigrationState()
  {
    return $this->externalManagedBackendBucketMigrationState;
  }
  /**
   * Determines the fraction of requests to backend buckets that should be
   * processed by the global external Application Load Balancer.
   *
   * The value of this field must be in the range [0, 100].
   *
   * This value can only be set if the loadBalancingScheme in the BackendService
   * is set to EXTERNAL (when using the classic Application Load Balancer) and
   * the migration state is TEST_BY_PERCENTAGE.
   *
   * @param float $externalManagedBackendBucketMigrationTestingPercentage
   */
  public function setExternalManagedBackendBucketMigrationTestingPercentage($externalManagedBackendBucketMigrationTestingPercentage)
  {
    $this->externalManagedBackendBucketMigrationTestingPercentage = $externalManagedBackendBucketMigrationTestingPercentage;
  }
  /**
   * @return float
   */
  public function getExternalManagedBackendBucketMigrationTestingPercentage()
  {
    return $this->externalManagedBackendBucketMigrationTestingPercentage;
  }
  /**
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a ForwardingRule. Include the fingerprint in patch request to
   * ensure that you do not overwrite changes that were applied from another
   * concurrent request.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * ForwardingRule.
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
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
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
   * Resource reference of a PublicDelegatedPrefix. The PDP must be a sub-PDP in
   * EXTERNAL_IPV6_FORWARDING_RULE_CREATION mode.
   *
   * Use one of the following formats to specify a sub-PDP when creating an IPv6
   * NetLB forwarding rule using BYOIP: Full resource URL, as inhttps://www.goog
   * leapis.com/compute/v1/projects/project_id/regions/region/publicDelegatedPre
   * fixes/sub-pdp-name Partial URL, as in:        -
   * projects/project_id/regions/region/publicDelegatedPrefixes/sub-pdp-name
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
   * The IP Version that will be used by this forwarding rule.  Valid options
   * are IPV4 or IPV6.
   *
   * Accepted values: IPV4, IPV6, UNSPECIFIED_VERSION
   *
   * @param self::IP_VERSION_* $ipVersion
   */
  public function setIpVersion($ipVersion)
  {
    $this->ipVersion = $ipVersion;
  }
  /**
   * @return self::IP_VERSION_*
   */
  public function getIpVersion()
  {
    return $this->ipVersion;
  }
  /**
   * Indicates whether or not this load balancer can be used as a collector for
   * packet mirroring. To prevent mirroring loops, instances behind this load
   * balancer will not have their traffic mirrored even if aPacketMirroring rule
   * applies to them. This can only be set to true for load balancers that have
   * theirloadBalancingScheme set to INTERNAL.
   *
   * @param bool $isMirroringCollector
   */
  public function setIsMirroringCollector($isMirroringCollector)
  {
    $this->isMirroringCollector = $isMirroringCollector;
  }
  /**
   * @return bool
   */
  public function getIsMirroringCollector()
  {
    return $this->isMirroringCollector;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#forwardingRule for forwarding rule resources.
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
   * A fingerprint for the labels being applied to this resource, which is
   * essentially a hash of the labels set used for optimistic locking. The
   * fingerprint is initially generated by Compute Engine and changes after
   * every request to modify or update labels. You must always provide an up-to-
   * date fingerprint hash in order to update or change labels, otherwise the
   * request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve a
   * ForwardingRule.
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
   * Labels for this resource. These can only be added or modified by
   * thesetLabels method. Each label key/value pair must comply withRFC1035.
   * Label values may be empty.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Specifies the forwarding rule type.
   *
   * For more information about forwarding rules, refer to Forwarding rule
   * concepts.
   *
   * Accepted values: EXTERNAL, EXTERNAL_MANAGED, INTERNAL, INTERNAL_MANAGED,
   * INTERNAL_SELF_MANAGED, INVALID
   *
   * @param self::LOAD_BALANCING_SCHEME_* $loadBalancingScheme
   */
  public function setLoadBalancingScheme($loadBalancingScheme)
  {
    $this->loadBalancingScheme = $loadBalancingScheme;
  }
  /**
   * @return self::LOAD_BALANCING_SCHEME_*
   */
  public function getLoadBalancingScheme()
  {
    return $this->loadBalancingScheme;
  }
  /**
   * Opaque filter criteria used by load balancer to restrict routing
   * configuration to a limited set of xDS compliant clients. In their xDS
   * requests to load balancer, xDS clients present node metadata. When there is
   * a match, the relevant configuration is made available to those proxies.
   * Otherwise, all the resources (e.g.TargetHttpProxy, UrlMap) referenced by
   * the ForwardingRule are not visible to those proxies.
   *
   * For each metadataFilter in this list, if itsfilterMatchCriteria is set to
   * MATCH_ANY, at least one of thefilterLabels must match the corresponding
   * label provided in the metadata. If its filterMatchCriteria is set to
   * MATCH_ALL, then all of its filterLabels must match with corresponding
   * labels provided in the metadata. If multiplemetadataFilters are specified,
   * all of them need to be satisfied in order to be considered a match.
   *
   * metadataFilters specified here will be applifed before those specified in
   * the UrlMap that thisForwardingRule references.
   *
   * metadataFilters only applies to Loadbalancers that have their
   * loadBalancingScheme set toINTERNAL_SELF_MANAGED.
   *
   * @param MetadataFilter[] $metadataFilters
   */
  public function setMetadataFilters($metadataFilters)
  {
    $this->metadataFilters = $metadataFilters;
  }
  /**
   * @return MetadataFilter[]
   */
  public function getMetadataFilters()
  {
    return $this->metadataFilters;
  }
  /**
   * Name of the resource; provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * For Private Service Connect forwarding rules that forward traffic to Google
   * APIs, the forwarding rule name must be a 1-20 characters string with
   * lowercase letters and numbers and must start with a letter.
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
   * This field is not used for global external load balancing.
   *
   * For internal passthrough Network Load Balancers, this field identifies the
   * network that the load balanced IP should belong to for this forwarding
   * rule. If the subnetwork is specified, the network of the subnetwork will be
   * used. If neither subnetwork nor this field is specified, the default
   * network will be used.
   *
   * For Private Service Connect forwarding rules that forward traffic to Google
   * APIs, a network must be provided.
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
   * This signifies the networking tier used for configuring this load balancer
   * and can only take the following values:PREMIUM, STANDARD.
   *
   * For regional ForwardingRule, the valid values are PREMIUM andSTANDARD. For
   * GlobalForwardingRule, the valid value isPREMIUM.
   *
   * If this field is not specified, it is assumed to be PREMIUM. If IPAddress
   * is specified, this value must be equal to the networkTier of the Address.
   *
   * Accepted values: FIXED_STANDARD, PREMIUM, STANDARD,
   * STANDARD_OVERRIDES_FIXED_STANDARD
   *
   * @param self::NETWORK_TIER_* $networkTier
   */
  public function setNetworkTier($networkTier)
  {
    $this->networkTier = $networkTier;
  }
  /**
   * @return self::NETWORK_TIER_*
   */
  public function getNetworkTier()
  {
    return $this->networkTier;
  }
  /**
   * This is used in PSC consumer ForwardingRule to control whether it should
   * try to auto-generate a DNS zone or not. Non-PSC forwarding rules do not use
   * this field. Once set, this field is not mutable.
   *
   * @param bool $noAutomateDnsZone
   */
  public function setNoAutomateDnsZone($noAutomateDnsZone)
  {
    $this->noAutomateDnsZone = $noAutomateDnsZone;
  }
  /**
   * @return bool
   */
  public function getNoAutomateDnsZone()
  {
    return $this->noAutomateDnsZone;
  }
  /**
   * The ports, portRange, and allPorts fields are mutually exclusive. Only
   * packets addressed to ports in the specified range will be forwarded to the
   * backends configured with this forwarding rule.
   *
   * The portRange field has the following limitations:        - It requires
   * that the forwarding rule IPProtocol be TCP,    UDP, or SCTP, and    - It's
   * applicable only to the following products: external passthrough    Network
   * Load Balancers, internal and external proxy Network Load Balancers,
   * internal and external Application Load Balancers, external protocol
   * forwarding, and Classic VPN.    - Some products have restrictions on what
   * ports can be used. See     port specifications for details.
   *
   * For external forwarding rules, two or more forwarding rules cannot use the
   * same [IPAddress, IPProtocol] pair, and cannot have overlappingportRanges.
   *
   * For internal forwarding rules within the same VPC network, two or more
   * forwarding rules cannot use the same [IPAddress, IPProtocol] pair, and
   * cannot have overlapping portRanges.
   *
   * @pattern: \\d+(?:-\\d+)?
   *
   * @param string $portRange
   */
  public function setPortRange($portRange)
  {
    $this->portRange = $portRange;
  }
  /**
   * @return string
   */
  public function getPortRange()
  {
    return $this->portRange;
  }
  /**
   * The ports, portRange, and allPorts fields are mutually exclusive. Only
   * packets addressed to ports in the specified range will be forwarded to the
   * backends configured with this forwarding rule.
   *
   * The ports field has the following limitations:        - It requires that
   * the forwarding rule IPProtocol be TCP,    UDP, or SCTP, and    - It's
   * applicable only to the following products: internal passthrough    Network
   * Load Balancers, backend service-based external passthrough Network    Load
   * Balancers, and internal protocol forwarding.    - You can specify a list of
   * up to five ports by number, separated by    commas. The ports can be
   * contiguous or discontiguous.
   *
   * For external forwarding rules, two or more forwarding rules cannot use the
   * same [IPAddress, IPProtocol] pair if they share at least one port number.
   *
   * For internal forwarding rules within the same VPC network, two or more
   * forwarding rules cannot use the same [IPAddress, IPProtocol] pair if they
   * share at least one port number.
   *
   * @pattern: \\d+(?:-\\d+)?
   *
   * @param string[] $ports
   */
  public function setPorts($ports)
  {
    $this->ports = $ports;
  }
  /**
   * @return string[]
   */
  public function getPorts()
  {
    return $this->ports;
  }
  /**
   * [Output Only] The PSC connection id of the PSC forwarding rule.
   *
   * @param string $pscConnectionId
   */
  public function setPscConnectionId($pscConnectionId)
  {
    $this->pscConnectionId = $pscConnectionId;
  }
  /**
   * @return string
   */
  public function getPscConnectionId()
  {
    return $this->pscConnectionId;
  }
  /**
   * @param self::PSC_CONNECTION_STATUS_* $pscConnectionStatus
   */
  public function setPscConnectionStatus($pscConnectionStatus)
  {
    $this->pscConnectionStatus = $pscConnectionStatus;
  }
  /**
   * @return self::PSC_CONNECTION_STATUS_*
   */
  public function getPscConnectionStatus()
  {
    return $this->pscConnectionStatus;
  }
  /**
   * Output only. [Output Only] URL of the region where the regional forwarding
   * rule resides. This field is not applicable to global forwarding rules. You
   * must specify this field as part of the HTTP request URL. It is not settable
   * as a field in the request body.
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
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
  /**
   * Service Directory resources to register this forwarding rule with.
   * Currently, only supports a single Service Directory resource.
   *
   * @param ForwardingRuleServiceDirectoryRegistration[] $serviceDirectoryRegistrations
   */
  public function setServiceDirectoryRegistrations($serviceDirectoryRegistrations)
  {
    $this->serviceDirectoryRegistrations = $serviceDirectoryRegistrations;
  }
  /**
   * @return ForwardingRuleServiceDirectoryRegistration[]
   */
  public function getServiceDirectoryRegistrations()
  {
    return $this->serviceDirectoryRegistrations;
  }
  /**
   * An optional prefix to the service name for this forwarding rule. If
   * specified, the prefix is the first label of the fully qualified service
   * name.
   *
   * The label must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the label must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * This field is only used for internal load balancing.
   *
   * @param string $serviceLabel
   */
  public function setServiceLabel($serviceLabel)
  {
    $this->serviceLabel = $serviceLabel;
  }
  /**
   * @return string
   */
  public function getServiceLabel()
  {
    return $this->serviceLabel;
  }
  /**
   * [Output Only] The internal fully qualified service name for this forwarding
   * rule.
   *
   * This field is only used for internal load balancing.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
  /**
   * If not empty, this forwarding rule will only forward the traffic when the
   * source IP address matches one of the IP addresses or CIDR ranges set here.
   * Note that a forwarding rule can only have up to 64 source IP ranges, and
   * this field can only be used with a regional forwarding rule whose scheme
   * isEXTERNAL. Each source_ip_range entry should be either an IP address (for
   * example, 1.2.3.4) or a CIDR range (for example, 1.2.3.0/24).
   *
   * @param string[] $sourceIpRanges
   */
  public function setSourceIpRanges($sourceIpRanges)
  {
    $this->sourceIpRanges = $sourceIpRanges;
  }
  /**
   * @return string[]
   */
  public function getSourceIpRanges()
  {
    return $this->sourceIpRanges;
  }
  /**
   * This field identifies the subnetwork that the load balanced IP should
   * belong to for this forwarding rule, used with internal load balancers and
   * external passthrough Network Load Balancers with IPv6.
   *
   * If the network specified is in auto subnet mode, this field is optional.
   * However, a subnetwork must be specified if the network is in custom subnet
   * mode or when creating external forwarding rule with IPv6.
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
   * The URL of the target resource to receive the matched traffic.  For
   * regional forwarding rules, this target must be in the same region as the
   * forwarding rule. For global forwarding rules, this target must be a global
   * load balancing resource.
   *
   * The forwarded traffic must be of a type appropriate to the target object.
   * -  For load balancers, see the "Target" column in [Port
   * specifications](https://cloud.google.com/load-balancing/docs/forwarding-
   * rule-concepts#ip_address_specifications).      -  For Private Service
   * Connect forwarding rules that forward traffic to Google APIs, provide the
   * name of a supported Google API bundle:                            -  vpc-sc
   * -  APIs that support VPC Service Controls.              -  all-apis - All
   * supported Google APIs.                        -  For Private Service
   * Connect forwarding rules that forward traffic to managed services, the
   * target must be a service attachment. The target is not mutable once set as
   * a service attachment.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ForwardingRule::class, 'Google_Service_Compute_ForwardingRule');
