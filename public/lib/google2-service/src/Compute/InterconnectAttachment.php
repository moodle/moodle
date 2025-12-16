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

class InterconnectAttachment extends \Google\Collection
{
  /**
   * 100 Gbit/s
   */
  public const BANDWIDTH_BPS_100G = 'BPS_100G';
  /**
   * 100 Mbit/s
   */
  public const BANDWIDTH_BPS_100M = 'BPS_100M';
  /**
   * 10 Gbit/s
   */
  public const BANDWIDTH_BPS_10G = 'BPS_10G';
  /**
   * 1 Gbit/s
   */
  public const BANDWIDTH_BPS_1G = 'BPS_1G';
  /**
   * 200 Mbit/s
   */
  public const BANDWIDTH_BPS_200M = 'BPS_200M';
  /**
   * 20 Gbit/s
   */
  public const BANDWIDTH_BPS_20G = 'BPS_20G';
  /**
   * 2 Gbit/s
   */
  public const BANDWIDTH_BPS_2G = 'BPS_2G';
  /**
   * 300 Mbit/s
   */
  public const BANDWIDTH_BPS_300M = 'BPS_300M';
  /**
   * 400 Mbit/s
   */
  public const BANDWIDTH_BPS_400M = 'BPS_400M';
  /**
   * 500 Mbit/s
   */
  public const BANDWIDTH_BPS_500M = 'BPS_500M';
  /**
   * 50 Gbit/s
   */
  public const BANDWIDTH_BPS_50G = 'BPS_50G';
  /**
   * 50 Mbit/s
   */
  public const BANDWIDTH_BPS_50M = 'BPS_50M';
  /**
   * 5 Gbit/s
   */
  public const BANDWIDTH_BPS_5G = 'BPS_5G';
  public const EDGE_AVAILABILITY_DOMAIN_AVAILABILITY_DOMAIN_1 = 'AVAILABILITY_DOMAIN_1';
  public const EDGE_AVAILABILITY_DOMAIN_AVAILABILITY_DOMAIN_2 = 'AVAILABILITY_DOMAIN_2';
  public const EDGE_AVAILABILITY_DOMAIN_AVAILABILITY_DOMAIN_ANY = 'AVAILABILITY_DOMAIN_ANY';
  /**
   * The interconnect attachment will carry only encrypted traffic that is
   * encrypted by an IPsec device such as HA VPN gateway; VMs cannot directly
   * send traffic to or receive traffic from such an interconnect attachment.
   * To use HA VPN over Cloud Interconnect, the interconnect attachment must be
   * created with this option.
   */
  public const ENCRYPTION_IPSEC = 'IPSEC';
  /**
   * This is the default value, which means the Interconnect Attachment will
   * carry unencrypted traffic. VMs will be able to send traffic to or receive
   * traffic from such interconnect attachment.
   */
  public const ENCRYPTION_NONE = 'NONE';
  /**
   * Indicates that attachment has been turned up and is ready to use.
   */
  public const OPERATIONAL_STATUS_OS_ACTIVE = 'OS_ACTIVE';
  /**
   * Indicates that attachment is not ready to use yet, because turnup is not
   * complete.
   */
  public const OPERATIONAL_STATUS_OS_UNPROVISIONED = 'OS_UNPROVISIONED';
  /**
   * The interconnect attachment can have both IPv4 and IPv6 addresses.
   */
  public const STACK_TYPE_IPV4_IPV6 = 'IPV4_IPV6';
  /**
   * The interconnect attachment will only be assigned IPv4 addresses.
   */
  public const STACK_TYPE_IPV4_ONLY = 'IPV4_ONLY';
  /**
   * Indicates that attachment has been turned up and is ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The attachment was deleted externally and is no longer functional. This
   * could be because the associated Interconnect was wiped out, or because the
   * other side of a Partner attachment was deleted.
   */
  public const STATE_DEFUNCT = 'DEFUNCT';
  /**
   * A PARTNER attachment is in the process of provisioning after a
   * PARTNER_PROVIDER attachment was created that references it.
   */
  public const STATE_PARTNER_REQUEST_RECEIVED = 'PARTNER_REQUEST_RECEIVED';
  /**
   * PARTNER or PARTNER_PROVIDER attachment that is waiting for the customer to
   * activate.
   */
  public const STATE_PENDING_CUSTOMER = 'PENDING_CUSTOMER';
  /**
   * A newly created PARTNER attachment that has not yet been configured on the
   * Partner side.
   */
  public const STATE_PENDING_PARTNER = 'PENDING_PARTNER';
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that attachment is not ready to use yet, because turnup is not
   * complete.
   */
  public const STATE_UNPROVISIONED = 'UNPROVISIONED';
  /**
   * Attachment to a dedicated interconnect.
   */
  public const TYPE_DEDICATED = 'DEDICATED';
  /**
   * Attachment to a dedicated interconnect, forwarding L2 packets.
   */
  public const TYPE_L2_DEDICATED = 'L2_DEDICATED';
  /**
   * Attachment to a partner interconnect, created by the customer.
   */
  public const TYPE_PARTNER = 'PARTNER';
  /**
   * Attachment to a partner interconnect, created by the partner.
   */
  public const TYPE_PARTNER_PROVIDER = 'PARTNER_PROVIDER';
  protected $collection_key = 'ipsecInternalAddresses';
  /**
   * Determines whether this Attachment will carry packets. Not present for
   * PARTNER_PROVIDER.
   *
   * @var bool
   */
  public $adminEnabled;
  /**
   * Output only. [Output Only] URL of the AttachmentGroup that includes this
   * Attachment.
   *
   * @var string
   */
  public $attachmentGroup;
  /**
   * Provisioned bandwidth capacity for the interconnect attachment. For
   * attachments of type DEDICATED, the user can set the bandwidth. For
   * attachments of type PARTNER, the Google Partner that is operating the
   * interconnect must set the bandwidth. Output only for PARTNER type, mutable
   * for PARTNER_PROVIDER and DEDICATED, and can take one of the following
   * values:        - BPS_50M: 50 Mbit/s    - BPS_100M: 100 Mbit/s    -
   * BPS_200M: 200 Mbit/s    - BPS_300M: 300 Mbit/s    - BPS_400M: 400 Mbit/s
   * - BPS_500M: 500 Mbit/s    - BPS_1G: 1 Gbit/s    - BPS_2G: 2 Gbit/s    -
   * BPS_5G: 5 Gbit/s    - BPS_10G: 10 Gbit/s    - BPS_20G: 20 Gbit/s    -
   * BPS_50G: 50 Gbit/s    - BPS_100G: 100 Gbit/s
   *
   * @var string
   */
  public $bandwidth;
  /**
   * Single IPv4 address + prefix length to be configured on the cloud router
   * interface for this interconnect attachment.        - Both
   * candidate_cloud_router_ip_address and
   * candidate_customer_router_ip_address fields must be set or both must be
   * unset.    - Prefix length of both candidate_cloud_router_ip_address and
   * candidate_customer_router_ip_address must be the same.    - Max prefix
   * length is 31.
   *
   * @var string
   */
  public $candidateCloudRouterIpAddress;
  /**
   * Single IPv6 address + prefix length to be configured on the cloud router
   * interface for this interconnect attachment.        - Both
   * candidate_cloud_router_ipv6_address and
   * candidate_customer_router_ipv6_address fields must be set or both must be
   * unset.    - Prefix length of both candidate_cloud_router_ipv6_address and
   * candidate_customer_router_ipv6_address must be the same.    - Max prefix
   * length is 126.
   *
   * @var string
   */
  public $candidateCloudRouterIpv6Address;
  /**
   * Single IPv4 address + prefix length to be configured on the customer router
   * interface for this interconnect attachment.
   *
   * @var string
   */
  public $candidateCustomerRouterIpAddress;
  /**
   * Single IPv6 address + prefix length to be configured on the customer router
   * interface for this interconnect attachment.
   *
   * @var string
   */
  public $candidateCustomerRouterIpv6Address;
  /**
   * This field is not available.
   *
   * @var string[]
   */
  public $candidateIpv6Subnets;
  /**
   * Input only. Up to 16 candidate prefixes that can be used to restrict the
   * allocation of cloudRouterIpAddress and customerRouterIpAddress for this
   * attachment. All prefixes must be within link-local address space
   * (169.254.0.0/16) and must be /29 or shorter (/28, /27, etc). Google will
   * attempt to select an unused /29 from the supplied candidate prefix(es). The
   * request will fail if all possible /29s are in use on Google's edge. If not
   * supplied, Google will randomly select an unused /29 from all of link-local
   * space.
   *
   * @var string[]
   */
  public $candidateSubnets;
  /**
   * Output only. [Output Only] IPv4 address + prefix length to be configured on
   * Cloud Router Interface for this interconnect attachment.
   *
   * @var string
   */
  public $cloudRouterIpAddress;
  /**
   * Output only. [Output Only] IPv6 address + prefix length to be configured on
   * Cloud Router Interface for this interconnect attachment.
   *
   * @var string
   */
  public $cloudRouterIpv6Address;
  /**
   * This field is not available.
   *
   * @var string
   */
  public $cloudRouterIpv6InterfaceId;
  protected $configurationConstraintsType = InterconnectAttachmentConfigurationConstraints::class;
  protected $configurationConstraintsDataType = '';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * Output only. [Output Only] IPv4 address + prefix length to be configured on
   * the customer router subinterface for this interconnect attachment.
   *
   * @var string
   */
  public $customerRouterIpAddress;
  /**
   * Output only. [Output Only] IPv6 address + prefix length to be configured on
   * the customer router subinterface for this interconnect attachment.
   *
   * @var string
   */
  public $customerRouterIpv6Address;
  /**
   * This field is not available.
   *
   * @var string
   */
  public $customerRouterIpv6InterfaceId;
  /**
   * Output only. [Output Only] Dataplane version for this
   * InterconnectAttachment. This field is only present for Dataplane version 2
   * and higher. Absence of this field in the API output indicates that the
   * Dataplane is version 1.
   *
   * @var int
   */
  public $dataplaneVersion;
  /**
   * An optional description of this resource.
   *
   * @var string
   */
  public $description;
  /**
   * Input only. Desired availability domain for the attachment. Only available
   * for type PARTNER, at creation time, and can take one of the following
   * values:        - AVAILABILITY_DOMAIN_ANY    - AVAILABILITY_DOMAIN_1    -
   * AVAILABILITY_DOMAIN_2
   *
   * For improved reliability, customers should configure a pair of attachments,
   * one per availability domain. The selected availability domain will be
   * provided to the Partner via the pairing key, so that the provisioned
   * circuit will lie in the specified domain. If not specified, the value will
   * default to AVAILABILITY_DOMAIN_ANY.
   *
   * @var string
   */
  public $edgeAvailabilityDomain;
  /**
   * Indicates the user-supplied encryption option of this VLAN attachment
   * (interconnectAttachment). Can only be specified at attachment creation for
   * PARTNER or DEDICATED attachments. Possible values are:        - NONE - This
   * is the default value, which means that the    VLAN attachment carries
   * unencrypted traffic. VMs are able to send    traffic to, or receive traffic
   * from, such a VLAN attachment.    - IPSEC - The VLAN attachment carries only
   * encrypted    traffic that is encrypted by an IPsec device, such as an HA
   * VPN gateway or    third-party IPsec VPN. VMs cannot directly send traffic
   * to, or receive    traffic from, such a VLAN attachment. To use *HA VPN over
   * Cloud    Interconnect*, the VLAN attachment must be created with this
   * option.
   *
   * @var string
   */
  public $encryption;
  /**
   * Output only. [Output Only] Google reference ID, to be used when raising
   * support tickets with Google or otherwise to debug backend connectivity
   * issues. [Deprecated] This field is not used.
   *
   * @deprecated
   * @var string
   */
  public $googleReferenceId;
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * URL of the underlying Interconnect object that this attachment's traffic
   * will traverse through.
   *
   * @var string
   */
  public $interconnect;
  /**
   * A list of URLs of addresses that have been reserved for the VLAN
   * attachment. Used only for the VLAN attachment that has the encryption
   * option as IPSEC. The addresses must be regional internal IP address ranges.
   * When creating an HA VPN gateway over the VLAN attachment, if the attachment
   * is configured to use a regional internal IP address, then the VPN gateway's
   * IP address is allocated from the IP address range specified here. For
   * example, if the HA VPN gateway's interface 0 is paired to this VLAN
   * attachment, then a regional internal IP address for the VPN gateway
   * interface 0 will be allocated from the IP address specified for this VLAN
   * attachment. If this field is not specified when creating the VLAN
   * attachment, then later on when creating an HA VPN gateway on this VLAN
   * attachment, the HA VPN gateway's IP address is allocated from the regional
   * external IP address pool.
   *
   * @var string[]
   */
  public $ipsecInternalAddresses;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#interconnectAttachment for interconnect attachments.
   *
   * @var string
   */
  public $kind;
  protected $l2ForwardingType = InterconnectAttachmentL2Forwarding::class;
  protected $l2ForwardingDataType = '';
  /**
   * A fingerprint for the labels being applied to this InterconnectAttachment,
   * which is essentially a hash of the labels set used for optimistic locking.
   * The fingerprint is initially generated by Compute Engine and changes after
   * every request to modify or update labels. You must always provide an up-to-
   * date fingerprint hash in order to update or change labels, otherwise the
   * request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an
   * InterconnectAttachment.
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
   * Maximum Transmission Unit (MTU), in bytes, of packets passing through this
   * interconnect attachment. Valid values are 1440, 1460, 1500, and 8896. If
   * not specified, the value will default to 1440.
   *
   * @var int
   */
  public $mtu;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. [Output Only] The current status of whether or not this
   * interconnect attachment is functional, which can take one of the following
   * values:        - OS_ACTIVE: The attachment has been turned up and is ready
   * to    use.     - OS_UNPROVISIONED: The attachment is not ready to use yet,
   * because turnup is not complete.
   *
   * @var string
   */
  public $operationalStatus;
  /**
   * [Output only for type PARTNER. Input only for PARTNER_PROVIDER. Not present
   * for DEDICATED]. The opaque identifier of a PARTNER attachment used to
   * initiate provisioning with a selected partner. Of the form
   * "XXXXX/region/domain"
   *
   * @var string
   */
  public $pairingKey;
  protected $paramsType = InterconnectAttachmentParams::class;
  protected $paramsDataType = '';
  /**
   * Optional BGP ASN for the router supplied by a Layer 3 Partner if they
   * configured BGP on behalf of the customer. Output only for PARTNER type,
   * input only for PARTNER_PROVIDER, not available for DEDICATED.
   *
   * @var string
   */
  public $partnerAsn;
  protected $partnerMetadataType = InterconnectAttachmentPartnerMetadata::class;
  protected $partnerMetadataDataType = '';
  protected $privateInterconnectInfoType = InterconnectAttachmentPrivateInfo::class;
  protected $privateInterconnectInfoDataType = '';
  /**
   * Output only. [Output Only] URL of the region where the regional
   * interconnect attachment resides. You must specify this field as part of the
   * HTTP request URL. It is not settable as a field in the request body.
   *
   * @var string
   */
  public $region;
  /**
   * Output only. [Output Only] If the attachment is on a Cross-Cloud
   * Interconnect connection, this field contains the interconnect's remote
   * location service provider. Example values: "Amazon Web Services" "Microsoft
   * Azure".
   *
   * The field is set only for attachments on Cross-Cloud Interconnect
   * connections. Its value is copied from the InterconnectRemoteLocation
   * remoteService field.
   *
   * @var string
   */
  public $remoteService;
  /**
   * URL of the Cloud Router to be used for dynamic routing. This router must be
   * in the same region as this InterconnectAttachment. The
   * InterconnectAttachment will automatically connect the Interconnect to the
   * network & region within which the Cloud Router is configured.
   *
   * @var string
   */
  public $router;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The stack type for this interconnect attachment to identify whether the
   * IPv6 feature is enabled or not. If not specified, IPV4_ONLY will be used.
   *
   * This field can be both set at interconnect attachments creation and update
   * interconnect attachment operations.
   *
   * @var string
   */
  public $stackType;
  /**
   * Output only. [Output Only] The current state of this attachment's
   * functionality. Enum values ACTIVE and UNPROVISIONED are shared by
   * DEDICATED/PRIVATE, PARTNER, and PARTNER_PROVIDER interconnect attachments,
   * while enum values PENDING_PARTNER, PARTNER_REQUEST_RECEIVED, and
   * PENDING_CUSTOMER are used for only PARTNER and PARTNER_PROVIDER
   * interconnect attachments. This state can take one of the following values:
   * - ACTIVE: The attachment has been turned up and is ready to use.    -
   * UNPROVISIONED: The attachment is not ready to use yet, because turnup    is
   * not complete.    - PENDING_PARTNER: A newly-created PARTNER attachment that
   * has not yet    been configured on the Partner side.    -
   * PARTNER_REQUEST_RECEIVED: A PARTNER attachment is in the process of
   * provisioning after a PARTNER_PROVIDER attachment was created that
   * references it.     - PENDING_CUSTOMER: A PARTNER or PARTNER_PROVIDER
   * attachment that is waiting for a customer to activate it.     - DEFUNCT:
   * The attachment was deleted externally and is no longer functional. This
   * could be because the associated Interconnect was removed, or because the
   * other side of a Partner attachment was deleted.
   *
   * @var string
   */
  public $state;
  /**
   * Input only. Length of the IPv4 subnet mask. Allowed values:             -
   * 29 (default)     - 30
   *
   * The default value is 29, except for Cross-Cloud Interconnect connections
   * that use an InterconnectRemoteLocation with a
   * constraints.subnetLengthRange.min equal to 30. For example, connections
   * that use an Azure remote location fall into this category. In these cases,
   * the default value is 30, and requesting 29 returns an error.
   *
   * Where both 29 and 30 are allowed, 29 is preferred, because it gives Google
   * Cloud Support more debugging visibility.
   *
   * @var int
   */
  public $subnetLength;
  /**
   * The type of interconnect attachment this is, which can take one of the
   * following values:        - DEDICATED: an attachment to a Dedicated
   * Interconnect.    - PARTNER: an attachment to a Partner Interconnect,
   * created by the    customer.    - PARTNER_PROVIDER: an attachment to a
   * Partner Interconnect, created by    the partner.
   *
   * - L2_DEDICATED: a L2 attachment to a Dedicated Interconnect.
   *
   * @var string
   */
  public $type;
  /**
   * The IEEE 802.1Q VLAN tag for this attachment, in the range 2-4093. Only
   * specified at creation time.
   *
   * @var int
   */
  public $vlanTag8021q;

