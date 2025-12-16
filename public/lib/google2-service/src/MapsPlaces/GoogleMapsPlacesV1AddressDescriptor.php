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

class GoogleMapsPlacesV1AddressDescriptor extends \Google\Collection
{
  protected $collection_key = 'landmarks';
  protected $areasType = GoogleMapsPlacesV1AddressDescriptorArea::class;
  protected $areasDataType = 'array';
  protected $landmarksType = GoogleMapsPlacesV1AddressDescriptorLandmark::class;
  protected $landmarksDataType = 'array';

  /**
   * A ranked list of containing or adjacent areas. The most recognizable and
   * precise areas are ranked first.
   *
   * @param GoogleMapsPlacesV1AddressDescriptorArea[] $areas
   */
  public function setAreas($areas)
  {
    $this->areas = $areas;
  }
  /**
   * @return GoogleMapsPlacesV1AddressDescriptorArea[]
   */
  public function getAreas()
  {
    return $this->areas;
  }
  /**
   * A ranked list of nearby landmarks. The most recognizable and nearby
   * landmarks are ranked first.
   *
   * @param GoogleMapsPlacesV1AddressDescriptorLandmark[] $landmarks
   */
  public function setLandmarks($landmarks)
  {
    $this->landmarks = $landmarks;
  }
  /**
   * @return GoogleMapsPlacesV1AddressDescriptorLandmark[]
   */
  public function getLandmarks()
  {
    return $this->landmarks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AddressDescriptor::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AddressDescriptor');
