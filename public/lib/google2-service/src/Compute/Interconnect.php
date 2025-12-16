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

class Interconnect extends \Google\Collection
{
  /**
   * A dedicated physical interconnection with the customer.
   */
  public const INTERCONNECT_TYPE_DEDICATED = 'DEDICATED';
  /**
   * [Deprecated] A private, physical interconnection with the customer.
   */
  public const INTERCONNECT_TYPE_IT_PRIVATE = 'IT_PRIVATE';
  /**
   * A partner-managed interconnection shared between customers via partner.
   */
  public const INTERCONNECT_TYPE_PARTNER = 'PARTNER';
  /**
   * 100G Ethernet, LR Optics.
   */
  public const LINK_TYPE_LINK_TYPE_ETHERNET_100G_LR = 'LINK_TYPE_ETHERNET_100G_LR';
  /**
   * 10G Ethernet, LR Optics. [(rate_bps) =  10000000000];
   */
  public const LINK_TYPE_LINK_TYPE_ETHERNET_10G_LR = 'LINK_TYPE_ETHERNET_10G_LR';
  /**
   * 400G Ethernet, LR4 Optics.
   */
  public const LINK_TYPE_LINK_TYPE_ETHERNET_400G_LR4 = 'LINK_TYPE_ETHERNET_400G_LR4';
  /**
   * The interconnect is valid, turned up, and ready to use. Attachments may be
   * provisioned on this interconnect.
   */
  public const OPERATIONAL_STATUS_OS_ACTIVE = 'OS_ACTIVE';
  /**
   * The interconnect has not completed turnup. No attachments may be
   * provisioned on this interconnect.
   */
  public const OPERATIONAL_STATUS_OS_UNPROVISIONED = 'OS_UNPROVISIONED';
  /**
   * The interconnect is valid, turned up, and ready to use. Attachments may be
   * provisioned on this interconnect.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The interconnect has not completed turnup. No attachments may be
   * provisioned on this interconnect.
   */
  public const STATE_UNPROVISIONED = 'UNPROVISIONED';
  /**
   * Subzone A.
   */
  public const SUBZONE_SUBZONE_A = 'SUBZONE_A';
  /**
   * Subzone B.
   */
  public const SUBZONE_SUBZONE_B = 'SUBZONE_B';
  protected $collection_key = 'wireGroups';
  /**
   * Enable or disable the application awareness feature on this Cloud
   * Interconnect.
   *
   * @var bool
   */
  public $aaiEnabled;
  /**
   * Administrative status of the interconnect. When this is set to true, the
   * Interconnect is functional and can carry traffic. When set to false, no
   * packets can be carried over the interconnect and no BGP routes are
   * exchanged over it. By default, the status is set to true.
   *
   * @var bool
   */
  public $adminEnabled;
  protected $applicationAwareInterconnectType = InterconnectApplicationAwareInterconnect::class;
  protected $applicationAwareInterconnectDataType = '';
  /**
   * [Output only] List of features available for this Interconnect connection,
   * which can take one of the following values:        - IF_MACSEC: If present,
   * then the Interconnect connection is    provisioned on MACsec capable
   * hardware ports. If not present, then the    Interconnect connection is
   * provisioned on non-MACsec capable ports. Any    attempt to enable MACsec
   * will fail.    - IF_CROSS_SITE_NETWORK: If present, then the Interconnect
   * connection is    provisioned exclusively for Cross-Site Networking. Any
   * attempt to configure    VLAN attachments will fail. If not present, then
   * the Interconnect    connection is not provisioned for Cross-Site
   * Networking. Any attempt to use    it for Cross-Site Networking will fail.
   *
   * @var string[]
   */
  public $availableFeatures;
  protected $circuitInfosType = InterconnectCircuitInfo::class;
  protected $circuitInfosDataType = 'array';
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * Customer name, to put in the Letter of Authorization as the party
   * authorized to request a crossconnect.
   *
   * @var string
   */
  public $customerName;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  protected $expectedOutagesType = InterconnectOutageNotification::class;
  protected $expectedOutagesDataType = 'array';
  /**
   * Output only. [Output Only] IP address configured on the Google side of the
   * Interconnect link. This can be used only for ping tests.
   *
   * @var string
   */
  public $googleIpAddress;
  /**
   * Output only. [Output Only] Google reference ID to be used when raising
   * support tickets with Google or otherwise to debug backend connectivity
   * issues.
   *
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
   * Output only. [Output Only] A list of the URLs of all
   * InterconnectAttachments configured to use  this Interconnect.
   *
   * @var string[]
   */
  public $interconnectAttachments;
  /**
   * Output only. [Output Only] URLs of InterconnectGroups that include this
   * Interconnect. Order is arbitrary and items are unique.
   *
   * @var string[]
   */
  public $interconnectGroups;
  /**
   * Type of interconnect, which can take one of the following values:        -
   * PARTNER: A partner-managed interconnection shared between customers
   * though a partner.    - DEDICATED: A dedicated physical interconnection with
   * the    customer.
   *
   * Note that a value IT_PRIVATE has been deprecated in favor of DEDICATED.
   *
   * @var string
   */
  public $interconnectType;
  /**
   * Output only. [Output Only] Type of the resource. Alwayscompute#interconnect
   * for interconnects.
   *
   * @var string
   */
  public $kind;
  /**
   * A fingerprint for the labels being applied to this Interconnect, which is
   * essentially a hash of the labels set used for optimistic locking. The
   * fingerprint is initially generated by Compute Engine and changes after
   * every request to modify or update labels. You must always provide an up-to-
   * date fingerprint hash in order to update or change labels, otherwise the
   * request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an
   * Interconnect.
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
   * Type of link requested, which can take one of the following values:
   * - LINK_TYPE_ETHERNET_10G_LR: A 10G Ethernet with LR optics    -
   * LINK_TYPE_ETHERNET_100G_LR: A 100G Ethernet with LR optics.    -
   * LINK_TYPE_ETHERNET_400G_LR4: A 400G Ethernet with LR4 optics.
   *
   *  Note that this field indicates the speed of each of the links in the
   * bundle, not the speed of the entire bundle.
   *
   * @var string
   */
  public $linkType;
  /**
   * URL of the InterconnectLocation object that represents where this
   * connection is to be provisioned.
   *
   * @var string
   */
  public $location;
  protected $macsecType = InterconnectMacsec::class;
  protected $macsecDataType = '';
  /**
   * Enable or disable MACsec on this Interconnect connection. MACsec enablement
   * fails if the MACsec object is not specified.
   *
   * @var bool
   */
  public $macsecEnabled;
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
   * Email address to contact the customer NOC for operations and maintenance
   * notifications regarding this Interconnect. If specified, this will be used
   * for notifications in addition to all other forms described, such as Cloud
   * Monitoring logs alerting and Cloud Notifications. This field is required
   * for users who sign up for Cloud Interconnect using workforce identity
   * federation.
   *
   * @var string
   */
  public $nocContactEmail;
  /**
   * Output only. [Output Only] The current status of this Interconnect's
   * functionality, which can take one of the following values:        -
   * OS_ACTIVE: A valid Interconnect, which is turned up and is ready to    use.
   * Attachments may be provisioned on this Interconnect.
   *
   * - OS_UNPROVISIONED: An Interconnect that has not completed turnup. No
   * attachments may be provisioned on this Interconnect. -
   * OS_UNDER_MAINTENANCE: An Interconnect that is undergoing internal
   * maintenance. No attachments may be provisioned or updated on this
   * Interconnect.
   *
   * @var string
   */
  public $operationalStatus;
  protected $paramsType = InterconnectParams::class;
  protected $paramsDataType = '';
  /**
   * Output only. [Output Only] IP address configured on the customer side of
   * the Interconnect link. The customer should configure this IP address during
   * turnup when prompted by Google NOC. This can be used only for ping tests.
   *
   * @var string
   */
  public $peerIpAddress;
  /**
   * Output only. [Output Only] Number of links actually provisioned in this
   * interconnect.
   *
   * @var int
   */
  public $provisionedLinkCount;
  /**
   * Indicates that this is a Cross-Cloud Interconnect. This field specifies the
   * location outside of Google's network that the interconnect is connected to.
   *
   * @var string
   */
  public $remoteLocation;
  /**
   * Optional. This parameter can be provided only with Interconnect INSERT. It
   * isn't valid for Interconnect PATCH. List of features requested for this
   * Interconnect connection, which can take one of the following values:
   * - IF_MACSEC: If specified, then the connection is created on MACsec
   * capable hardware ports. If not specified, non-MACsec capable ports will
   * also be considered.    - IF_CROSS_SITE_NETWORK: If specified, then the
   * connection is created    exclusively for Cross-Site Networking. The
   * connection can not be used for    Cross-Site Networking unless this feature
   * is specified.
   *
   * @var string[]
   */
  public $requestedFeatures;
  /**
   * Target number of physical links in the link bundle, as requested by the
   * customer.
   *
   * @var int
   */
  public $requestedLinkCount;
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
   * Output only. [Output Only] The current state of Interconnect functionality,
   * which can take one of the following values:        - ACTIVE: The
   * Interconnect is valid, turned up and ready to use.    Attachments may be
   * provisioned on this Interconnect.    - UNPROVISIONED: The Interconnect has
   * not completed turnup. No    attachments may be provisioned on this
   * Interconnect.    - UNDER_MAINTENANCE: The Interconnect is undergoing
   * internal maintenance.    No attachments may be provisioned or updated on
   * this    Interconnect.
   *
   * @var string
   */
  public $state;
  /**
   * Specific subzone in the InterconnectLocation that represents where this
   * connection is to be provisioned.
   *
   * @var string
   */
  public $subzone;
  /**
   * Output only. [Output Only] A list of the URLs of all CrossSiteNetwork
   * WireGroups configured to use this Interconnect. The Interconnect cannot be
   * deleted if this list is non-empty.
   *
   * @var string[]
   */
  public $wireGroups;

