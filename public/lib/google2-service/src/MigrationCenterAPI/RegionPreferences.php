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

namespace Google\Service\MigrationCenterAPI;

class RegionPreferences extends \Google\Collection
{
  protected $collection_key = 'preferredRegions';
  /**
   * A list of preferred regions, ordered by the most preferred region first.
   * Set only valid Google Cloud region names. See
   * https://cloud.google.com/compute/docs/regions-zones for available regions.
   *
   * @var string[]
   */
  public $preferredRegions;

  /**
   * A list of preferred regions, ordered by the most preferred region first.
   * Set only valid Google Cloud region names. See
   * https://cloud.google.com/compute/docs/regions-zones for available regions.
   *
   * @param string[] $preferredRegions
   */
  public function setPreferredRegions($preferredRegions)
  {
    $this->preferredRegions = $preferredRegions;
  }
  /**
   * @return string[]
   */
  public function getPreferredRegions()
  {
    return $this->preferredRegions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionPreferences::class, 'Google_Service_MigrationCenterAPI_RegionPreferences');
