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

namespace Google\Service\Batch;

class LocationPolicy extends \Google\Collection
{
  protected $collection_key = 'allowedLocations';
  /**
   * A list of allowed location names represented by internal URLs. Each
   * location can be a region or a zone. Only one region or multiple zones in
   * one region is supported now. For example, ["regions/us-central1"] allow VMs
   * in any zones in region us-central1. ["zones/us-central1-a", "zones/us-
   * central1-c"] only allow VMs in zones us-central1-a and us-central1-c.
   * Mixing locations from different regions would cause errors. For example,
   * ["regions/us-central1", "zones/us-central1-a", "zones/us-central1-b",
   * "zones/us-west1-a"] contains locations from two distinct regions: us-
   * central1 and us-west1. This combination will trigger an error.
   *
   * @var string[]
   */
  public $allowedLocations;

  /**
   * A list of allowed location names represented by internal URLs. Each
   * location can be a region or a zone. Only one region or multiple zones in
   * one region is supported now. For example, ["regions/us-central1"] allow VMs
   * in any zones in region us-central1. ["zones/us-central1-a", "zones/us-
   * central1-c"] only allow VMs in zones us-central1-a and us-central1-c.
   * Mixing locations from different regions would cause errors. For example,
   * ["regions/us-central1", "zones/us-central1-a", "zones/us-central1-b",
   * "zones/us-west1-a"] contains locations from two distinct regions: us-
   * central1 and us-west1. This combination will trigger an error.
   *
   * @param string[] $allowedLocations
   */
  public function setAllowedLocations($allowedLocations)
  {
    $this->allowedLocations = $allowedLocations;
  }
  /**
   * @return string[]
   */
  public function getAllowedLocations()
  {
    return $this->allowedLocations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationPolicy::class, 'Google_Service_Batch_LocationPolicy');
