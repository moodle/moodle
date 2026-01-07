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

class SubnetworkSecondaryRange extends \Google\Model
{
  /**
   * The range of IP addresses belonging to this subnetwork secondary range.
   * Provide this property when you create the subnetwork. Ranges must be unique
   * and non-overlapping with all primary and secondary IP ranges within a
   * network. Only IPv4 is supported. The range can be any range listed in
   * theValid ranges list.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * The name associated with this subnetwork secondary range, used when adding
   * an alias IP range to a VM instance. The name must be 1-63 characters long,
   * and comply withRFC1035. The name must be unique within the subnetwork.
   *
   * @var string
   */
  public $rangeName;
  /**
   * The URL of the reserved internal range.
   *
   * @var string
   */
  public $reservedInternalRange;

  /**
   * The range of IP addresses belonging to this subnetwork secondary range.
   * Provide this property when you create the subnetwork. Ranges must be unique
   * and non-overlapping with all primary and secondary IP ranges within a
   * network. Only IPv4 is supported. The range can be any range listed in
   * theValid ranges list.
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
  /**
   * The name associated with this subnetwork secondary range, used when adding
   * an alias IP range to a VM instance. The name must be 1-63 characters long,
   * and comply withRFC1035. The name must be unique within the subnetwork.
   *
   * @param string $rangeName
   */
  public function setRangeName($rangeName)
  {
    $this->rangeName = $rangeName;
  }
  /**
   * @return string
   */
  public function getRangeName()
  {
    return $this->rangeName;
  }
  /**
   * The URL of the reserved internal range.
   *
   * @param string $reservedInternalRange
   */
  public function setReservedInternalRange($reservedInternalRange)
  {
    $this->reservedInternalRange = $reservedInternalRange;
  }
  /**
   * @return string
   */
  public function getReservedInternalRange()
  {
    return $this->reservedInternalRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubnetworkSecondaryRange::class, 'Google_Service_Compute_SubnetworkSecondaryRange');
