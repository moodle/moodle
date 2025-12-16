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

class GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat extends \Google\Model
{
  protected $mainTextType = GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText::class;
  protected $mainTextDataType = '';
  protected $secondaryTextType = GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText::class;
  protected $secondaryTextDataType = '';

  /**
   * Represents the name of the Place or query.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText $mainText
   */
  public function setMainText(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText $mainText)
  {
    $this->mainText = $mainText;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText
   */
  public function getMainText()
  {
    return $this->mainText;
  }
  /**
   * Represents additional disambiguating features (such as a city or region) to
   * further identify the Place or refine the query.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText $secondaryText
   */
  public function setSecondaryText(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText $secondaryText)
  {
    $this->secondaryText = $secondaryText;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionFormattableText
   */
  public function getSecondaryText()
  {
    return $this->secondaryText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionStructuredFormat');
