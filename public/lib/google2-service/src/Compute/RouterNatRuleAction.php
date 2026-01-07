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

class RouterNatRuleAction extends \Google\Collection
{
  protected $collection_key = 'sourceNatDrainRanges';
  /**
   * A list of URLs of the IP resources used for this NAT rule. These IP
   * addresses must be valid static external IP addresses assigned to the
   * project. This field is used for public NAT.
   *
   * @var string[]
   */
  public $sourceNatActiveIps;
  /**
   * A list of URLs of the subnetworks used as source ranges for this NAT Rule.
   * These subnetworks must have purpose set to PRIVATE_NAT. This field is used
   * for private NAT.
   *
   * @var string[]
   */
  public $sourceNatActiveRanges;
  /**
   * A list of URLs of the IP resources to be drained. These IPs must be valid
   * static external IPs that have been assigned to the NAT. These IPs should be
   * used for updating/patching a NAT rule only. This field is used for public
   * NAT.
   *
   * @var string[]
   */
  public $sourceNatDrainIps;
  /**
   * A list of URLs of subnetworks representing source ranges to be drained.
   * This is only supported on patch/update, and these subnetworks must have
   * previously been used as active ranges in this NAT Rule. This field is used
   * for private NAT.
   *
   * @var string[]
   */
  public $sourceNatDrainRanges;

  /**
   * A list of URLs of the IP resources used for this NAT rule. These IP
   * addresses must be valid static external IP addresses assigned to the
   * project. This field is used for public NAT.
   *
   * @param string[] $sourceNatActiveIps
   */
  public function setSourceNatActiveIps($sourceNatActiveIps)
  {
    $this->sourceNatActiveIps = $sourceNatActiveIps;
  }
  /**
   * @return string[]
   */
  public function getSourceNatActiveIps()
  {
    return $this->sourceNatActiveIps;
  }
  /**
   * A list of URLs of the subnetworks used as source ranges for this NAT Rule.
   * These subnetworks must have purpose set to PRIVATE_NAT. This field is used
   * for private NAT.
   *
   * @param string[] $sourceNatActiveRanges
   */
  public function setSourceNatActiveRanges($sourceNatActiveRanges)
  {
    $this->sourceNatActiveRanges = $sourceNatActiveRanges;
  }
  /**
   * @return string[]
   */
  public function getSourceNatActiveRanges()
  {
    return $this->sourceNatActiveRanges;
  }
  /**
   * A list of URLs of the IP resources to be drained. These IPs must be valid
   * static external IPs that have been assigned to the NAT. These IPs should be
   * used for updating/patching a NAT rule only. This field is used for public
   * NAT.
   *
   * @param string[] $sourceNatDrainIps
   */
  public function setSourceNatDrainIps($sourceNatDrainIps)
  {
    $this->sourceNatDrainIps = $sourceNatDrainIps;
  }
  /**
   * @return string[]
   */
  public function getSourceNatDrainIps()
  {
    return $this->sourceNatDrainIps;
  }
  /**
   * A list of URLs of subnetworks representing source ranges to be drained.
   * This is only supported on patch/update, and these subnetworks must have
   * previously been used as active ranges in this NAT Rule. This field is used
   * for private NAT.
   *
   * @param string[] $sourceNatDrainRanges
   */
  public function setSourceNatDrainRanges($sourceNatDrainRanges)
  {
    $this->sourceNatDrainRanges = $sourceNatDrainRanges;
  }
  /**
   * @return string[]
   */
  public function getSourceNatDrainRanges()
  {
    return $this->sourceNatDrainRanges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterNatRuleAction::class, 'Google_Service_Compute_RouterNatRuleAction');
