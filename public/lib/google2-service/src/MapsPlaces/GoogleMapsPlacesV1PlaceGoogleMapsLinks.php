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

class GoogleMapsPlacesV1PlaceGoogleMapsLinks extends \Google\Model
{
  /**
   * A link to show the directions to the place. The link only populates the
   * destination location and uses the default travel mode `DRIVE`.
   *
   * @var string
   */
  public $directionsUri;
  /**
   * A link to show photos of this place on Google Maps.
   *
   * @var string
   */
  public $photosUri;
  /**
   * A link to show this place.
   *
   * @var string
   */
  public $placeUri;
  /**
   * A link to show reviews of this place on Google Maps.
   *
   * @var string
   */
  public $reviewsUri;
  /**
   * A link to write a review for this place on Google Maps.
   *
   * @var string
   */
  public $writeAReviewUri;

  /**
   * A link to show the directions to the place. The link only populates the
   * destination location and uses the default travel mode `DRIVE`.
   *
   * @param string $directionsUri
   */
  public function setDirectionsUri($directionsUri)
  {
    $this->directionsUri = $directionsUri;
  }
  /**
   * @return string
   */
  public function getDirectionsUri()
  {
    return $this->directionsUri;
  }
  /**
   * A link to show photos of this place on Google Maps.
   *
   * @param string $photosUri
   */
  public function setPhotosUri($photosUri)
  {
    $this->photosUri = $photosUri;
  }
  /**
   * @return string
   */
  public function getPhotosUri()
  {
    return $this->photosUri;
  }
  /**
   * A link to show this place.
   *
   * @param string $placeUri
   */
  public function setPlaceUri($placeUri)
  {
    $this->placeUri = $placeUri;
  }
  /**
   * @return string
   */
  public function getPlaceUri()
  {
    return $this->placeUri;
  }
  /**
   * A link to show reviews of this place on Google Maps.
   *
   * @param string $reviewsUri
   */
  public function setReviewsUri($reviewsUri)
  {
    $this->reviewsUri = $reviewsUri;
  }
  /**
   * @return string
   */
  public function getReviewsUri()
  {
    return $this->reviewsUri;
  }
  /**
   * A link to write a review for this place on Google Maps.
   *
   * @param string $writeAReviewUri
   */
  public function setWriteAReviewUri($writeAReviewUri)
  {
    $this->writeAReviewUri = $writeAReviewUri;
  }
  /**
   * @return string
   */
  public function getWriteAReviewUri()
  {
    return $this->writeAReviewUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceGoogleMapsLinks::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceGoogleMapsLinks');
