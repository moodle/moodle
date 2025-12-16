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

class GoogleMapsPlacesV1AutocompletePlacesResponseSuggestion extends \Google\Model
{
  protected $placePredictionType = GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionPlacePrediction::class;
  protected $placePredictionDataType = '';
  protected $queryPredictionType = GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionQueryPrediction::class;
  protected $queryPredictionDataType = '';

  /**
   * A prediction for a Place.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionPlacePrediction $placePrediction
   */
  public function setPlacePrediction(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionPlacePrediction $placePrediction)
  {
    $this->placePrediction = $placePrediction;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionPlacePrediction
   */
  public function getPlacePrediction()
  {
    return $this->placePrediction;
  }
  /**
   * A prediction for a query.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionQueryPrediction $queryPrediction
   */
  public function setQueryPrediction(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionQueryPrediction $queryPrediction)
  {
    $this->queryPrediction = $queryPrediction;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesResponseSuggestionQueryPrediction
   */
  public function getQueryPrediction()
  {
    return $this->queryPrediction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AutocompletePlacesResponseSuggestion::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AutocompletePlacesResponseSuggestion');
