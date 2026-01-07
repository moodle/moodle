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

class GoogleMapsPlacesV1AutocompletePlacesRequest extends \Google\Collection
{
  protected $collection_key = 'includedRegionCodes';
  /**
   * Optional. Include pure service area businesses if the field is set to true.
   * Pure service area business is a business that visits or delivers to
   * customers directly but does not serve customers at their business address.
   * For example, businesses like cleaning services or plumbers. Those
   * businesses do not have a physical address or location on Google Maps.
   * Places will not return fields including `location`, `plus_code`, and other
   * location related fields for these businesses.
   *
   * @var bool
   */
  public $includePureServiceAreaBusinesses;
  /**
   * Optional. If true, the response will include both Place and query
   * predictions. Otherwise the response will only return Place predictions.
   *
   * @var bool
   */
  public $includeQueryPredictions;
  /**
   * Optional. Included primary Place type (for example, "restaurant" or
   * "gas_station") in Place Types
   * (https://developers.google.com/maps/documentation/places/web-service/place-
   * types), or only `(regions)`, or only `(cities)`. A Place is only returned
   * if its primary type is included in this list. Up to 5 values can be
   * specified. If no types are specified, all Place types are returned.
   *
   * @var string[]
   */
  public $includedPrimaryTypes;
  /**
   * Optional. Only include results in the specified regions, specified as up to
   * 15 CLDR two-character region codes. An empty set will not restrict the
   * results. If both `location_restriction` and `included_region_codes` are
   * set, the results will be located in the area of intersection.
   *
   * @var string[]
   */
  public $includedRegionCodes;
  /**
   * Required. The text string on which to search.
   *
   * @var string
   */
  public $input;
  /**
   * Optional. A zero-based Unicode character offset of `input` indicating the
   * cursor position in `input`. The cursor position may influence what
   * predictions are returned. If empty, defaults to the length of `input`.
   *
   * @var int
   */
  public $inputOffset;
  /**
   * Optional. The language in which to return results. Defaults to en-US. The
   * results may be in mixed languages if the language used in `input` is
   * different from `language_code` or if the returned Place does not have a
   * translation from the local language to `language_code`.
   *
   * @var string
   */
  public $languageCode;
  protected $locationBiasType = GoogleMapsPlacesV1AutocompletePlacesRequestLocationBias::class;
  protected $locationBiasDataType = '';
  protected $locationRestrictionType = GoogleMapsPlacesV1AutocompletePlacesRequestLocationRestriction::class;
  protected $locationRestrictionDataType = '';
  protected $originType = GoogleTypeLatLng::class;
  protected $originDataType = '';
  /**
   * Optional. The region code, specified as a CLDR two-character region code.
   * This affects address formatting, result ranking, and may influence what
   * results are returned. This does not restrict results to the specified
   * region. To restrict results to a region, use `region_code_restriction`.
   *
   * @var string
   */
  public $regionCode;
  /**
   * Optional. A string which identifies an Autocomplete session for billing
   * purposes. Must be a URL and filename safe base64 string with at most 36
   * ASCII characters in length. Otherwise an INVALID_ARGUMENT error is
   * returned. The session begins when the user starts typing a query, and
   * concludes when they select a place and a call to Place Details or Address
   * Validation is made. Each session can have multiple queries, followed by one
   * Place Details or Address Validation request. The credentials used for each
   * request within a session must belong to the same Google Cloud Console
   * project. Once a session has concluded, the token is no longer valid; your
   * app must generate a fresh token for each session. If the `session_token`
   * parameter is omitted, or if you reuse a session token, the session is
   * charged as if no session token was provided (each request is billed
   * separately). We recommend the following guidelines: * Use session tokens
   * for all Place Autocomplete calls. * Generate a fresh token for each
   * session. Using a version 4 UUID is recommended. * Ensure that the
   * credentials used for all Place Autocomplete, Place Details, and Address
   * Validation requests within a session belong to the same Cloud Console
   * project. * Be sure to pass a unique session token for each new session.
   * Using the same token for more than one session will result in each request
   * being billed individually.
   *
   * @var string
   */
  public $sessionToken;

