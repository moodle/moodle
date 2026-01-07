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

class GoogleMapsPlacesV1Circle extends \Google\Model
{
  protected $centerType = GoogleTypeLatLng::class;
  protected $centerDataType = '';
  /**
   * Required. Radius measured in meters. The radius must be within [0.0,
   * 50000.0].
   *
   * @var 
   */
  public $radius;

  /**
   * Required. Center latitude and longitude. The range of latitude must be
   * within [-90.0, 90.0]. The range of the longitude must be within [-180.0,
   * 180.0].
   *
   * @param GoogleTypeLatLng $center
   */
  public function setCenter(GoogleTypeLatLng $center)
  {
    $this->center = $center;
  }
  /**
   * @return GoogleTypeLatLng
   */
  public function getCenter()
  {
    return $this->center;
  }
  public function setRadius($radius)
  {
    $this->radius = $radius;
  }
  public function getRadius()
  {
    return $this->radius;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1Circle::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1Circle');
