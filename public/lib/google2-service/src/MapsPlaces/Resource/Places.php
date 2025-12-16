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

namespace Google\Service\MapsPlaces\Resource;

use Google\Service\MapsPlaces\GoogleMapsPlacesV1AutocompletePlacesRequest;
use Google\Service\MapsPlaces\GoogleMapsPlacesV1AutocompletePlacesResponse;
use Google\Service\MapsPlaces\GoogleMapsPlacesV1Place;
use Google\Service\MapsPlaces\GoogleMapsPlacesV1SearchNearbyRequest;
use Google\Service\MapsPlaces\GoogleMapsPlacesV1SearchNearbyResponse;
use Google\Service\MapsPlaces\GoogleMapsPlacesV1SearchTextRequest;
use Google\Service\MapsPlaces\GoogleMapsPlacesV1SearchTextResponse;

/**
 * The "places" collection of methods.
 * Typical usage is:
 *  <code>
 *   $placesService = new Google\Service\MapsPlaces(...);
 *   $places = $placesService->places;
 *  </code>
 */
class Places extends \Google\Service\Resource
{
  /**
   * Returns predictions for the given input. (places.autocomplete)
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleMapsPlacesV1AutocompletePlacesResponse
   * @throws \Google\Service\Exception
   */
  public function autocomplete(GoogleMapsPlacesV1AutocompletePlacesRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('autocomplete', [$params], GoogleMapsPlacesV1AutocompletePlacesResponse::class);
  }
  /**
   * Get the details of a place based on its resource name, which is a string in
   * the `places/{place_id}` format. (places.get)
   *
   * @param string $name Required. The resource name of a place, in the
   * `places/{place_id}` format.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode Optional. Place details will be displayed with
   * the preferred language if available. Current list of supported languages:
   * https://developers.google.com/maps/faq#languagesupport.
   * @opt_param string regionCode Optional. The Unicode country/region code (CLDR)
   * of the location where the request is coming from. This parameter is used to
   * display the place details, like region-specific place name, if available. The
   * parameter can affect results based on applicable law. For more information,
   * see https://www.unicode.org/cldr/charts/latest/supplemental/territory_languag
   * e_information.html. Note that 3-digit region codes are not currently
   * supported.
   * @opt_param string sessionToken Optional. A string which identifies an
   * Autocomplete session for billing purposes. Must be a URL and filename safe
   * base64 string with at most 36 ASCII characters in length. Otherwise an
   * INVALID_ARGUMENT error is returned. The session begins when the user starts
   * typing a query, and concludes when they select a place and a call to Place
   * Details or Address Validation is made. Each session can have multiple
   * queries, followed by one Place Details or Address Validation request. The
   * credentials used for each request within a session must belong to the same
   * Google Cloud Console project. Once a session has concluded, the token is no
   * longer valid; your app must generate a fresh token for each session. If the
   * `session_token` parameter is omitted, or if you reuse a session token, the
   * session is charged as if no session token was provided (each request is
   * billed separately). We recommend the following guidelines: * Use session
   * tokens for all Place Autocomplete calls. * Generate a fresh token for each
   * session. Using a version 4 UUID is recommended. * Ensure that the credentials
   * used for all Place Autocomplete, Place Details, and Address Validation
   * requests within a session belong to the same Cloud Console project. * Be sure
   * to pass a unique session token for each new session. Using the same token for
   * more than one session will result in each request being billed individually.
   * @return GoogleMapsPlacesV1Place
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleMapsPlacesV1Place::class);
  }
  /**
   * Search for places near locations. (places.searchNearby)
   *
   * @param GoogleMapsPlacesV1SearchNearbyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleMapsPlacesV1SearchNearbyResponse
   * @throws \Google\Service\Exception
   */
  public function searchNearby(GoogleMapsPlacesV1SearchNearbyRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('searchNearby', [$params], GoogleMapsPlacesV1SearchNearbyResponse::class);
  }
  /**
   * Text query based place search. (places.searchText)
   *
   * @param GoogleMapsPlacesV1SearchTextRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleMapsPlacesV1SearchTextResponse
   * @throws \Google\Service\Exception
   */
  public function searchText(GoogleMapsPlacesV1SearchTextRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('searchText', [$params], GoogleMapsPlacesV1SearchTextResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Places::class, 'Google_Service_MapsPlaces_Resource_Places');
