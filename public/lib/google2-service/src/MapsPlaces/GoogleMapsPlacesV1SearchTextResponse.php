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

class GoogleMapsPlacesV1SearchTextResponse extends \Google\Collection
{
  protected $collection_key = 'routingSummaries';
  protected $contextualContentsType = GoogleMapsPlacesV1ContextualContent::class;
  protected $contextualContentsDataType = 'array';
  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted or empty, there are no subsequent pages.
   *
   * @var string
   */
  public $nextPageToken;
  protected $placesType = GoogleMapsPlacesV1Place::class;
  protected $placesDataType = 'array';
  protected $routingSummariesType = GoogleMapsPlacesV1RoutingSummary::class;
  protected $routingSummariesDataType = 'array';
  /**
   * A link allows the user to search with the same text query as specified in
   * the request on Google Maps.
   *
   * @var string
   */
  public $searchUri;

  /**
   * Experimental: See
   * https://developers.google.com/maps/documentation/places/web-
   * service/experimental/places-generative for more details. A list of
   * contextual contents where each entry associates to the corresponding place
   * in the same index in the places field. The contents that are relevant to
   * the `text_query` in the request are preferred. If the contextual content is
   * not available for one of the places, it will return non-contextual content.
   * It will be empty only when the content is unavailable for this place. This
   * list will have as many entries as the list of places if requested.
   *
   * @param GoogleMapsPlacesV1ContextualContent[] $contextualContents
   */
  public function setContextualContents($contextualContents)
  {
    $this->contextualContents = $contextualContents;
  }
  /**
   * @return GoogleMapsPlacesV1ContextualContent[]
   */
  public function getContextualContents()
  {
    return $this->contextualContents;
  }
  /**
   * A token that can be sent as `page_token` to retrieve the next page. If this
   * field is omitted or empty, there are no subsequent pages.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * A list of places that meet the user's text search criteria.
   *
   * @param GoogleMapsPlacesV1Place[] $places
   */
  public function setPlaces($places)
  {
    $this->places = $places;
  }
  /**
   * @return GoogleMapsPlacesV1Place[]
   */
  public function getPlaces()
  {
    return $this->places;
  }
  /**
   * A list of routing summaries where each entry associates to the
   * corresponding place in the same index in the `places` field. If the routing
   * summary is not available for one of the places, it will contain an empty
   * entry. This list will have as many entries as the list of places if
   * requested.
   *
   * @param GoogleMapsPlacesV1RoutingSummary[] $routingSummaries
   */
  public function setRoutingSummaries($routingSummaries)
  {
    $this->routingSummaries = $routingSummaries;
  }
  /**
   * @return GoogleMapsPlacesV1RoutingSummary[]
   */
  public function getRoutingSummaries()
  {
    return $this->routingSummaries;
  }
  /**
   * A link allows the user to search with the same text query as specified in
   * the request on Google Maps.
   *
   * @param string $searchUri
   */
  public function setSearchUri($searchUri)
  {
    $this->searchUri = $searchUri;
  }
  /**
   * @return string
   */
  public function getSearchUri()
  {
    return $this->searchUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1SearchTextResponse::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1SearchTextResponse');