  /**
   * Enable or disable the application awareness feature on this Cloud
   * Interconnect.
   *
   * @param bool $aaiEnabled
   */
  public function setAaiEnabled($aaiEnabled)
  {
    $this->aaiEnabled = $aaiEnabled;
  }
  /**
   * @return bool
   */
  public function getAaiEnabled()
  {
    return $this->aaiEnabled;
  }
  /**
   * Administrative status of the interconnect. When this is set to true, the
   * Interconnect is functional and can carry traffic. When set to false, no
   * packets can be carried over the interconnect and no BGP routes are
   * exchanged over it. By default, the status is set to true.
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
   * Configuration information for application awareness on this Cloud
   * Interconnect.
   *
   * @param InterconnectApplicationAwareInterconnect $applicationAwareInterconnect
   */
  public function setApplicationAwareInterconnect(InterconnectApplicationAwareInterconnect $applicationAwareInterconnect)
  {
    $this->applicationAwareInterconnect = $applicationAwareInterconnect;
  }
  /**
   * @return InterconnectApplicationAwareInterconnect
   */
  public function getApplicationAwareInterconnect()
  {
    return $this->applicationAwareInterconnect;
  }
  /**
   * [Output only] List of features available for this Interconnect connection,
   * which can take one of the following values:        - IF_MACSEC: If present,
   * then the Interconnect connection is    provisioned on MACsec capable
   * hardware ports. If not present, then the    Interconnect connection is
   * provisioned on non-MACsec capable ports. Any    attempt to enable MACsec
   * will fail.    - IF_CROSS_SITE_NETWORK: If present, then the Interconnect
   * connection is    provisioned exclusively for Cross-Site Networking. Any
   * attempt to configure    VLAN attachments will fail. If not present, then
   * the Interconnect    connection is not provisioned for Cross-Site
   * Networking. Any attempt to use    it for Cross-Site Networking will fail.
   *
   * @param string[] $availableFeatures
   */
  public function setAvailableFeatures($availableFeatures)
  {
    $this->availableFeatures = $availableFeatures;
  }
  /**
   * @return string[]
   */
  public function getAvailableFeatures()
  {
    return $this->availableFeatures;
  }
  /**
   * Output only. [Output Only] A list of CircuitInfo objects, that describe the
   * individual circuits in this LAG.
   *
   * @param InterconnectCircuitInfo[] $circuitInfos
   */
  public function setCircuitInfos($circuitInfos)
  {
    $this->circuitInfos = $circuitInfos;
  }
  /**
   * @return InterconnectCircuitInfo[]
   */
  public function getCircuitInfos()
  {
    return $this->circuitInfos;
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
   * Customer name, to put in the Letter of Authorization as the party
   * authorized to request a crossconnect.
   *
   * @param string $customerName
   */
  public function setCustomerName($customerName)
  {
    $this->customerName = $customerName;
  }
  /**
   * @return string
   */
  public function getCustomerName()
  {
    return $this->customerName;
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
   * Output only. [Output Only] A list of outages expected for this
   * Interconnect.
   *
   * @param InterconnectOutageNotification[] $expectedOutages
   */
  public function setExpectedOutages($expectedOutages)
  {
    $this->expectedOutages = $expectedOutages;
  }
  /**
   * @return InterconnectOutageNotification[]
   */
  public function getExpectedOutages()
  {
    return $this->expectedOutages;
  }
  /**
   * Output only. [Output Only] IP address configured on the Google side of the
   * Interconnect link. This can be used only for ping tests.
   *
   * @param string $googleIpAddress
   */
  public function setGoogleIpAddress($googleIpAddress)
  {
    $this->googleIpAddress = $googleIpAddress;
  }
  /**
   * @return string
   */
  public function getGoogleIpAddress()
  {
    return $this->googleIpAddress;
  }
  /**
   * Output only. [Output Only] Google reference ID to be used when raising
   * support tickets with Google or otherwise to debug backend connectivity
   * issues.
   *
   * @param string $googleReferenceId
   */
  public function setGoogleReferenceId($googleReferenceId)
  {
    $this->googleReferenceId = $googleReferenceId;
  }
  /**
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
   * Output only. [Output Only] A list of the URLs of all
   * InterconnectAttachments configured to use  this Interconnect.
   *
   * @param string[] $interconnectAttachments
   */
  public function setInterconnectAttachments($interconnectAttachments)
  {
    $this->interconnectAttachments = $interconnectAttachments;
  }
  /**
   * @return string[]
   */
  public function getInterconnectAttachments()
  {
    return $this->interconnectAttachments;
  }
  /**
   * Output only. [Output Only] URLs of InterconnectGroups that include this
   * Interconnect. Order is arbitrary and items are unique.
   *
   * @param string[] $interconnectGroups
   */
  public function setInterconnectGroups($interconnectGroups)
  {
    $this->interconnectGroups = $interconnectGroups;
  }
  /**
   * @return string[]
   */
  public function getInterconnectGroups()
  {
    return $this->interconnectGroups;
  }
  /**
   * Type of interconnect, which can take one of the following values:        -
   * PARTNER: A partner-managed interconnection shared between customers
   * though a partner.    - DEDICATED: A dedicated physical interconnection with
   * the    customer.
   *
   * Note that a value IT_PRIVATE has been deprecated in favor of DEDICATED.
   *
   * Accepted values: DEDICATED, IT_PRIVATE, PARTNER
   *
   * @param self::INTERCONNECT_TYPE_* $interconnectType
   */
  public function setInterconnectType($interconnectType)
  {
    $this->interconnectType = $interconnectType;
  }
  /**
   * @return self::INTERCONNECT_TYPE_*
   */
  public function getInterconnectType()
  {
    return $this->interconnectType;
  }
  /**
   * Output only. [Output Only] Type of the resource. Alwayscompute#interconnect
   * for interconnects.
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
   * A fingerprint for the labels being applied to this Interconnect, which is
   * essentially a hash of the labels set used for optimistic locking. The
   * fingerprint is initially generated by Compute Engine and changes after
   * every request to modify or update labels. You must always provide an up-to-
   * date fingerprint hash in order to update or change labels, otherwise the
   * request will fail with error412 conditionNotMet.
   *
   * To see the latest fingerprint, make a get() request to retrieve an
   * Interconnect.
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
   * Type of link requested, which can take one of the following values:
   * - LINK_TYPE_ETHERNET_10G_LR: A 10G Ethernet with LR optics    -
   * LINK_TYPE_ETHERNET_100G_LR: A 100G Ethernet with LR optics.    -
   * LINK_TYPE_ETHERNET_400G_LR4: A 400G Ethernet with LR4 optics.
   *
   *  Note that this field indicates the speed of each of the links in the
   * bundle, not the speed of the entire bundle.
   *
   * Accepted values: LINK_TYPE_ETHERNET_100G_LR, LINK_TYPE_ETHERNET_10G_LR,
   * LINK_TYPE_ETHERNET_400G_LR4
   *
   * @param self::LINK_TYPE_* $linkType
   */
  public function setLinkType($linkType)
  {
    $this->linkType = $linkType;
  }
  /**
   * @return self::LINK_TYPE_*
   */
  public function getLinkType()
  {
    return $this->linkType;
  }
  /**
   * URL of the InterconnectLocation object that represents where this
   * connection is to be provisioned.
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
   * Configuration that enables Media Access Control security (MACsec) on the
   * Cloud Interconnect connection between Google and your on-premises router.
   *
   * @param InterconnectMacsec $macsec
   */
  public function setMacsec(InterconnectMacsec $macsec)
  {
    $this->macsec = $macsec;
  }
  /**
   * @return InterconnectMacsec
   */
  public function getMacsec()
  {
    return $this->macsec;
  }
  /**
   * Enable or disable MACsec on this Interconnect connection. MACsec enablement
   * fails if the MACsec object is not specified.
   *
   * @param bool $macsecEnabled
   */
  public function setMacsecEnabled($macsecEnabled)
  {
    $this->macsecEnabled = $macsecEnabled;
  }
  /**
   * @return bool
   */
  public function getMacsecEnabled()
  {
    return $this->macsecEnabled;
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
   * Email address to contact the customer NOC for operations and maintenance
   * notifications regarding this Interconnect. If specified, this will be used
   * for notifications in addition to all other forms described, such as Cloud
   * Monitoring logs alerting and Cloud Notifications. This field is required
   * for users who sign up for Cloud Interconnect using workforce identity
   * federation.
   *
   * @param string $nocContactEmail
   */
  public function setNocContactEmail($nocContactEmail)
  {
    $this->nocContactEmail = $nocContactEmail;
  }
  /**
   * @return string
   */
  public function getNocContactEmail()
  {
    return $this->nocContactEmail;
  }
  /**
   * Output only. [Output Only] The current status of this Interconnect's
   * functionality, which can take one of the following values:        -
   * OS_ACTIVE: A valid Interconnect, which is turned up and is ready to    use.
   * Attachments may be provisioned on this Interconnect.
   *
   * - OS_UNPROVISIONED: An Interconnect that has not completed turnup. No
   * attachments may be provisioned on this Interconnect. -
   * OS_UNDER_MAINTENANCE: An Interconnect that is undergoing internal
   * maintenance. No attachments may be provisioned or updated on this
   * Interconnect.
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
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param InterconnectParams $params
   */
  public function setParams(InterconnectParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return InterconnectParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Output only. [Output Only] IP address configured on the customer side of
   * the Interconnect link. The customer should configure this IP address during
   * turnup when prompted by Google NOC. This can be used only for ping tests.
   *
   * @param string $peerIpAddress
   */
  public function setPeerIpAddress($peerIpAddress)
  {
    $this->peerIpAddress = $peerIpAddress;
  }
  /**
   * @return string
   */
  public function getPeerIpAddress()
  {
    return $this->peerIpAddress;
  }
  /**
   * Output only. [Output Only] Number of links actually provisioned in this
   * interconnect.
   *
   * @param int $provisionedLinkCount
   */
  public function setProvisionedLinkCount($provisionedLinkCount)
  {
    $this->provisionedLinkCount = $provisionedLinkCount;
  }
  /**
   * @return int
   */
  public function getProvisionedLinkCount()
  {
    return $this->provisionedLinkCount;
  }
  /**
   * Indicates that this is a Cross-Cloud Interconnect. This field specifies the
   * location outside of Google's network that the interconnect is connected to.
   *
   * @param string $remoteLocation
   */
  public function setRemoteLocation($remoteLocation)
  {
    $this->remoteLocation = $remoteLocation;
  }
  /**
   * @return string
   */
  public function getRemoteLocation()
  {
    return $this->remoteLocation;
  }
  /**
   * Optional. This parameter can be provided only with Interconnect INSERT. It
   * isn't valid for Interconnect PATCH. List of features requested for this
   * Interconnect connection, which can take one of the following values:
   * - IF_MACSEC: If specified, then the connection is created on MACsec
   * capable hardware ports. If not specified, non-MACsec capable ports will
   * also be considered.    - IF_CROSS_SITE_NETWORK: If specified, then the
   * connection is created    exclusively for Cross-Site Networking. The
   * connection can not be used for    Cross-Site Networking unless this feature
   * is specified.
   *
   * @param string[] $requestedFeatures
   */
  public function setRequestedFeatures($requestedFeatures)
  {
    $this->requestedFeatures = $requestedFeatures;
  }
  /**
   * @return string[]
   */
  public function getRequestedFeatures()
  {
    return $this->requestedFeatures;
  }
  /**
   * Target number of physical links in the link bundle, as requested by the
   * customer.
   *
   * @param int $requestedLinkCount
   */
  public function setRequestedLinkCount($requestedLinkCount)
  {
    $this->requestedLinkCount = $requestedLinkCount;
  }
  /**
   * @return int
   */
  public function getRequestedLinkCount()
  {
    return $this->requestedLinkCount;
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
   * Output only. [Output Only] The current state of Interconnect functionality,
   * which can take one of the following values:        - ACTIVE: The
   * Interconnect is valid, turned up and ready to use.    Attachments may be
   * provisioned on this Interconnect.    - UNPROVISIONED: The Interconnect has
   * not completed turnup. No    attachments may be provisioned on this
   * Interconnect.    - UNDER_MAINTENANCE: The Interconnect is undergoing
   * internal maintenance.    No attachments may be provisioned or updated on
   * this    Interconnect.
   *
   * Accepted values: ACTIVE, UNPROVISIONED
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
   * Specific subzone in the InterconnectLocation that represents where this
   * connection is to be provisioned.
   *
   * Accepted values: SUBZONE_A, SUBZONE_B
   *
   * @param self::SUBZONE_* $subzone
   */
  public function setSubzone($subzone)
  {
    $this->subzone = $subzone;
  }
  /**
   * @return self::SUBZONE_*
   */
  public function getSubzone()
  {
    return $this->subzone;
  }
  /**
   * Output only. [Output Only] A list of the URLs of all CrossSiteNetwork
   * WireGroups configured to use this Interconnect. The Interconnect cannot be
   * deleted if this list is non-empty.
   *
   * @param string[] $wireGroups
   */
  public function setWireGroups($wireGroups)
  {
    $this->wireGroups = $wireGroups;
  }
  /**
   * @return string[]
   */
  public function getWireGroups()
  {
    return $this->wireGroups;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Interconnect::class, 'Google_Service_Compute_Interconnect');
