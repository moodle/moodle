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

class InterconnectAttachmentL2ForwardingApplianceMappingInnerVlanToApplianceMapping extends \Google\Collection
{
  protected $collection_key = 'innerVlanTags';
  /**
   * Required in this object. A single IPv4 or IPv6 address used as the
   * destination IP address for ingress packets that match on both VLAN tags.
   *
   * @var string
   */
  public $innerApplianceIpAddress;
  /**
   * Required in this object. Used to match the inner VLAN tag on the packet.
   * Each entry can be a single number or a range of numbers in the range of 1
   * to 4094, e.g., ["1", "4001-4094"] is valid. Non-empty and Non-overlapping
   * VLAN tag ranges are enforced, and violating operations will be rejected.
   *
   * The inner VLAN tags must have an ethertype value of 0x8100.
   *
   * @var string[]
   */
  public $innerVlanTags;

  /**
   * Required in this object. A single IPv4 or IPv6 address used as the
   * destination IP address for ingress packets that match on both VLAN tags.
   *
   * @param string $innerApplianceIpAddress
   */
  public function setInnerApplianceIpAddress($innerApplianceIpAddress)
  {
    $this->innerApplianceIpAddress = $innerApplianceIpAddress;
  }
  /**
   * @return string
   */
  public function getInnerApplianceIpAddress()
  {
    return $this->innerApplianceIpAddress;
  }
  /**
   * Required in this object. Used to match the inner VLAN tag on the packet.
   * Each entry can be a single number or a range of numbers in the range of 1
   * to 4094, e.g., ["1", "4001-4094"] is valid. Non-empty and Non-overlapping
   * VLAN tag ranges are enforced, and violating operations will be rejected.
   *
   * The inner VLAN tags must have an ethertype value of 0x8100.
   *
   * @param string[] $innerVlanTags
   */
  public function setInnerVlanTags($innerVlanTags)
  {
    $this->innerVlanTags = $innerVlanTags;
  }
  /**
   * @return string[]
   */
  public function getInnerVlanTags()
  {
    return $this->innerVlanTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentL2ForwardingApplianceMappingInnerVlanToApplianceMapping::class, 'Google_Service_Compute_InterconnectAttachmentL2ForwardingApplianceMappingInnerVlanToApplianceMapping');
