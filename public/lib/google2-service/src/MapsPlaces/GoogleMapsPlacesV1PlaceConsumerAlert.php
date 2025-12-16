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

class GoogleMapsPlacesV1PlaceConsumerAlert extends \Google\Model
{
  protected $detailsType = GoogleMapsPlacesV1PlaceConsumerAlertDetails::class;
  protected $detailsDataType = '';
  /**
   * The language code of the consumer alert message. This is a BCP 47 language
   * code.
   *
   * @var string
   */
  public $languageCode;
  /**
   * The overview of the consumer alert message.
   *
   * @var string
   */
  public $overview;

  /**
   * The details of the consumer alert message.
   *
   * @param GoogleMapsPlacesV1PlaceConsumerAlertDetails $details
   */
  public function setDetails(GoogleMapsPlacesV1PlaceConsumerAlertDetails $details)
  {
    $this->details = $details;
  }
  /**
   * @return GoogleMapsPlacesV1PlaceConsumerAlertDetails
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The language code of the consumer alert message. This is a BCP 47 language
   * code.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The overview of the consumer alert message.
   *
   * @param string $overview
   */
  public function setOverview($overview)
  {
    $this->overview = $overview;
  }
  /**
   * @return string
   */
  public function getOverview()
  {
    return $this->overview;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1PlaceConsumerAlert::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1PlaceConsumerAlert');
