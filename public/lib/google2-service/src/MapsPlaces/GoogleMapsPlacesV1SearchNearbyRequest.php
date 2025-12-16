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

class GoogleMapsPlacesV1SearchNearbyRequest extends \Google\Collection
{
  /**
   * RankPreference value not set. Will use rank by POPULARITY by default.
   */
  public const RANK_PREFERENCE_RANK_PREFERENCE_UNSPECIFIED = 'RANK_PREFERENCE_UNSPECIFIED';
  /**
   * Ranks results by distance.
   */
  public const RANK_PREFERENCE_DISTANCE = 'DISTANCE';
  /**
   * Ranks results by popularity.
   */
  public const RANK_PREFERENCE_POPULARITY = 'POPULARITY';
  protected $collection_key = 'includedTypes';
  /**
   * Excluded primary Place type (e.g. "restaurant" or "gas_station") from
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. Up to 50 types from [Table
   * A](https://developers.google.com/maps/documentation/places/web-
   * service/place-types#table-a) may be specified. If there are any conflicting
   * primary types, i.e. a type appears in both included_primary_types and
   * excluded_primary_types, an INVALID_ARGUMENT error is returned. If a Place
   * type is specified with multiple type restrictions, only places that satisfy
   * all of the restrictions are returned. For example, if we have
   * {included_types = ["restaurant"], excluded_primary_types = ["restaurant"]},
   * the returned places provide "restaurant" related services but do not
   * operate primarily as "restaurants".
   *
   * @var string[]
   */
  public $excludedPrimaryTypes;
  /**
   * Excluded Place type (eg, "restaurant" or "gas_station") from
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. Up to 50 types from [Table
   * A](https://developers.google.com/maps/documentation/places/web-
   * service/place-types#table-a) may be specified. If the client provides both
   * included_types (e.g. restaurant) and excluded_types (e.g. cafe), then the
   * response should include places that are restaurant but not cafe. The
   * response includes places that match at least one of the included_types and
   * none of the excluded_types. If there are any conflicting types, i.e. a type
   * appears in both included_types and excluded_types, an INVALID_ARGUMENT
   * error is returned. If a Place type is specified with multiple type
   * restrictions, only places that satisfy all of the restrictions are
   * returned. For example, if we have {included_types = ["restaurant"],
   * excluded_primary_types = ["restaurant"]}, the returned places provide
   * "restaurant" related services but do not operate primarily as
   * "restaurants".
   *
   * @var string[]
   */
  public $excludedTypes;
  /**
   * Included primary Place type (e.g. "restaurant" or "gas_station") from
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. A place can only have a single primary type from the supported types
   * table associated with it. Up to 50 types from [Table
   * A](https://developers.google.com/maps/documentation/places/web-
   * service/place-types#table-a) may be specified. If there are any conflicting
   * primary types, i.e. a type appears in both included_primary_types and
   * excluded_primary_types, an INVALID_ARGUMENT error is returned. If a Place
   * type is specified with multiple type restrictions, only places that satisfy
   * all of the restrictions are returned. For example, if we have
   * {included_types = ["restaurant"], excluded_primary_types = ["restaurant"]},
   * the returned places provide "restaurant" related services but do not
   * operate primarily as "restaurants".
   *
   * @var string[]
   */
  public $includedPrimaryTypes;
  /**
   * Included Place type (eg, "restaurant" or "gas_station") from
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. Up to 50 types from [Table
   * A](https://developers.google.com/maps/documentation/places/web-
   * service/place-types#table-a) may be specified. If there are any conflicting
   * types, i.e. a type appears in both included_types and excluded_types, an
   * INVALID_ARGUMENT error is returned. If a Place type is specified with
   * multiple type restrictions, only places that satisfy all of the
   * restrictions are returned. For example, if we have {included_types =
   * ["restaurant"], excluded_primary_types = ["restaurant"]}, the returned
   * places provide "restaurant" related services but do not operate primarily
   * as "restaurants".
   *
   * @var string[]
   */
  public $includedTypes;
  /**
   * Place details will be displayed with the preferred language if available.
   * If the language code is unspecified or unrecognized, place details of any
   * language may be returned, with a preference for English if such details
   * exist. Current list of supported languages:
   * https://developers.google.com/maps/faq#languagesupport.
   *
   * @var string
   */
  public $languageCode;
  protected $locationRestrictionType = GoogleMapsPlacesV1SearchNearbyRequestLocationRestriction::class;
  protected $locationRestrictionDataType = '';
  /**
   * Maximum number of results to return. It must be between 1 and 20 (default),
   * inclusively. If the number is unset, it falls back to the upper limit. If
   * the number is set to negative or exceeds the upper limit, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var int
   */
  public $maxResultCount;
  /**
   * How results will be ranked in the response.
   *
   * @var string
   */
  public $rankPreference;
  /**
   * The Unicode country/region code (CLDR) of the location where the request is
   * coming from. This parameter is used to display the place details, like
   * region-specific place name, if available. The parameter can affect results
   * based on applicable law. For more information, see https://www.unicode.org/
   * cldr/charts/latest/supplemental/territory_language_information.html. Note
   * that 3-digit region codes are not currently supported.
   *
   * @var string
   */
  public $regionCode;
  protected $routingParametersType = GoogleMapsPlacesV1RoutingParameters::class;
  protected $routingParametersDataType = '';

