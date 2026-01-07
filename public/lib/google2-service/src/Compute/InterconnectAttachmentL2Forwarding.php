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

class InterconnectAttachmentL2Forwarding extends \Google\Model
{
  protected $applianceMappingsType = InterconnectAttachmentL2ForwardingApplianceMapping::class;
  protected $applianceMappingsDataType = 'map';
  /**
   * Optional. A single IPv4 or IPv6 address used as the default destination IP
   * when there is no VLAN mapping result found.
   *
   * Unset field (null-value) indicates the unmatched packet should be dropped.
   *
   * @var string
   */
  public $defaultApplianceIpAddress;
  protected $geneveHeaderType = InterconnectAttachmentL2ForwardingGeneveHeader::class;
  protected $geneveHeaderDataType = '';
  /**
   * Required. Resource URL of the network to which this attachment belongs.
   *
   * @var string
   */
  public $network;
  /**
   * Required. A single IPv4 or IPv6 address. This address will be used as the
   * source IP address for packets sent to the appliances, and must be used as
   * the destination IP address for packets that should be sent out through this
   * attachment.
   *
   * @var string
   */
  public $tunnelEndpointIpAddress;

  /**
   * Optional. A map of VLAN tags to appliances and optional inner mapping
   * rules. If VLANs are not explicitly mapped to any appliance, the
   * defaultApplianceIpAddress is used.
   *
   * Each VLAN tag can be a single number or a range of numbers in the range of
   * 1 to 4094, e.g., "1" or "4001-4094". Non-empty and non-overlapping VLAN tag
   * ranges are enforced, and violating operations will be rejected.
   *
   * The VLAN tags in the Ethernet header must use an ethertype value of 0x88A8
   * or 0x8100.
   *
   * @param InterconnectAttachmentL2ForwardingApplianceMapping[] $applianceMappings
   */
  public function setApplianceMappings($applianceMappings)
  {
    $this->applianceMappings = $applianceMappings;
  }
  /**
   * @return InterconnectAttachmentL2ForwardingApplianceMapping[]
   */
  public function getApplianceMappings()
  {
    return $this->applianceMappings;
  }
  /**
   * Optional. A single IPv4 or IPv6 address used as the default destination IP
   * when there is no VLAN mapping result found.
   *
   * Unset field (null-value) indicates the unmatched packet should be dropped.
   *
   * @param string $defaultApplianceIpAddress
   */
  public function setDefaultApplianceIpAddress($defaultApplianceIpAddress)
  {
    $this->defaultApplianceIpAddress = $defaultApplianceIpAddress;
  }
  /**
   * @return string
   */
  public function getDefaultApplianceIpAddress()
  {
    return $this->defaultApplianceIpAddress;
  }
  /**
   * Optional. It represents the structure of a Geneve (Generic Network
   * Virtualization Encapsulation) header, as defined in RFC8926. It
   * encapsulates packets from various protocols (e.g., Ethernet, IPv4, IPv6)
   * for use in network virtualization environments.
   *
   * @param InterconnectAttachmentL2ForwardingGeneveHeader $geneveHeader
   */
  public function setGeneveHeader(InterconnectAttachmentL2ForwardingGeneveHeader $geneveHeader)
  {
    $this->geneveHeader = $geneveHeader;
  }
  /**
   * @return InterconnectAttachmentL2ForwardingGeneveHeader
   */
  public function getGeneveHeader()
  {
    return $this->geneveHeader;
  }
  /**
   * Required. Resource URL of the network to which this attachment belongs.
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
   * Required. A single IPv4 or IPv6 address. This address will be used as the
   * source IP address for packets sent to the appliances, and must be used as
   * the destination IP address for packets that should be sent out through this
   * attachment.
   *
   * @param string $tunnelEndpointIpAddress
   */
  public function setTunnelEndpointIpAddress($tunnelEndpointIpAddress)
  {
    $this->tunnelEndpointIpAddress = $tunnelEndpointIpAddress;
  }
  /**
   * @return string
   */
  public function getTunnelEndpointIpAddress()
  {
    return $this->tunnelEndpointIpAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentL2Forwarding::class, 'Google_Service_Compute_InterconnectAttachmentL2Forwarding');
