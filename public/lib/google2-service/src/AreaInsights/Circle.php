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

namespace Google\Service\AreaInsights;

class Circle extends \Google\Model
{
  protected $latLngType = LatLng::class;
  protected $latLngDataType = '';
  /**
   * **Format:** Must be in the format `places/PLACE_ID`, where `PLACE_ID` is
   * the unique identifier of a place. For example:
   * `places/ChIJgUbEo8cfqokR5lP9_Wh_DaM`.
   *
   * @var string
   */
  public $place;
  /**
   * Optional. The radius of the circle in meters
   *
   * @var int
   */
  public $radius;

  /**
   * The latitude and longitude of the center of the circle.
   *
   * @param LatLng $latLng
   */
  public function setLatLng(LatLng $latLng)
  {
    $this->latLng = $latLng;
  }
  /**
   * @return LatLng
   */
  public function getLatLng()
  {
    return $this->latLng;
  }
  /**
   * **Format:** Must be in the format `places/PLACE_ID`, where `PLACE_ID` is
   * the unique identifier of a place. For example:
   * `places/ChIJgUbEo8cfqokR5lP9_Wh_DaM`.
   *
   * @param string $place
   */
  public function setPlace($place)
  {
    $this->place = $place;
  }
  /**
   * @return string
   */
  public function getPlace()
  {
    return $this->place;
  }
  /**
   * Optional. The radius of the circle in meters
   *
   * @param int $radius
   */
  public function setRadius($radius)
  {
    $this->radius = $radius;
  }
  /**
   * @return int
   */
  public function getRadius()
  {
    return $this->radius;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Circle::class, 'Google_Service_AreaInsights_Circle');
