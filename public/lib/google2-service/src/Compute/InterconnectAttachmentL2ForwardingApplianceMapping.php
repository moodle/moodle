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

class InterconnectAttachmentL2ForwardingApplianceMapping extends \Google\Collection
{
  protected $collection_key = 'innerVlanToApplianceMappings';
  /**
   * Optional. A single IPv4 or IPv6 address used as the destination IP address
   * for ingress packets that match on a VLAN tag, but do not match a more
   * specific inner VLAN tag.
   *
   * Unset field (null-value) indicates both VLAN tags are required to be
   * mapped. Otherwise, defaultApplianceIpAddress is used.
   *
   * @var string
   */
  public $applianceIpAddress;
  protected $innerVlanToApplianceMappingsType = InterconnectAttachmentL2ForwardingApplianceMappingInnerVlanToApplianceMapping::class;
  protected $innerVlanToApplianceMappingsDataType = 'array';
  /**
   * Optional. The name of this appliance mapping rule.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. A single IPv4 or IPv6 address used as the destination IP address
   * for ingress packets that match on a VLAN tag, but do not match a more
   * specific inner VLAN tag.
   *
   * Unset field (null-value) indicates both VLAN tags are required to be
   * mapped. Otherwise, defaultApplianceIpAddress is used.
   *
   * @param string $applianceIpAddress
   */
  public function setApplianceIpAddress($applianceIpAddress)
  {
    $this->applianceIpAddress = $applianceIpAddress;
  }
  /**
   * @return string
   */
  public function getApplianceIpAddress()
  {
    return $this->applianceIpAddress;
  }
  /**
   * Optional. Used to match against the inner VLAN when the packet contains two
   * VLAN tags.
   *
   * A list of mapping rules from inner VLAN tags to IP addresses. If the inner
   * VLAN is not explicitly mapped to an IP address range, the
   * applianceIpAddress is used.
   *
   * @param InterconnectAttachmentL2ForwardingApplianceMappingInnerVlanToApplianceMapping[] $innerVlanToApplianceMappings
   */
  public function setInnerVlanToApplianceMappings($innerVlanToApplianceMappings)
  {
    $this->innerVlanToApplianceMappings = $innerVlanToApplianceMappings;
  }
  /**
   * @return InterconnectAttachmentL2ForwardingApplianceMappingInnerVlanToApplianceMapping[]
   */
  public function getInnerVlanToApplianceMappings()
  {
    return $this->innerVlanToApplianceMappings;
  }
  /**
   * Optional. The name of this appliance mapping rule.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentL2ForwardingApplianceMapping::class, 'Google_Service_Compute_InterconnectAttachmentL2ForwardingApplianceMapping');
