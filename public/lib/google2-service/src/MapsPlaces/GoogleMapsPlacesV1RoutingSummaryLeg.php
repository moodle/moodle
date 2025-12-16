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

namespace Google\Service\MapsPlaces;

class GoogleMapsPlacesV1RoutingSummaryLeg extends \Google\Model
{
  /**
   * The distance of this leg of the trip.
   *
   * @var int
   */
  public $distanceMeters;
  /**
   * The time it takes to complete this leg of the trip.
   *
   * @var string
   */
  public $duration;

  /**
   * The distance of this leg of the trip.
   *
   * @param int $distanceMeters
   */
  public function setDistanceMeters($distanceMeters)
  {
    $this->distanceMeters = $distanceMeters;
  }
  /**
   * @return int
   */
  public function getDistanceMeters()
  {
    return $this->distanceMeters;
  }
  /**
   * The time it takes to complete this leg of the trip.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1RoutingSummaryLeg::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1RoutingSummaryLeg');
