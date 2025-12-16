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

class GoogleMapsPlacesV1SearchTextRequestLocation extends \Google\Model
{
  protected $rectangleType = GoogleGeoTypeViewport::class;
  protected $rectangleDataType = '';
  /**
   * @var bool
   */
  public $strictRestriction;

  /**
   * @param GoogleGeoTypeViewport
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
  /**
   * @param bool
   */
  public function setStrictRestriction($strictRestriction)
  {
    $this->strictRestriction = $strictRestriction;
  }
  /**
   * @return bool
   */
  public function getStrictRestriction()
  {
    return $this->strictRestriction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1SearchTextRequestLocation::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1SearchTextRequestLocation');
