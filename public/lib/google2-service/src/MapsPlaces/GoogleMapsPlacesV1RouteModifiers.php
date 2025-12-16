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

class GoogleMapsPlacesV1RouteModifiers extends \Google\Model
{
  /**
   * Optional. When set to true, avoids ferries where reasonable, giving
   * preference to routes not containing ferries. Applies only to the `DRIVE`
   * and `TWO_WHEELER` `TravelMode`.
   *
   * @var bool
   */
  public $avoidFerries;
  /**
   * Optional. When set to true, avoids highways where reasonable, giving
   * preference to routes not containing highways. Applies only to the `DRIVE`
   * and `TWO_WHEELER` `TravelMode`.
   *
   * @var bool
   */
  public $avoidHighways;
  /**
   * Optional. When set to true, avoids navigating indoors where reasonable,
   * giving preference to routes not containing indoor navigation. Applies only
   * to the `WALK` `TravelMode`.
   *
   * @var bool
   */
  public $avoidIndoor;
  /**
   * Optional. When set to true, avoids toll roads where reasonable, giving
   * preference to routes not containing toll roads. Applies only to the `DRIVE`
   * and `TWO_WHEELER` `TravelMode`.
   *
   * @var bool
   */
  public $avoidTolls;

  /**
   * Optional. When set to true, avoids ferries where reasonable, giving
   * preference to routes not containing ferries. Applies only to the `DRIVE`
   * and `TWO_WHEELER` `TravelMode`.
   *
   * @param bool $avoidFerries
   */
  public function setAvoidFerries($avoidFerries)
  {
    $this->avoidFerries = $avoidFerries;
  }
  /**
   * @return bool
   */
  public function getAvoidFerries()
  {
    return $this->avoidFerries;
  }
  /**
   * Optional. When set to true, avoids highways where reasonable, giving
   * preference to routes not containing highways. Applies only to the `DRIVE`
   * and `TWO_WHEELER` `TravelMode`.
   *
   * @param bool $avoidHighways
   */
  public function setAvoidHighways($avoidHighways)
  {
    $this->avoidHighways = $avoidHighways;
  }
  /**
   * @return bool
   */
  public function getAvoidHighways()
  {
    return $this->avoidHighways;
  }
  /**
   * Optional. When set to true, avoids navigating indoors where reasonable,
   * giving preference to routes not containing indoor navigation. Applies only
   * to the `WALK` `TravelMode`.
   *
   * @param bool $avoidIndoor
   */
  public function setAvoidIndoor($avoidIndoor)
  {
    $this->avoidIndoor = $avoidIndoor;
  }
  /**
   * @return bool
   */
  public function getAvoidIndoor()
  {
    return $this->avoidIndoor;
  }
  /**
   * Optional. When set to true, avoids toll roads where reasonable, giving
   * preference to routes not containing toll roads. Applies only to the `DRIVE`
   * and `TWO_WHEELER` `TravelMode`.
   *
   * @param bool $avoidTolls
   */
  public function setAvoidTolls($avoidTolls)
  {
    $this->avoidTolls = $avoidTolls;
  }
  /**
   * @return bool
   */
  public function getAvoidTolls()
  {
    return $this->avoidTolls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1RouteModifiers::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1RouteModifiers');
