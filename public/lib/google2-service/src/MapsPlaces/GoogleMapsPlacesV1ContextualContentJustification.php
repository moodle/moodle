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

class GoogleMapsPlacesV1ContextualContentJustification extends \Google\Model
{
  protected $businessAvailabilityAttributesJustificationType = GoogleMapsPlacesV1ContextualContentJustificationBusinessAvailabilityAttributesJustification::class;
  protected $businessAvailabilityAttributesJustificationDataType = '';
  protected $reviewJustificationType = GoogleMapsPlacesV1ContextualContentJustificationReviewJustification::class;
  protected $reviewJustificationDataType = '';

  /**
   * Experimental: See
   * https://developers.google.com/maps/documentation/places/web-
   * service/experimental/places-generative for more details.
   *
   * @param GoogleMapsPlacesV1ContextualContentJustificationBusinessAvailabilityAttributesJustification $businessAvailabilityAttributesJustification
   */
  public function setBusinessAvailabilityAttributesJustification(GoogleMapsPlacesV1ContextualContentJustificationBusinessAvailabilityAttributesJustification $businessAvailabilityAttributesJustification)
  {
    $this->businessAvailabilityAttributesJustification = $businessAvailabilityAttributesJustification;
  }
  /**
   * @return GoogleMapsPlacesV1ContextualContentJustificationBusinessAvailabilityAttributesJustification
   */
  public function getBusinessAvailabilityAttributesJustification()
  {
    return $this->businessAvailabilityAttributesJustification;
  }
  /**
   * Experimental: See
   * https://developers.google.com/maps/documentation/places/web-
   * service/experimental/places-generative for more details.
   *
   * @param GoogleMapsPlacesV1ContextualContentJustificationReviewJustification $reviewJustification
   */
  public function setReviewJustification(GoogleMapsPlacesV1ContextualContentJustificationReviewJustification $reviewJustification)
  {
    $this->reviewJustification = $reviewJustification;
  }
  /**
   * @return GoogleMapsPlacesV1ContextualContentJustificationReviewJustification
   */
  public function getReviewJustification()
  {
    return $this->reviewJustification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1ContextualContentJustification::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1ContextualContentJustification');
