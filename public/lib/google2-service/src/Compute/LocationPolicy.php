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

class LocationPolicy extends \Google\Model
{
  /**
   * GCE picks zones for creating VM instances to fulfill the requested number
   * of VMs within present resource constraints and to maximize utilization of
   * unused zonal reservations. Recommended for batch workloads that do not
   * require high availability.
   */
  public const TARGET_SHAPE_ANY = 'ANY';
  /**
   * GCE always selects a single zone for all the VMs, optimizing for resource
   * quotas, available reservations and general capacity. Recommended for batch
   * workloads that cannot tollerate distribution over multiple zones. This the
   * default shape in Bulk Insert and Capacity Advisor APIs.
   */
  public const TARGET_SHAPE_ANY_SINGLE_ZONE = 'ANY_SINGLE_ZONE';
  /**
   * GCE prioritizes acquisition of resources, scheduling VMs in zones where
   * resources are available while distributing VMs as evenly as possible across
   * allowed zones to minimize the impact of zonal failure. Recommended for
   * highly available serving workloads.
   */
  public const TARGET_SHAPE_BALANCED = 'BALANCED';
  protected $locationsType = LocationPolicyLocation::class;
  protected $locationsDataType = 'map';
  /**
   * Strategy for distributing VMs across zones in a region.
   *
   * @var string
   */
  public $targetShape;

  /**
   * Location configurations mapped by location name. Currently only zone names
   * are supported and must be represented as valid internal URLs, such as
   * zones/us-central1-a.
   *
   * @param LocationPolicyLocation[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return LocationPolicyLocation[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * Strategy for distributing VMs across zones in a region.
   *
   * Accepted values: ANY, ANY_SINGLE_ZONE, BALANCED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationPolicy::class, 'Google_Service_Compute_LocationPolicy');
