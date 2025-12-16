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

class GoogleMapsPlacesV1ContextualContent extends \Google\Collection
{
  protected $collection_key = 'reviews';
  protected $justificationsType = GoogleMapsPlacesV1ContextualContentJustification::class;
  protected $justificationsDataType = 'array';
  protected $photosType = GoogleMapsPlacesV1Photo::class;
  protected $photosDataType = 'array';
  protected $reviewsType = GoogleMapsPlacesV1Review::class;
  protected $reviewsDataType = 'array';

  /**
   * Experimental: See
   * https://developers.google.com/maps/documentation/places/web-
   * service/experimental/places-generative for more details. Justifications for
   * the place.
   *
   * @param GoogleMapsPlacesV1ContextualContentJustification[] $justifications
   */
  public function setJustifications($justifications)
  {
    $this->justifications = $justifications;
  }
  /**
   * @return GoogleMapsPlacesV1ContextualContentJustification[]
   */
  public function getJustifications()
  {
    return $this->justifications;
  }
  /**
   * Information (including references) about photos of this place, contexual to
   * the place query.
   *
   * @param GoogleMapsPlacesV1Photo[] $photos
   */
  public function setPhotos($photos)
  {
    $this->photos = $photos;
  }
  /**
   * @return GoogleMapsPlacesV1Photo[]
   */
  public function getPhotos()
  {
    return $this->photos;
  }
  /**
   * List of reviews about this place, contexual to the place query.
   *
   * @param GoogleMapsPlacesV1Review[] $reviews
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
class_alias(GoogleMapsPlacesV1ContextualContent::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1ContextualContent');
