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

class SnapshotSettingsStorageLocationSettings extends \Google\Model
{
  /**
   * Store snapshot in the same region as with the originating disk. No
   * additional parameters are needed.
   */
  public const POLICY_LOCAL_REGION = 'LOCAL_REGION';
  /**
   * Store snapshot in the nearest multi region Cloud Storage bucket, relative
   * to the originating disk. No additional parameters are needed.
   */
  public const POLICY_NEAREST_MULTI_REGION = 'NEAREST_MULTI_REGION';
  /**
   * Store snapshot in the specific locations, as specified by the user. The
   * list of regions to store must be defined under the `locations` field.
   */
  public const POLICY_SPECIFIC_LOCATIONS = 'SPECIFIC_LOCATIONS';
  public const POLICY_STORAGE_LOCATION_POLICY_UNSPECIFIED = 'STORAGE_LOCATION_POLICY_UNSPECIFIED';
  protected $locationsType = SnapshotSettingsStorageLocationSettingsStorageLocationPreference::class;
  protected $locationsDataType = 'map';
  /**
   * The chosen location policy.
   *
   * @var string
   */
  public $policy;

  /**
   * When the policy is SPECIFIC_LOCATIONS, snapshots will be stored in the
   * locations listed in this field. Keys are Cloud Storage bucket locations.
   * Only one location can be specified.
   *
   * @param SnapshotSettingsStorageLocationSettingsStorageLocationPreference[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return SnapshotSettingsStorageLocationSettingsStorageLocationPreference[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * The chosen location policy.
   *
   * Accepted values: LOCAL_REGION, NEAREST_MULTI_REGION, SPECIFIC_LOCATIONS,
   * STORAGE_LOCATION_POLICY_UNSPECIFIED
   *
   * @param self::POLICY_* $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return self::POLICY_*
   */
  public function getPolicy()
  {
    return $this->policy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SnapshotSettingsStorageLocationSettings::class, 'Google_Service_Compute_SnapshotSettingsStorageLocationSettings');
