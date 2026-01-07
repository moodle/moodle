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

class FutureResourcesSpecLocationPolicy extends \Google\Model
{
  protected $locationsType = FutureResourcesSpecLocationPolicyLocation::class;
  protected $locationsDataType = 'map';

  /**
   * Preferences for specified locations. Keys of the map are locations - zones,
   * in format of 'zones/'. Values are preferences for the zones. If a zone is
   * not specified in this map, it is ALLOWed.
   *
   * @param FutureResourcesSpecLocationPolicyLocation[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return FutureResourcesSpecLocationPolicyLocation[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureResourcesSpecLocationPolicy::class, 'Google_Service_Compute_FutureResourcesSpecLocationPolicy');
