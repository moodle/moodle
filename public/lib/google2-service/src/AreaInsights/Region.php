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

class Region extends \Google\Model
{
  /**
   * The [place ID](https://developers.google.com/maps/documentation/places/web-
   * service/place-id) of the geographic region. Not all region types are
   * supported; see documentation for details. **Format:** Must be in the format
   * `places/PLACE_ID`, where `PLACE_ID` is the unique identifier of a place.
   * For example: `places/ChIJPV4oX_65j4ARVW8IJ6IJUYs`.
   *
   * @var string
   */
  public $place;

  /**
   * The [place ID](https://developers.google.com/maps/documentation/places/web-
   * service/place-id) of the geographic region. Not all region types are
   * supported; see documentation for details. **Format:** Must be in the format
   * `places/PLACE_ID`, where `PLACE_ID` is the unique identifier of a place.
   * For example: `places/ChIJPV4oX_65j4ARVW8IJ6IJUYs`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Region::class, 'Google_Service_AreaInsights_Region');