  /**
   * Determines whether this Attachment will carry packets. Not present for
   * PARTNER_PROVIDER.
   *
   * @param bool $adminEnabled
   */
  public function setAdminEnabled($adminEnabled)
  {
    $this->adminEnabled = $adminEnabled;
  }
  /**
   * @return bool
   */
  public function getAdminEnabled()
  {
    return $this->adminEnabled;
  }
  /**
   * Output only. [Output Only] URL of the AttachmentGroup that includes this
   * Attachment.
   *
   * @param string $attachmentGroup
   */
  public function setAttachmentGroup($attachmentGroup)
  {
    $this->attachmentGroup = $attachmentGroup;
  }
  /**
   * @return string
   */
  public function getAttachmentGroup()
  {
    return $this->attachmentGroup;
  }
  /**
   * Provisioned bandwidth capacity for the interconnect attachment. For
   * attachments of type DEDICATED, the user can set the bandwidth. For
   * attachments of type PARTNER, the Google Partner that is operating the
   * interconnect must set the bandwidth. Output only for PARTNER type, mutable
   * for PARTNER_PROVIDER and DEDICATED, and can take one of the following
   * values:        - BPS_50M: 50 Mbit/s    - BPS_100M: 100 Mbit/s    -
   * BPS_200M: 200 Mbit/s    - BPS_300M: 300 Mbit/s    - BPS_400M: 400 Mbit/s
   * - BPS_500M: 500 Mbit/s    - BPS_1G: 1 Gbit/s    - BPS_2G: 2 Gbit/s    -
   * BPS_5G: 5 Gbit/s    - BPS_10G: 10 Gbit/s    - BPS_20G: 20 Gbit/s    -
   * BPS_50G: 50 Gbit/s    - BPS_100G: 100 Gbit/s
   *
   * Accepted values: BPS_100G, BPS_100M, BPS_10G, BPS_1G, BPS_200M, BPS_20G,
   * BPS_2G, BPS_300M, BPS_400M, BPS_500M, BPS_50G, BPS_50M, BPS_5G
   *
   * @param self::BANDWIDTH_* $bandwidth
   */
  public function setBandwidth($bandwidth)
  {
    $this->bandwidth = $bandwidth;
  }
  /**
   * @return self::BANDWIDTH_*
   */
  public function getBandwidth()
  {
    return $this->bandwidth;
  }
  /**
   * Single IPv4 address + prefix length to be configured on the cloud router
   * interface for this interconnect attachment.        - Both
   * candidate_cloud_router_ip_address and
   * candidate_customer_router_ip_address fields must be set or both must be
   * unset.    - Prefix length of both candidate_cloud_router_ip_address and
   * candidate_customer_router_ip_address must be the same.    - Max prefix
   * length is 31.
   *
   * @param string $candidateCloudRouterIpAddress
   */
  public function setCandidateCloudRouterIpAddress($candidateCloudRouterIpAddress)
  {
    $this->candidateCloudRouterIpAddress = $candidateCloudRouterIpAddress;
  }
  /**
   * @return string
   */
  public function getCandidateCloudRouterIpAddress()
  {
    return $this->candidateCloudRouterIpAddress;
  }
  /**
   * Single IPv6 address + prefix length to be configured on the cloud router
   * interface for this interconnect attachment.        - Both
   * candidate_cloud_router_ipv6_address and
   * candidate_customer_router_ipv6_address fields must be set or both must be
   * unset.    - Prefix length of both candidate_cloud_router_ipv6_address and
   * candidate_customer_router_ipv6_address must be the same.    - Max prefix
   * length is 126.
   *
   * @param string $candidateCloudRouterIpv6Address
   */
  public function setCandidateCloudRouterIpv6Address($candidateCloudRouterIpv6Address)
  {
    $this->candidateCloudRouterIpv6Address = $candidateCloudRouterIpv6Address;
  }
  /**
   * @return string
   */
  public function getCandidateCloudRouterIpv6Address()
  {
    return $this->candidateCloudRouterIpv6Address;
  }
  /**
   * Single IPv4 address + prefix length to be configured on the customer router
   * interface for this interconnect attachment.
   *
   * @param string $candidateCustomerRouterIpAddress
   */
  public function setCandidateCustomerRouterIpAddress($candidateCustomerRouterIpAddress)
  {
    $this->candidateCustomerRouterIpAddress = $candidateCustomerRouterIpAddress;
  }
  /**
   * @return string
   */
  public function getCandidateCustomerRouterIpAddress()
  {
    return $this->candidateCustomerRouterIpAddress;
  }
  /**
   * Single IPv6 address + prefix length to be configured on the customer router
   * interface for this interconnect attachment.
   *
   * @param string $candidateCustomerRouterIpv6Address
   */
  public function setCandidateCustomerRouterIpv6Address($candidateCustomerRouterIpv6Address)
  {
    $this->candidateCustomerRouterIpv6Address = $candidateCustomerRouterIpv6Address;
  }
  /**
   * @return string
   */
  public function getCandidateCustomerRouterIpv6Address()
  {
    return $this->candidateCustomerRouterIpv6Address;
  }
  /**
   * This field is not available.
   *
   * @param string[] $candidateIpv6Subnets
   */
  public function setCandidateIpv6Subnets($candidateIpv6Subnets)
  {
    $this->candidateIpv6Subnets = $candidateIpv6Subnets;
  }
  /**
   * @return string[]
   */
  public function getCandidateIpv6Subnets()
  {
    return $this->candidateIpv6Subnets;
  }
  /**
   * Input only. Up to 16 candidate prefixes that can be used to restrict the
   * allocation of cloudRouterIpAddress and customerRouterIpAddress for this
   * attachment. All prefixes must be within link-local address space
   * (169.254.0.0/16) and must be /29 or shorter (/28, /27, etc). Google will
   * attempt to select an unused /29 from the supplied candidate prefix(es). The
   * request will fail if all possible /29s are in use on Google's edge. If not
   * supplied, Google will randomly select an unused /29 from all of link-local
   * space.
   *
   * @param string[] $candidateSubnets
   */
  public function setCandidateSubnets($candidateSubnets)
  {
    $this->candidateSubnets = $candidateSubnets;
  }
  /**
   * @return string[]
   */
  public function getCandidateSubnets()
  {
    return $this->candidateSubnets;
  }
  /**
   * Output only. [Output Only] IPv4 address + prefix length to be configured on
   * Cloud Router Interface for this interconnect attachment.
   *
   * @param string $cloudRouterIpAddress
   */
  public function setCloudRouterIpAddress($cloudRouterIpAddress)
  {
    $this->cloudRouterIpAddress = $cloudRouterIpAddress;
  }
  /**
   * @return string
   */
  public function getCloudRouterIpAddress()
  {
    return $this->cloudRouterIpAddress;
  }
  /**
   * Output only. [Output Only] IPv6 address + prefix length to be configured on
   * Cloud Router Interface for this interconnect attachment.
   *
   * @param string $cloudRouterIpv6Address
   */
  public function setCloudRouterIpv6Address($cloudRouterIpv6Address)
  {
    $this->cloudRouterIpv6Address = $cloudRouterIpv6Address;
  }
  /**
   * @return string
   */
  public function getCloudRouterIpv6Address()
  {
    return $this->cloudRouterIpv6Address;
  }
  /**
   * This field is not available.
   *
   * @param string $cloudRouterIpv6InterfaceId
   */
  public function setCloudRouterIpv6InterfaceId($cloudRouterIpv6InterfaceId)
  {
    $this->cloudRouterIpv6InterfaceId = $cloudRouterIpv6InterfaceId;
  }
  /**
   * @return string
   */
  public function getCloudRouterIpv6InterfaceId()
  {
    return $this->cloudRouterIpv6InterfaceId;
  }
  /**
   * Output only. [Output Only] Constraints for this attachment, if any. The
   * attachment does not work if these constraints are not met.
   *
   * @param InterconnectAttachmentConfigurationConstraints $configurationConstraints
   */
  public function setConfigurationConstraints(InterconnectAttachmentConfigurationConstraints $configurationConstraints)
  {
    $this->configurationConstraints = $configurationConstraints;
  }
  /**
   * @return InterconnectAttachmentConfigurationConstraints
   */
  public function getConfigurationConstraints()
  {
    return $this->configurationConstraints;
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
   * Output only. [Output Only] IPv4 address + prefix length to be configured on
   * the customer router subinterface for this interconnect attachment.
   *
   * @param string $customerRouterIpAddress
   */
  public function setCustomerRouterIpAddress($customerRouterIpAddress)
  {
    $this->customerRouterIpAddress = $customerRouterIpAddress;
  }
  /**
   * @return string
   */
  public function getCustomerRouterIpAddress()
  {
    return $this->customerRouterIpAddress;
  }
  /**
   * Output only. [Output Only] IPv6 address + prefix length to be configured on
   * the customer router subinterface for this interconnect attachment.
   *
   * @param string $customerRouterIpv6Address
   */
  public function setCustomerRouterIpv6Address($customerRouterIpv6Address)
  {
    $this->customerRouterIpv6Address = $customerRouterIpv6Address;
  }
  /**
   * @return string
   */
  public function getCustomerRouterIpv6Address()
  {
    return $this->customerRouterIpv6Address;
  }
  /**
   * This field is not available.
   *
   * @param string $customerRouterIpv6InterfaceId
   */
  public function setCustomerRouterIpv6InterfaceId($customerRouterIpv6InterfaceId)
  {
    $this->customerRouterIpv6InterfaceId = $customerRouterIpv6InterfaceId;
  }
  /**
   * @return string
   */
  public function getCustomerRouterIpv6InterfaceId()
  {
    return $this->customerRouterIpv6InterfaceId;
  }
  /**
   * Output only. [Output Only] Dataplane version for this
   * InterconnectAttachment. This field is only present for Dataplane version 2
   * and higher. Absence of this field in the API output indicates that the
   * Dataplane is version 1.
   *
   * @param int $dataplaneVersion
   */
  public function setDataplaneVersion($dataplaneVersion)
  {
    $this->dataplaneVersion = $dataplaneVersion;
  }
  /**
   * @return int
   */
  public function getDataplaneVersion()
  {
    return $this->dataplaneVersion;
  }
  /**
   * An optional description of this resource.
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
   * Input only. Desired availability domain for the attachment. Only available
   * for type PARTNER, at creation time, and can take one of the following
   * values:        - AVAILABILITY_DOMAIN_ANY    - AVAILABILITY_DOMAIN_1    -
   * AVAILABILITY_DOMAIN_2
   *
   * For improved reliability, customers should configure a pair of attachments,
   * one per availability domain. The selected availability domain will be
   * provided to the Partner via the pairing key, so that the provisioned
   * circuit will lie in the specified domain. If not specified, the value will
   * default to AVAILABILITY_DOMAIN_ANY.
   *
   * Accepted values: AVAILABILITY_DOMAIN_1, AVAILABILITY_DOMAIN_2,
   * AVAILABILITY_DOMAIN_ANY
   *
   * @param self::EDGE_AVAILABILITY_DOMAIN_* $edgeAvailabilityDomain
   */
  public function setEdgeAvailabilityDomain($edgeAvailabilityDomain)
  {
    $this->edgeAvailabilityDomain = $edgeAvailabilityDomain;
  }
  /**
   * @return self::EDGE_AVAILABILITY_DOMAIN_*
   */
  public function getEdgeAvailabilityDomain()
  {
    return $this->edgeAvailabilityDomain;
  }
  /**
   * Indicates the user-supplied encryption option of this VLAN attachment
   * (interconnectAttachment). Can only be specified at attachment creation for
   * PARTNER or DEDICATED attachments. Possible values are:        - NONE - This
   * is the default value, which means that the    VLAN attachment carries
   * unencrypted traffic. VMs are able to send    traffic to, or receive traffic
   * from, such a VLAN attachment.    - IPSEC - The VLAN attachment carries only
   * encrypted    traffic that is encrypted by an IPsec device, such as an HA
   * VPN gateway or    third-party IPsec VPN. VMs cannot directly send traffic
   * to, or receive    traffic from, such a VLAN attachment. To use *HA VPN over
   * Cloud    Interconnect*, the VLAN attachment must be created with this
   * option.
   *
   * Accepted values: IPSEC, NONE
   *
   * @param self::ENCRYPTION_* $encryption
   */
  public function setEncryption($encryption)
  {
    $this->encryption = $encryption;
  }
  /**
   * @return self::ENCRYPTION_*
   */
  public function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * Output only. [Output Only] Google reference ID, to be used when raising
   * support tickets with Google or otherwise to debug backend connectivity
   * issues. [Deprecated] This field is not used.
   *
   * @deprecated
   * @param string $googleReferenceId
   */
  public function setGoogleReferenceId($googleReferenceId)
  {
    $this->googleReferenceId = $googleReferenceId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getGoogleReferenceId()
  {
    return $this->googleReferenceId;
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
   * URL of the underlying Interconnect object that this attachment's traffic
   * will traverse through.
   *
   * @param string $interconnect
   */
  public function setInterconnect($interconnect)
  {
    $this->interconnect = $interconnect;
  }
  /**
   * @return string
   */
  public function getInterconnect()
  {
    return $this->interconnect;
  }
  /**
   * A list of URLs of addresses that have been reserved for the VLAN
   * attachment. Used only for the VLAN attachment that has the encryption
   * option as IPSEC. The addresses must be regional internal IP address ranges.
   * When creating an HA VPN gateway over the VLAN attachment, if the attachment
   * is configured to use a regional internal IP address, then the VPN gateway's
   * IP address is allocated from the IP address range specified here. For
   * example, if the HA VPN gateway's interface 0 is paired to this VLAN
   * attachment, then a regional internal IP address for the VPN gateway
   * interface 0 will be allocated from the IP address specified for this VLAN
   * attachment. If this field is not specified when creating the VLAN
   * attachment, then later on when creating an HA VPN gateway on this VLAN
   * attachment, the HA VPN gateway's IP address is allocated from the regional
   * external IP address pool.
   *
   * @param string[] $ipsecInternalAddresses
   */
  public function setIpsecInternalAddresses($ipsecInternalAddresses)
  {
    $this->ipsecInternalAddresses = $ipsecInternalAddresses;
  }
  /**
   * @return string[]
   */
  public function getIpsecInternalAddresses()
  {
    return $this->ipsecInternalAddresses;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#interconnectAttachment for interconnect attachments.
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
   * L2 Interconnect Attachment related config. This field is required if the
   * type is L2_DEDICATED.
   *
   * The configuration specifies how VLAN tags (like dot1q, qinq, or dot1ad)
   * within L2 packets are mapped to the destination appliances IP addresses.
   * The packet is then encapsulated with the appliance IP address and sent to
   * the edge appliance.
   *
   * @param InterconnectAttachmentL2Forwarding $l2Forwarding
   */
  public function setL2Forwarding(InterconnectAttachmentL2Forwarding $l2Forwarding)
  {
    $this->l2Forwarding = $l2Forwarding;
  }
  /**
   * @return InterconnectAttachmentL2Forwarding
   */
  public function getL2Forwarding()
  {
    return $this->l2Forwarding;
  }
  /**
   * A fingerprint for the labels being applied to this InterconnectAttachment,
   * which is essentially a hash of the labels set used for optimistic locking.
   * The fingerprint is initially generated by Compute Engine and changes after
   * every request to modify or update labels. You must always provide an up-to-
   * date fingerprint hash in order to update or change labels, otherwise the
   * request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an
   * InterconnectAttachment.
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
   * Maximum Transmission Unit (MTU), in bytes, of packets passing through this
   * interconnect attachment. Valid values are 1440, 1460, 1500, and 8896. If
   * not specified, the value will default to 1440.
   *
   * @param int $mtu
   */
  public function setMtu($mtu)
  {
    $this->mtu = $mtu;
  }
  /**
   * @return int
   */
  public function getMtu()
  {
    return $this->mtu;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * Output only. [Output Only] The current status of whether or not this
   * interconnect attachment is functional, which can take one of the following
   * values:        - OS_ACTIVE: The attachment has been turned up and is ready
   * to    use.     - OS_UNPROVISIONED: The attachment is not ready to use yet,
   * because turnup is not complete.
   *
   * Accepted values: OS_ACTIVE, OS_UNPROVISIONED
   *
   * @param self::OPERATIONAL_STATUS_* $operationalStatus
   */
  public function setOperationalStatus($operationalStatus)
  {
    $this->operationalStatus = $operationalStatus;
  }
  /**
   * @return self::OPERATIONAL_STATUS_*
   */
  public function getOperationalStatus()
  {
    return $this->operationalStatus;
  }
  /**
   * [Output only for type PARTNER. Input only for PARTNER_PROVIDER. Not present
   * for DEDICATED]. The opaque identifier of a PARTNER attachment used to
   * initiate provisioning with a selected partner. Of the form
   * "XXXXX/region/domain"
   *
   * @param string $pairingKey
   */
  public function setPairingKey($pairingKey)
  {
    $this->pairingKey = $pairingKey;
  }
  /**
   * @return string
   */
  public function getPairingKey()
  {
    return $this->pairingKey;
  }
  /**
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param InterconnectAttachmentParams $params
   */
  public function setParams(InterconnectAttachmentParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return InterconnectAttachmentParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Optional BGP ASN for the router supplied by a Layer 3 Partner if they
   * configured BGP on behalf of the customer. Output only for PARTNER type,
   * input only for PARTNER_PROVIDER, not available for DEDICATED.
   *
   * @param string $partnerAsn
   */
  public function setPartnerAsn($partnerAsn)
  {
    $this->partnerAsn = $partnerAsn;
  }
  /**
   * @return string
   */
  public function getPartnerAsn()
  {
    return $this->partnerAsn;
  }
  /**
   * Informational metadata about Partner attachments from Partners to display
   * to customers. Output only for PARTNER type, mutable for PARTNER_PROVIDER,
   * not available for DEDICATED.
   *
   * @param InterconnectAttachmentPartnerMetadata $partnerMetadata
   */
  public function setPartnerMetadata(InterconnectAttachmentPartnerMetadata $partnerMetadata)
  {
    $this->partnerMetadata = $partnerMetadata;
  }
  /**
   * @return InterconnectAttachmentPartnerMetadata
   */
  public function getPartnerMetadata()
  {
    return $this->partnerMetadata;
  }
  /**
   * Output only. [Output Only] Information specific to an
   * InterconnectAttachment. This property is populated if the interconnect that
   * this is attached to is of type DEDICATED.
   *
   * @param InterconnectAttachmentPrivateInfo $privateInterconnectInfo
   */
  public function setPrivateInterconnectInfo(InterconnectAttachmentPrivateInfo $privateInterconnectInfo)
  {
    $this->privateInterconnectInfo = $privateInterconnectInfo;
  }
  /**
   * @return InterconnectAttachmentPrivateInfo
   */
  public function getPrivateInterconnectInfo()
  {
    return $this->privateInterconnectInfo;
  }
  /**
   * Output only. [Output Only] URL of the region where the regional
   * interconnect attachment resides. You must specify this field as part of the
   * HTTP request URL. It is not settable as a field in the request body.
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
   * Output only. [Output Only] If the attachment is on a Cross-Cloud
   * Interconnect connection, this field contains the interconnect's remote
   * location service provider. Example values: "Amazon Web Services" "Microsoft
   * Azure".
   *
   * The field is set only for attachments on Cross-Cloud Interconnect
   * connections. Its value is copied from the InterconnectRemoteLocation
   * remoteService field.
   *
   * @param string $remoteService
   */
  public function setRemoteService($remoteService)
  {
    $this->remoteService = $remoteService;
  }
  /**
   * @return string
   */
  public function getRemoteService()
  {
    return $this->remoteService;
  }
  /**
   * URL of the Cloud Router to be used for dynamic routing. This router must be
   * in the same region as this InterconnectAttachment. The
   * InterconnectAttachment will automatically connect the Interconnect to the
   * network & region within which the Cloud Router is configured.
   *
   * @param string $router
   */
  public function setRouter($router)
  {
    $this->router = $router;
  }
  /**
   * @return string
   */
  public function getRouter()
  {
    return $this->router;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
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
   * Output only. [Output Only] Server-defined URL for the resource.
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
   * The stack type for this interconnect attachment to identify whether the
   * IPv6 feature is enabled or not. If not specified, IPV4_ONLY will be used.
   *
   * This field can be both set at interconnect attachments creation and update
   * interconnect attachment operations.
   *
   * Accepted values: IPV4_IPV6, IPV4_ONLY
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
   * Output only. [Output Only] The current state of this attachment's
   * functionality. Enum values ACTIVE and UNPROVISIONED are shared by
   * DEDICATED/PRIVATE, PARTNER, and PARTNER_PROVIDER interconnect attachments,
   * while enum values PENDING_PARTNER, PARTNER_REQUEST_RECEIVED, and
   * PENDING_CUSTOMER are used for only PARTNER and PARTNER_PROVIDER
   * interconnect attachments. This state can take one of the following values:
   * - ACTIVE: The attachment has been turned up and is ready to use.    -
   * UNPROVISIONED: The attachment is not ready to use yet, because turnup    is
   * not complete.    - PENDING_PARTNER: A newly-created PARTNER attachment that
   * has not yet    been configured on the Partner side.    -
   * PARTNER_REQUEST_RECEIVED: A PARTNER attachment is in the process of
   * provisioning after a PARTNER_PROVIDER attachment was created that
   * references it.     - PENDING_CUSTOMER: A PARTNER or PARTNER_PROVIDER
   * attachment that is waiting for a customer to activate it.     - DEFUNCT:
   * The attachment was deleted externally and is no longer functional. This
   * could be because the associated Interconnect was removed, or because the
   * other side of a Partner attachment was deleted.
   *
   * Accepted values: ACTIVE, DEFUNCT, PARTNER_REQUEST_RECEIVED,
   * PENDING_CUSTOMER, PENDING_PARTNER, STATE_UNSPECIFIED, UNPROVISIONED
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
   * Input only. Length of the IPv4 subnet mask. Allowed values:             -
   * 29 (default)     - 30
   *
   * The default value is 29, except for Cross-Cloud Interconnect connections
   * that use an InterconnectRemoteLocation with a
   * constraints.subnetLengthRange.min equal to 30. For example, connections
   * that use an Azure remote location fall into this category. In these cases,
   * the default value is 30, and requesting 29 returns an error.
   *
   * Where both 29 and 30 are allowed, 29 is preferred, because it gives Google
   * Cloud Support more debugging visibility.
   *
   * @param int $subnetLength
   */
  public function setSubnetLength($subnetLength)
  {
    $this->subnetLength = $subnetLength;
  }
  /**
   * @return int
   */
  public function getSubnetLength()
  {
    return $this->subnetLength;
  }
  /**
   * The type of interconnect attachment this is, which can take one of the
   * following values:        - DEDICATED: an attachment to a Dedicated
   * Interconnect.    - PARTNER: an attachment to a Partner Interconnect,
   * created by the    customer.    - PARTNER_PROVIDER: an attachment to a
   * Partner Interconnect, created by    the partner.
   *
   * - L2_DEDICATED: a L2 attachment to a Dedicated Interconnect.
   *
   * Accepted values: DEDICATED, L2_DEDICATED, PARTNER, PARTNER_PROVIDER
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
   * The IEEE 802.1Q VLAN tag for this attachment, in the range 2-4093. Only
   * specified at creation time.
   *
   * @param int $vlanTag8021q
   */
  public function setVlanTag8021q($vlanTag8021q)
  {
    $this->vlanTag8021q = $vlanTag8021q;
  }
  /**
   * @return int
   */
  public function getVlanTag8021q()
  {
    return $this->vlanTag8021q;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachment::class, 'Google_Service_Compute_InterconnectAttachment');
