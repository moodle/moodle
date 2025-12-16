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

class SubnetworksExpandIpCidrRangeRequest extends \Google\Model
{
  /**
   * The IP (in CIDR format or netmask) of internal addresses that are legal on
   * this Subnetwork. This range should be disjoint from other subnetworks
   * within this network. This range can only be larger than (i.e. a superset
   * of) the range previously defined before the update.
   *
   * @var string
   */
  public $ipCidrRange;

  /**
   * The IP (in CIDR format or netmask) of internal addresses that are legal on
   * this Subnetwork. This range should be disjoint from other subnetworks
   * within this network. This range can only be larger than (i.e. a superset
   * of) the range previously defined before the update.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubnetworksExpandIpCidrRangeRequest::class, 'Google_Service_Compute_SubnetworksExpandIpCidrRangeRequest');