  /**
   * Optional. Include pure service area businesses if the field is set to true.
   * Pure service area business is a business that visits or delivers to
   * customers directly but does not serve customers at their business address.
   * For example, businesses like cleaning services or plumbers. Those
   * businesses do not have a physical address or location on Google Maps.
   * Places will not return fields including `location`, `plus_code`, and other
   * location related fields for these businesses.
   *
   * @param bool $includePureServiceAreaBusinesses
   */
  public function setIncludePureServiceAreaBusinesses($includePureServiceAreaBusinesses)
  {
    $this->includePureServiceAreaBusinesses = $includePureServiceAreaBusinesses;
  }
  /**
   * @return bool
   */
  public function getIncludePureServiceAreaBusinesses()
  {
    return $this->includePureServiceAreaBusinesses;
  }
  /**
   * Optional. If true, the response will include both Place and query
   * predictions. Otherwise the response will only return Place predictions.
   *
   * @param bool $includeQueryPredictions
   */
  public function setIncludeQueryPredictions($includeQueryPredictions)
  {
    $this->includeQueryPredictions = $includeQueryPredictions;
  }
  /**
   * @return bool
   */
  public function getIncludeQueryPredictions()
  {
    return $this->includeQueryPredictions;
  }
  /**
   * Optional. Included primary Place type (for example, "restaurant" or
   * "gas_station") in Place Types
   * (https://developers.google.com/maps/documentation/places/web-service/place-
   * types), or only `(regions)`, or only `(cities)`. A Place is only returned
   * if its primary type is included in this list. Up to 5 values can be
   * specified. If no types are specified, all Place types are returned.
   *
   * @param string[] $includedPrimaryTypes
   */
  public function setIncludedPrimaryTypes($includedPrimaryTypes)
  {
    $this->includedPrimaryTypes = $includedPrimaryTypes;
  }
  /**
   * @return string[]
   */
  public function getIncludedPrimaryTypes()
  {
    return $this->includedPrimaryTypes;
  }
  /**
   * Optional. Only include results in the specified regions, specified as up to
   * 15 CLDR two-character region codes. An empty set will not restrict the
   * results. If both `location_restriction` and `included_region_codes` are
   * set, the results will be located in the area of intersection.
   *
   * @param string[] $includedRegionCodes
   */
  public function setIncludedRegionCodes($includedRegionCodes)
  {
    $this->includedRegionCodes = $includedRegionCodes;
  }
  /**
   * @return string[]
   */
  public function getIncludedRegionCodes()
  {
    return $this->includedRegionCodes;
  }
  /**
   * Required. The text string on which to search.
   *
   * @param string $input
   */
  public function setInput($input)
  {
    $this->input = $input;
  }
  /**
   * @return string
   */
  public function getInput()
  {
    return $this->input;
  }
  /**
   * Optional. A zero-based Unicode character offset of `input` indicating the
   * cursor position in `input`. The cursor position may influence what
   * predictions are returned. If empty, defaults to the length of `input`.
   *
   * @param int $inputOffset
   */
  public function setInputOffset($inputOffset)
  {
    $this->inputOffset = $inputOffset;
  }
  /**
   * @return int
   */
  public function getInputOffset()
  {
    return $this->inputOffset;
  }
  /**
   * Optional. The language in which to return results. Defaults to en-US. The
   * results may be in mixed languages if the language used in `input` is
   * different from `language_code` or if the returned Place does not have a
   * translation from the local language to `language_code`.
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
   * Optional. Bias results to a specified location. At most one of
   * `location_bias` or `location_restriction` should be set. If neither are
   * set, the results will be biased by IP address, meaning the IP address will
   * be mapped to an imprecise location and used as a biasing signal.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesRequestLocationBias $locationBias
   */
  public function setLocationBias(GoogleMapsPlacesV1AutocompletePlacesRequestLocationBias $locationBias)
  {
    $this->locationBias = $locationBias;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesRequestLocationBias
   */
  public function getLocationBias()
  {
    return $this->locationBias;
  }
  /**
   * Optional. Restrict results to a specified location. At most one of
   * `location_bias` or `location_restriction` should be set. If neither are
   * set, the results will be biased by IP address, meaning the IP address will
   * be mapped to an imprecise location and used as a biasing signal.
   *
   * @param GoogleMapsPlacesV1AutocompletePlacesRequestLocationRestriction $locationRestriction
   */
  public function setLocationRestriction(GoogleMapsPlacesV1AutocompletePlacesRequestLocationRestriction $locationRestriction)
  {
    $this->locationRestriction = $locationRestriction;
  }
  /**
   * @return GoogleMapsPlacesV1AutocompletePlacesRequestLocationRestriction
   */
  public function getLocationRestriction()
  {
    return $this->locationRestriction;
  }
  /**
   * Optional. The origin point from which to calculate geodesic distance to the
   * destination (returned as `distance_meters`). If this value is omitted,
   * geodesic distance will not be returned.
   *
   * @param GoogleTypeLatLng $origin
   */
  public function setOrigin(GoogleTypeLatLng $origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return GoogleTypeLatLng
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * Optional. The region code, specified as a CLDR two-character region code.
   * This affects address formatting, result ranking, and may influence what
   * results are returned. This does not restrict results to the specified
   * region. To restrict results to a region, use `region_code_restriction`.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * Optional. A string which identifies an Autocomplete session for billing
   * purposes. Must be a URL and filename safe base64 string with at most 36
   * ASCII characters in length. Otherwise an INVALID_ARGUMENT error is
   * returned. The session begins when the user starts typing a query, and
   * concludes when they select a place and a call to Place Details or Address
   * Validation is made. Each session can have multiple queries, followed by one
   * Place Details or Address Validation request. The credentials used for each
   * request within a session must belong to the same Google Cloud Console
   * project. Once a session has concluded, the token is no longer valid; your
   * app must generate a fresh token for each session. If the `session_token`
   * parameter is omitted, or if you reuse a session token, the session is
   * charged as if no session token was provided (each request is billed
   * separately). We recommend the following guidelines: * Use session tokens
   * for all Place Autocomplete calls. * Generate a fresh token for each
   * session. Using a version 4 UUID is recommended. * Ensure that the
   * credentials used for all Place Autocomplete, Place Details, and Address
   * Validation requests within a session belong to the same Cloud Console
   * project. * Be sure to pass a unique session token for each new session.
   * Using the same token for more than one session will result in each request
   * being billed individually.
   *
   * @param string $sessionToken
   */
  public function setSessionToken($sessionToken)
  {
    $this->sessionToken = $sessionToken;
  }
  /**
   * @return string
   */
  public function getSessionToken()
  {
    return $this->sessionToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1AutocompletePlacesRequest::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1AutocompletePlacesRequest');
