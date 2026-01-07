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

class GoogleMapsPlacesV1PlaceAccessibilityOptions extends \Google\Model
{
  /**
   * Places has wheelchair accessible entrance.
   *
   * @var bool
   */
  public $wheelchairAccessibleEntrance;
  /**
   * Place offers wheelchair accessible parking.
   *
   * @var bool
   */
  public $wheelchairAccessibleParking;
  /**
   * Place has wheelchair accessible restroom.
   *
   * @var bool
   */
  public $wheelchairAccessibleRestroom;
  /**
   * Place has wheelchair accessible seating.
   *
   * @var bool
   */
  public $wheelchairAccessibleSeating;

  /**
   * Places has wheelchair accessible entrance.
   *
   * @param bool $wheelchairAccessibleEntrance
   */
  public function setWheelchairAccessibleEntrance($wheelchairAccessibleEntrance)
  {
    $this->wheelchairAccessibleEntrance = $wheelchairAccessibleEntrance;
  }
  /**
   * @return bool
   */
  public function getWheelchairAccessibleEntrance()
  {
    return $this->wheelchairAccessibleEntrance;
  }
  /**
   * Place offers wheelchair accessible parking.
   *
   * @param bool $wheelchairAccessibleParking
   */
  public function setWheelchairAccessibleParking($wheelchairAccessibleParking)
  {
    $this->wheelchairAccessibleParking = $wheelchairAccessibleParking;
  }
  /**
   * @return bool
   */
  public function getWheelchairAccessibleParking()
  {
    return $this->wheelchairAccessibleParking;
  }
  /**
   * Place has wheelchair accessible restroom.
   *
   * @param bool $wheelchairAccessibleRestroom
   */
  public function setWheelchairAccessibleRestroom($wheelchairAccessibleRestroom)
  {
    $this->wheelchairAccessibleRestroom = $wheelchairAccessibleRestroom;
  }
  /**
   * @return bool
   */
  public function getWheelchairAccessibleRestroom()
  {
    return $this->wheelchairAccessibleRestroom;
  }
  /**
   * Place has wheelchair accessible seating.
   *
   * @param bool $wheelchairAccessibleSeating
   */
  public function setWheelchairAccessibleSeating($wheelchairAccessibleSeating)
  {
    $this->wheelchairAccessibleSeating = $wheelchairAccessibleSeating;
  }
  /**
   * @return bool
   */
  public function getWheelchairAccessibleSeating()
  {
    return $this->wheelchairAccessibleSeating;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceAccessibilityOptions::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceAccessibilityOptions');
