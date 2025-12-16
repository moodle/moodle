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

class DistributionPolicy extends \Google\Collection
{
  /**
   * The group picks zones for creating VM instances to fulfill the requested
   * number of VMs within present resource constraints and to maximize
   * utilization of unused zonal reservations. Recommended for batch workloads
   * that do not require high availability.
   */
  public const TARGET_SHAPE_ANY = 'ANY';
  /**
   * The group creates all VM instances within a single zone. The zone is
   * selected based on the present resource constraints and to maximize
   * utilization of unused zonal reservations. Recommended for batch workloads
   * with heavy interprocess communication.
   */
  public const TARGET_SHAPE_ANY_SINGLE_ZONE = 'ANY_SINGLE_ZONE';
  /**
   * The group prioritizes acquisition of resources, scheduling VMs in zones
   * where resources are available while distributing VMs as evenly as possible
   * across selected zones to minimize the impact of zonal failure. Recommended
   * for highly available serving workloads.
   */
  public const TARGET_SHAPE_BALANCED = 'BALANCED';
  /**
   * The group schedules VM instance creation and deletion to achieve and
   * maintain an even number of managed instances across the selected zones. The
   * distribution is even when the number of managed instances does not differ
   * by more than 1 between any two zones. Recommended for highly available
   * serving workloads.
   */
  public const TARGET_SHAPE_EVEN = 'EVEN';
  protected $collection_key = 'zones';
  /**
   * The distribution shape to which the group converges either proactively or
   * on resize events (depending on the value set
   * inupdatePolicy.instanceRedistributionType).
   *
   * @var string
   */
  public $targetShape;
  protected $zonesType = DistributionPolicyZoneConfiguration::class;
  protected $zonesDataType = 'array';

  /**
   * The distribution shape to which the group converges either proactively or
   * on resize events (depending on the value set
   * inupdatePolicy.instanceRedistributionType).
   *
   * Accepted values: ANY, ANY_SINGLE_ZONE, BALANCED, EVEN
   *
   * @param self::TARGET_SHAPE_* $targetShape
   */
  public function setTargetShape($targetShape)
  {
    $this->targetShape = $targetShape;
  }
  /**
   * @return self::TARGET_SHAPE_*
   */
  public function getTargetShape()
  {
    return $this->targetShape;
  }
  /**
   * Zones where the regional managed instance group will create and manage its
   * instances.
   *
   * @param DistributionPolicyZoneConfiguration[] $zones
   */
  public function setZones($zones)
  {
    $this->zones = $zones;
  }
  /**
   * @return DistributionPolicyZoneConfiguration[]
   */
  public function getZones()
  {
    return $this->zones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DistributionPolicy::class, 'Google_Service_Compute_DistributionPolicy');
