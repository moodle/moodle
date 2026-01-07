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

namespace Google\Service\IAMCredentials;

class WorkloadIdentityPoolAllowedLocations extends \Google\Collection
{
  protected $collection_key = 'locations';
  /**
   * Output only. The hex encoded bitmap of the trust boundary locations
   *
   * @var string
   */
  public $encodedLocations;
  /**
   * Output only. The human readable trust boundary locations. For example,
   * ["us-central1", "europe-west1"]
   *
   * @var string[]
   */
  public $locations;

  /**
   * Output only. The hex encoded bitmap of the trust boundary locations
   *
   * @param string $encodedLocations
   */
  public function setEncodedLocations($encodedLocations)
  {
    $this->encodedLocations = $encodedLocations;
  }
  /**
   * @return string
   */
  public function getEncodedLocations()
  {
    return $this->encodedLocations;
  }
  /**
   * Output only. The human readable trust boundary locations. For example,
   * ["us-central1", "europe-west1"]
   *
   * @param string[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return string[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkloadIdentityPoolAllowedLocations::class, 'Google_Service_IAMCredentials_WorkloadIdentityPoolAllowedLocations');