  /**
   * Excluded primary Place type (e.g. "restaurant" or "gas_station") from
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. Up to 50 types from [Table
   * A](https://developers.google.com/maps/documentation/places/web-
   * service/place-types#table-a) may be specified. If there are any conflicting
   * primary types, i.e. a type appears in both included_primary_types and
   * excluded_primary_types, an INVALID_ARGUMENT error is returned. If a Place
   * type is specified with multiple type restrictions, only places that satisfy
   * all of the restrictions are returned. For example, if we have
   * {included_types = ["restaurant"], excluded_primary_types = ["restaurant"]},
   * the returned places provide "restaurant" related services but do not
   * operate primarily as "restaurants".
   *
   * @param string[] $excludedPrimaryTypes
   */
  public function setExcludedPrimaryTypes($excludedPrimaryTypes)
  {
    $this->excludedPrimaryTypes = $excludedPrimaryTypes;
  }
  /**
   * @return string[]
   */
  public function getExcludedPrimaryTypes()
  {
    return $this->excludedPrimaryTypes;
  }
  /**
   * Excluded Place type (eg, "restaurant" or "gas_station") from
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. Up to 50 types from [Table
   * A](https://developers.google.com/maps/documentation/places/web-
   * service/place-types#table-a) may be specified. If the client provides both
   * included_types (e.g. restaurant) and excluded_types (e.g. cafe), then the
   * response should include places that are restaurant but not cafe. The
   * response includes places that match at least one of the included_types and
   * none of the excluded_types. If there are any conflicting types, i.e. a type
   * appears in both included_types and excluded_types, an INVALID_ARGUMENT
   * error is returned. If a Place type is specified with multiple type
   * restrictions, only places that satisfy all of the restrictions are
   * returned. For example, if we have {included_types = ["restaurant"],
   * excluded_primary_types = ["restaurant"]}, the returned places provide
   * "restaurant" related services but do not operate primarily as
   * "restaurants".
   *
   * @param string[] $excludedTypes
   */
  public function setExcludedTypes($excludedTypes)
  {
    $this->excludedTypes = $excludedTypes;
  }
  /**
   * @return string[]
   */
  public function getExcludedTypes()
  {
    return $this->excludedTypes;
  }
  /**
   * Included primary Place type (e.g. "restaurant" or "gas_station") from
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. A place can only have a single primary type from the supported types
   * table associated with it. Up to 50 types from [Table
   * A](https://developers.google.com/maps/documentation/places/web-
   * service/place-types#table-a) may be specified. If there are any conflicting
   * primary types, i.e. a type appears in both included_primary_types and
   * excluded_primary_types, an INVALID_ARGUMENT error is returned. If a Place
   * type is specified with multiple type restrictions, only places that satisfy
   * all of the restrictions are returned. For example, if we have
   * {included_types = ["restaurant"], excluded_primary_types = ["restaurant"]},
   * the returned places provide "restaurant" related services but do not
   * operate primarily as "restaurants".
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
   * Included Place type (eg, "restaurant" or "gas_station") from
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. Up to 50 types from [Table
   * A](https://developers.google.com/maps/documentation/places/web-
   * service/place-types#table-a) may be specified. If there are any conflicting
   * types, i.e. a type appears in both included_types and excluded_types, an
   * INVALID_ARGUMENT error is returned. If a Place type is specified with
   * multiple type restrictions, only places that satisfy all of the
   * restrictions are returned. For example, if we have {included_types =
   * ["restaurant"], excluded_primary_types = ["restaurant"]}, the returned
   * places provide "restaurant" related services but do not operate primarily
   * as "restaurants".
   *
   * @param string[] $includedTypes
   */
  public function setIncludedTypes($includedTypes)
  {
    $this->includedTypes = $includedTypes;
  }
  /**
   * @return string[]
   */
  public function getIncludedTypes()
  {
    return $this->includedTypes;
  }
  /**
   * Place details will be displayed with the preferred language if available.
   * If the language code is unspecified or unrecognized, place details of any
   * language may be returned, with a preference for English if such details
   * exist. Current list of supported languages:
   * https://developers.google.com/maps/faq#languagesupport.
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
   * Required. The region to search.
   *
   * @param GoogleMapsPlacesV1SearchNearbyRequestLocationRestriction $locationRestriction
   */
  public function setLocationRestriction(GoogleMapsPlacesV1SearchNearbyRequestLocationRestriction $locationRestriction)
  {
    $this->locationRestriction = $locationRestriction;
  }
  /**
   * @return GoogleMapsPlacesV1SearchNearbyRequestLocationRestriction
   */
  public function getLocationRestriction()
  {
    return $this->locationRestriction;
  }
  /**
   * Maximum number of results to return. It must be between 1 and 20 (default),
   * inclusively. If the number is unset, it falls back to the upper limit. If
   * the number is set to negative or exceeds the upper limit, an
   * INVALID_ARGUMENT error is returned.
   *
   * @param int $maxResultCount
   */
  public function setMaxResultCount($maxResultCount)
  {
    $this->maxResultCount = $maxResultCount;
  }
  /**
   * @return int
   */
  public function getMaxResultCount()
  {
    return $this->maxResultCount;
  }
  /**
   * How results will be ranked in the response.
   *
   * Accepted values: RANK_PREFERENCE_UNSPECIFIED, DISTANCE, POPULARITY
   *
   * @param self::RANK_PREFERENCE_* $rankPreference
   */
  public function setRankPreference($rankPreference)
  {
    $this->rankPreference = $rankPreference;
  }
  /**
   * @return self::RANK_PREFERENCE_*
   */
  public function getRankPreference()
  {
    return $this->rankPreference;
  }
  /**
   * The Unicode country/region code (CLDR) of the location where the request is
   * coming from. This parameter is used to display the place details, like
   * region-specific place name, if available. The parameter can affect results
   * based on applicable law. For more information, see https://www.unicode.org/
   * cldr/charts/latest/supplemental/territory_language_information.html. Note
   * that 3-digit region codes are not currently supported.
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
   * Optional. Parameters that affect the routing to the search results.
   *
   * @param GoogleMapsPlacesV1RoutingParameters $routingParameters
   */
  public function setRoutingParameters(GoogleMapsPlacesV1RoutingParameters $routingParameters)
  {
    $this->routingParameters = $routingParameters;
  }
  /**
   * @return GoogleMapsPlacesV1RoutingParameters
   */
  public function getRoutingParameters()
  {
    return $this->routingParameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1SearchNearbyRequest::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1SearchNearbyRequest');
