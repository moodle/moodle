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

class GoogleMapsPlacesV1AutocompletePlacesRequestLocationBias extends \Google\Model
{
  protected $circleType = GoogleMapsPlacesV1Circle::class;
  protected $circleDataType = '';
  protected $rectangleType = GoogleGeoTypeViewport::class;
  protected $rectangleDataType = '';

  /**
   * A circle defined by a center point and radius.
   *
   * @param GoogleMapsPlacesV1Circle $circle
   */
  public function setCircle(GoogleMapsPlacesV1Circle $circle)
  {
    $this->circle = $circle;
  }
  /**
   * @return GoogleMapsPlacesV1Circle
   */
  public function getCircle()
  {
    return $this->circle;
  }
  /**
   * A viewport defined by a northeast and a southwest corner.
   *
   * @param GoogleGeoTypeViewport $rectangle
   */
  public function setRectangle(GoogleGeoTypeViewport $rectangle)
  {
    $this->rectangle = $rectangle;
  }
  /**
   * @return GoogleGeoTypeViewport
   */
  public function getRectangle()
  {
    return $this->rectangle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AutocompletePlacesRequestLocationBias::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AutocompletePlacesRequestLocationBias');
