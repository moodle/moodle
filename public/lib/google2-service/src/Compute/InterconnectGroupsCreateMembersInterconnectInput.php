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

class InterconnectGroupsCreateMembersInterconnectInput extends \Google\Collection
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
  protected $collection_key = 'requestedFeatures';
  /**
   * Administrative status of the interconnect. When this is set to true, the
   * Interconnect is functional and can carry traffic. When set to false, no
   * packets can be carried over the interconnect and no BGP routes are
   * exchanged over it. By default, the status is set to true.
   *
   * @var bool
   */
  public $adminEnabled;
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
  /**
   * A zone-free location to use for all Interconnects created in this call,
   * like "iad-1234".
   *
   * @var string
   */
  public $facility;
  /**
   * Type of interconnect, which can take one of the following values:        -
   * PARTNER: A partner-managed interconnection shared between    customers
   * though a partner.     - DEDICATED: A dedicated physical    interconnection
   * with the customer.
   *
   *  Note that a value IT_PRIVATE has been deprecated in favor of DEDICATED.
   *
   * @var string
   */
  public $interconnectType;
  /**
   * Type of link requested, which can take one of the following values:
   * - LINK_TYPE_ETHERNET_10G_LR: A 10G Ethernet with LR optics    -
   * LINK_TYPE_ETHERNET_100G_LR: A 100G Ethernet with LR optics.    -
   * LINK_TYPE_ETHERNET_400G_LR4: A 400G Ethernet with LR4    optics.
   *
   *  Note that this field indicates the speed of each of the links in the
   * bundle, not the speed of the entire bundle.
   *
   * @var string
   */
  public $linkType;
  /**
   * Name of the Interconnects to be created. This must be specified on the
   * template and/or on each individual interconnect. The name, if not empty,
   * must be 1-63 characters long, and comply with RFC1035. Specifically, any
   * nonempty name must be 1-63 characters long and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character must be a
   * lowercase letter, and all following characters must be a dash, lowercase
   * letter, or digit, except the last character, which cannot be a dash.
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
   * Indicates that this is a Cross-Cloud Interconnect. This field specifies the
   * location outside of Google's network that the interconnect is connected to.
   *
   * @var string
   */
  public $remoteLocation;
  /**
   * Optional. List of features requested for this Interconnect connection,
   * which can take one of the following values:        - IF_MACSEC: If
   * specified, then the connection is created on MACsec    capable hardware
   * ports. If not specified, non-MACsec capable ports will    also be
   * considered.    - IF_CROSS_SITE_NETWORK: If specified, then the connection
   * is created    exclusively for Cross-Site Networking. The connection can not
   * be used for    Cross-Site Networking unless this feature is specified.
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
   * A zone-free location to use for all Interconnects created in this call,
   * like "iad-1234".
   *
   * @param string $facility
   */
  public function setFacility($facility)
  {
    $this->facility = $facility;
  }
  /**
   * @return string
   */
  public function getFacility()
  {
    return $this->facility;
  }
  /**
   * Type of interconnect, which can take one of the following values:        -
   * PARTNER: A partner-managed interconnection shared between    customers
   * though a partner.     - DEDICATED: A dedicated physical    interconnection
   * with the customer.
   *
   *  Note that a value IT_PRIVATE has been deprecated in favor of DEDICATED.
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
   * Type of link requested, which can take one of the following values:
   * - LINK_TYPE_ETHERNET_10G_LR: A 10G Ethernet with LR optics    -
   * LINK_TYPE_ETHERNET_100G_LR: A 100G Ethernet with LR optics.    -
   * LINK_TYPE_ETHERNET_400G_LR4: A 400G Ethernet with LR4    optics.
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
   * Name of the Interconnects to be created. This must be specified on the
   * template and/or on each individual interconnect. The name, if not empty,
   * must be 1-63 characters long, and comply with RFC1035. Specifically, any
   * nonempty name must be 1-63 characters long and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character must be a
   * lowercase letter, and all following characters must be a dash, lowercase
   * letter, or digit, except the last character, which cannot be a dash.
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
   * Optional. List of features requested for this Interconnect connection,
   * which can take one of the following values:        - IF_MACSEC: If
   * specified, then the connection is created on MACsec    capable hardware
   * ports. If not specified, non-MACsec capable ports will    also be
   * considered.    - IF_CROSS_SITE_NETWORK: If specified, then the connection
   * is created    exclusively for Cross-Site Networking. The connection can not
   * be used for    Cross-Site Networking unless this feature is specified.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupsCreateMembersInterconnectInput::class, 'Google_Service_Compute_InterconnectGroupsCreateMembersInterconnectInput');
