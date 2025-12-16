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

class GoogleMapsPlacesV1References extends \Google\Collection
{
  protected $collection_key = 'reviews';
  /**
   * @var string[]
   */
  public $places;
  protected $reviewsType = GoogleMapsPlacesV1Review::class;
  protected $reviewsDataType = 'array';

  /**
   * @param string[]
   */
  public function setPlaces($places)
  {
    $this->places = $places;
  }
  /**
   * @return string[]
   */
  public function getPlaces()
  {
    return $this->places;
  }
  /**
   * @param GoogleMapsPlacesV1Review[]
   */
  public function setReviews($reviews)
  {
    $this->reviews = $reviews;
  }
  /**
   * @return GoogleMapsPlacesV1Review[]
   */
  public function getReviews()
  {
    return $this->reviews;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1References::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1References');
