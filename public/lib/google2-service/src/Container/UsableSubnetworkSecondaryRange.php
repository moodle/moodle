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

namespace Google\Service\Container;

class UsableSubnetworkSecondaryRange extends \Google\Model
{
  /**
   * UNKNOWN is the zero value of the Status enum. It's not a valid status.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * UNUSED denotes that this range is unclaimed by any cluster.
   */
  public const STATUS_UNUSED = 'UNUSED';
  /**
   * IN_USE_SERVICE denotes that this range is claimed by cluster(s) for
   * services. User-managed services range can be shared between clusters within
   * the same subnetwork.
   */
  public const STATUS_IN_USE_SERVICE = 'IN_USE_SERVICE';
  /**
   * IN_USE_SHAREABLE_POD denotes this range was created by the network admin
   * and is currently claimed by a cluster for pods. It can only be used by
   * other clusters as a pod range.
   */
  public const STATUS_IN_USE_SHAREABLE_POD = 'IN_USE_SHAREABLE_POD';
  /**
   * IN_USE_MANAGED_POD denotes this range was created by GKE and is claimed for
   * pods. It cannot be used for other clusters.
   */
  public const STATUS_IN_USE_MANAGED_POD = 'IN_USE_MANAGED_POD';
  /**
   * The range of IP addresses belonging to this subnetwork secondary range.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * The name associated with this subnetwork secondary range, used when adding
   * an alias IP range to a VM instance.
   *
   * @var string
   */
  public $rangeName;
  /**
   * This field is to determine the status of the secondary range programmably.
   *
   * @var string
   */
  public $status;

  /**
   * The range of IP addresses belonging to this subnetwork secondary range.
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
   * an alias IP range to a VM instance.
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
   * This field is to determine the status of the secondary range programmably.
   *
   * Accepted values: UNKNOWN, UNUSED, IN_USE_SERVICE, IN_USE_SHAREABLE_POD,
   * IN_USE_MANAGED_POD
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsableSubnetworkSecondaryRange::class, 'Google_Service_Container_UsableSubnetworkSecondaryRange');
