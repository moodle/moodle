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

class GoogleGeoTypeViewport extends \Google\Model
{
  protected $highType = GoogleTypeLatLng::class;
  protected $highDataType = '';
  protected $lowType = GoogleTypeLatLng::class;
  protected $lowDataType = '';

  /**
   * Required. The high point of the viewport.
   *
   * @param GoogleTypeLatLng $high
   */
  public function setHigh(GoogleTypeLatLng $high)
  {
    $this->high = $high;
  }
  /**
   * @return GoogleTypeLatLng
   */
  public function getHigh()
  {
    return $this->high;
  }
  /**
   * Required. The low point of the viewport.
   *
   * @param GoogleTypeLatLng $low
   */
  public function setLow(GoogleTypeLatLng $low)
  {
    $this->low = $low;
  }
  /**
   * @return GoogleTypeLatLng
   */
  public function getLow()
  {
    return $this->low;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleGeoTypeViewport::class, 'Google_Service_MapsPlaces_GoogleGeoTypeViewport');
