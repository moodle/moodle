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

class RouterInterface extends \Google\Model
{
  public const IP_VERSION_IPV4 = 'IPV4';
  public const IP_VERSION_IPV6 = 'IPV6';
  /**
   * The interface is automatically created for PARTNER type
   * InterconnectAttachment, Google will automatically create/update/delete this
   * interface when the PARTNER InterconnectAttachment is
   * created/provisioned/deleted. This type of interface cannot be manually
   * managed by user.
   */
  public const MANAGEMENT_TYPE_MANAGED_BY_ATTACHMENT = 'MANAGED_BY_ATTACHMENT';
  /**
   * Default value, the interface is manually created and managed by user.
   */
  public const MANAGEMENT_TYPE_MANAGED_BY_USER = 'MANAGED_BY_USER';
  /**
   * IP address and range of the interface.        - For Internet Protocol
   * version 4 (IPv4), the IP range must be in theRFC3927 link-local IP address
   * space. The value must    be a CIDR-formatted string, for example,
   * 169.254.0.1/30.    Note: Do not truncate the IP address, as it represents
   * the IP address of    the interface.     - For Internet Protocol version 6
   * (IPv6), the value    must be a unique local address (ULA) range from
   * fdff:1::/64    with a mask length of 126 or less. This value should be a
   * CIDR-formatted    string, for example, fdff:1::1/112. Within the router's
   * VPC, this IPv6 prefix will be reserved exclusively for this connection
   * and cannot be used for any other purpose.
   *
   * @var string
   */
  public $ipRange;
  /**
   * IP version of this interface.
   *
   * @var string
   */
  public $ipVersion;
  /**
   * URI of the linked Interconnect attachment. It must be in the same region as
   * the router. Each interface can have one linked resource, which can be a VPN
   * tunnel, an Interconnect attachment, or a subnetwork.
   *
   * @var string
   */
  public $linkedInterconnectAttachment;
  /**
   * URI of the linked VPN tunnel, which must be in the same region as the
   * router. Each interface can have one linked resource, which can be a VPN
   * tunnel, an Interconnect attachment, or a subnetwork.
   *
   * @var string
   */
  public $linkedVpnTunnel;
  /**
   * Output only. [Output Only] The resource that configures and manages this
   * interface.        - MANAGED_BY_USER is the default value and can be managed
   * directly    by users.    - MANAGED_BY_ATTACHMENT is an interface that is
   * configured and    managed by Cloud Interconnect, specifically, by an
   * InterconnectAttachment    of type PARTNER. Google automatically creates,
   * updates, and deletes    this type of interface when the PARTNER
   * InterconnectAttachment is    created, updated, or deleted.
   *
   * @var string
   */
  public $managementType;
  /**
   * Name of this interface entry. The name must be 1-63 characters long, and
   * comply withRFC1035. Specifically, the name must be 1-63 characters long and
   * match the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the
   * first character must be a lowercase letter, and all following characters
   * must be a dash, lowercase letter, or digit, except the last character,
   * which cannot be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * The regional private internal IP address that is used to establish BGP
   * sessions to a VM instance acting as a third-party Router Appliance, such as
   * a Next Gen Firewall, a Virtual Router, or an SD-WAN VM.
   *
   * @var string
   */
  public $privateIpAddress;
  /**
   * Name of the interface that will be redundant with the current interface you
   * are creating. The redundantInterface must belong to the same Cloud Router
   * as the interface here. To establish the BGP session to a Router Appliance
   * VM, you must create two BGP peers. The two BGP peers must be attached to
   * two separate interfaces that are redundant with each other. The
   * redundant_interface must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the redundant_interface must be 1-63 characters long and
   * match the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the
   * first character must be a lowercase letter, and all following characters
   * must be a dash, lowercase letter, or digit, except the last character,
   * which cannot be a dash.
   *
   * @var string
   */
  public $redundantInterface;
  /**
   * The URI of the subnetwork resource that this interface belongs to, which
   * must be in the same region as the Cloud Router. When you establish a BGP
   * session to a VM instance using this interface, the VM instance must belong
   * to the same subnetwork as the subnetwork specified here.
   *
   * @var string
   */
  public $subnetwork;

  /**
   * IP address and range of the interface.        - For Internet Protocol
   * version 4 (IPv4), the IP range must be in theRFC3927 link-local IP address
   * space. The value must    be a CIDR-formatted string, for example,
   * 169.254.0.1/30.    Note: Do not truncate the IP address, as it represents
   * the IP address of    the interface.     - For Internet Protocol version 6
   * (IPv6), the value    must be a unique local address (ULA) range from
   * fdff:1::/64    with a mask length of 126 or less. This value should be a
   * CIDR-formatted    string, for example, fdff:1::1/112. Within the router's
   * VPC, this IPv6 prefix will be reserved exclusively for this connection
   * and cannot be used for any other purpose.
   *
   * @param string $ipRange
   */
  public function setIpRange($ipRange)
  {
    $this->ipRange = $ipRange;
  }
  /**
   * @return string
   */
  public function getIpRange()
  {
    return $this->ipRange;
  }
  /**
   * IP version of this interface.
   *
   * Accepted values: IPV4, IPV6
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
   * URI of the linked Interconnect attachment. It must be in the same region as
   * the router. Each interface can have one linked resource, which can be a VPN
   * tunnel, an Interconnect attachment, or a subnetwork.
   *
   * @param string $linkedInterconnectAttachment
   */
  public function setLinkedInterconnectAttachment($linkedInterconnectAttachment)
  {
    $this->linkedInterconnectAttachment = $linkedInterconnectAttachment;
  }
  /**
   * @return string
   */
  public function getLinkedInterconnectAttachment()
  {
    return $this->linkedInterconnectAttachment;
  }
  /**
   * URI of the linked VPN tunnel, which must be in the same region as the
   * router. Each interface can have one linked resource, which can be a VPN
   * tunnel, an Interconnect attachment, or a subnetwork.
   *
   * @param string $linkedVpnTunnel
   */
  public function setLinkedVpnTunnel($linkedVpnTunnel)
  {
    $this->linkedVpnTunnel = $linkedVpnTunnel;
  }
  /**
   * @return string
   */
  public function getLinkedVpnTunnel()
  {
    return $this->linkedVpnTunnel;
  }
  /**
   * Output only. [Output Only] The resource that configures and manages this
   * interface.        - MANAGED_BY_USER is the default value and can be managed
   * directly    by users.    - MANAGED_BY_ATTACHMENT is an interface that is
   * configured and    managed by Cloud Interconnect, specifically, by an
   * InterconnectAttachment    of type PARTNER. Google automatically creates,
   * updates, and deletes    this type of interface when the PARTNER
   * InterconnectAttachment is    created, updated, or deleted.
   *
   * Accepted values: MANAGED_BY_ATTACHMENT, MANAGED_BY_USER
   *
   * @param self::MANAGEMENT_TYPE_* $managementType
   */
  public function setManagementType($managementType)
  {
    $this->managementType = $managementType;
  }
  /**
   * @return self::MANAGEMENT_TYPE_*
   */
  public function getManagementType()
  {
    return $this->managementType;
  }
  /**
   * Name of this interface entry. The name must be 1-63 characters long, and
   * comply withRFC1035. Specifically, the name must be 1-63 characters long and
   * match the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the
   * first character must be a lowercase letter, and all following characters
   * must be a dash, lowercase letter, or digit, except the last character,
   * which cannot be a dash.
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
   * The regional private internal IP address that is used to establish BGP
   * sessions to a VM instance acting as a third-party Router Appliance, such as
   * a Next Gen Firewall, a Virtual Router, or an SD-WAN VM.
   *
   * @param string $privateIpAddress
   */
  public function setPrivateIpAddress($privateIpAddress)
  {
    $this->privateIpAddress = $privateIpAddress;
  }
  /**
   * @return string
   */
  public function getPrivateIpAddress()
  {
    return $this->privateIpAddress;
  }
  /**
   * Name of the interface that will be redundant with the current interface you
   * are creating. The redundantInterface must belong to the same Cloud Router
   * as the interface here. To establish the BGP session to a Router Appliance
   * VM, you must create two BGP peers. The two BGP peers must be attached to
   * two separate interfaces that are redundant with each other. The
   * redundant_interface must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the redundant_interface must be 1-63 characters long and
   * match the regular expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the
   * first character must be a lowercase letter, and all following characters
   * must be a dash, lowercase letter, or digit, except the last character,
   * which cannot be a dash.
   *
   * @param string $redundantInterface
   */
  public function setRedundantInterface($redundantInterface)
  {
    $this->redundantInterface = $redundantInterface;
  }
  /**
   * @return string
   */
  public function getRedundantInterface()
  {
    return $this->redundantInterface;
  }
  /**
   * The URI of the subnetwork resource that this interface belongs to, which
   * must be in the same region as the Cloud Router. When you establish a BGP
   * session to a VM instance using this interface, the VM instance must belong
   * to the same subnetwork as the subnetwork specified here.
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
class_alias(RouterInterface::class, 'Google_Service_Compute_RouterInterface');
