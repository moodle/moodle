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

class GoogleMapsPlacesV1SearchTextRequest extends \Google\Collection
{
  /**
   * For a categorical query such as "Restaurants in New York City", RELEVANCE
   * is the default. For non-categorical queries such as "Mountain View, CA" we
   * recommend that you leave rankPreference unset.
   */
  public const RANK_PREFERENCE_RANK_PREFERENCE_UNSPECIFIED = 'RANK_PREFERENCE_UNSPECIFIED';
  /**
   * Ranks results by distance.
   */
  public const RANK_PREFERENCE_DISTANCE = 'DISTANCE';
  /**
   * Ranks results by relevance. Sort order determined by normal ranking stack.
   */
  public const RANK_PREFERENCE_RELEVANCE = 'RELEVANCE';
  protected $collection_key = 'priceLevels';
  protected $evOptionsType = GoogleMapsPlacesV1SearchTextRequestEVOptions::class;
  protected $evOptionsDataType = '';
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
   * The requested place type. Full list of types supported:
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. Only support one included type.
   *
   * @var string
   */
  public $includedType;
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
  protected $locationBiasType = GoogleMapsPlacesV1SearchTextRequestLocationBias::class;
  protected $locationBiasDataType = '';
  protected $locationRestrictionType = GoogleMapsPlacesV1SearchTextRequestLocationRestriction::class;
  protected $locationRestrictionDataType = '';
  /**
   * Deprecated: Use `page_size` instead. The maximum number of results per page
   * that can be returned. If the number of available results is larger than
   * `max_result_count`, a `next_page_token` is returned which can be passed to
   * `page_token` to get the next page of results in subsequent requests. If 0
   * or no value is provided, a default of 20 is used. The maximum value is 20;
   * values above 20 will be coerced to 20. Negative values will return an
   * INVALID_ARGUMENT error. If both `max_result_count` and `page_size` are
   * specified, `max_result_count` will be ignored.
   *
   * @deprecated
   * @var int
   */
  public $maxResultCount;
  /**
   * Filter out results whose average user rating is strictly less than this
   * limit. A valid value must be a float between 0 and 5 (inclusively) at a 0.5
   * cadence i.e. [0, 0.5, 1.0, ... , 5.0] inclusively. The input rating will
   * round up to the nearest 0.5(ceiling). For instance, a rating of 0.6 will
   * eliminate all results with a less than 1.0 rating.
   *
   * @var 
   */
  public $minRating;
  /**
   * Used to restrict the search to places that are currently open. The default
   * is false.
   *
   * @var bool
   */
  public $openNow;
  /**
   * Optional. The maximum number of results per page that can be returned. If
   * the number of available results is larger than `page_size`, a
   * `next_page_token` is returned which can be passed to `page_token` to get
   * the next page of results in subsequent requests. If 0 or no value is
   * provided, a default of 20 is used. The maximum value is 20; values above 20
   * will be set to 20. Negative values will return an INVALID_ARGUMENT error.
   * If both `max_result_count` and `page_size` are specified,
   * `max_result_count` will be ignored.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token, received from a previous TextSearch call. Provide
   * this to retrieve the subsequent page. When paginating, all parameters other
   * than `page_token`, `page_size`, and `max_result_count` provided to
   * TextSearch must match the initial call that provided the page token.
   * Otherwise an INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Used to restrict the search to places that are marked as certain price
   * levels. Users can choose any combinations of price levels. Default to
   * select all price levels.
   *
   * @var string[]
   */
  public $priceLevels;
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
  protected $searchAlongRouteParametersType = GoogleMapsPlacesV1SearchTextRequestSearchAlongRouteParameters::class;
  protected $searchAlongRouteParametersDataType = '';
  /**
   * Used to set strict type filtering for included_type. If set to true, only
   * results of the same type will be returned. Default to false.
   *
   * @var bool
   */
  public $strictTypeFiltering;
  /**
   * Required. The text query for textual search.
   *
   * @var string
   */
  public $textQuery;

  /**
   * Optional. Set the searchable EV options of a place search request.
   *
   * @param GoogleMapsPlacesV1SearchTextRequestEVOptions $evOptions
   */
  public function setEvOptions(GoogleMapsPlacesV1SearchTextRequestEVOptions $evOptions)
  {
    $this->evOptions = $evOptions;
  }
  /**
   * @return GoogleMapsPlacesV1SearchTextRequestEVOptions
   */
  public function getEvOptions()
  {
    return $this->evOptions;
  }
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
   * The requested place type. Full list of types supported:
   * https://developers.google.com/maps/documentation/places/web-service/place-
   * types. Only support one included type.
   *
   * @param string $includedType
   */
  public function setIncludedType($includedType)
  {
    $this->includedType = $includedType;
  }
  /**
   * @return string
   */
  public function getIncludedType()
  {
    return $this->includedType;
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
   * The region to search. This location serves as a bias which means results
   * around given location might be returned. Cannot be set along with
   * location_restriction.
   *
   * @param GoogleMapsPlacesV1SearchTextRequestLocationBias $locationBias
   */
  public function setLocationBias(GoogleMapsPlacesV1SearchTextRequestLocationBias $locationBias)
  {
    $this->locationBias = $locationBias;
  }
  /**
   * @return GoogleMapsPlacesV1SearchTextRequestLocationBias
   */
  public function getLocationBias()
  {
    return $this->locationBias;
  }
  /**
   * The region to search. This location serves as a restriction which means
   * results outside given location will not be returned. Cannot be set along
   * with location_bias.
   *
   * @param GoogleMapsPlacesV1SearchTextRequestLocationRestriction $locationRestriction
   */
  public function setLocationRestriction(GoogleMapsPlacesV1SearchTextRequestLocationRestriction $locationRestriction)
  {
    $this->locationRestriction = $locationRestriction;
  }
  /**
   * @return GoogleMapsPlacesV1SearchTextRequestLocationRestriction
   */
  public function getLocationRestriction()
  {
    return $this->locationRestriction;
  }
  /**
   * Deprecated: Use `page_size` instead. The maximum number of results per page
   * that can be returned. If the number of available results is larger than
   * `max_result_count`, a `next_page_token` is returned which can be passed to
   * `page_token` to get the next page of results in subsequent requests. If 0
   * or no value is provided, a default of 20 is used. The maximum value is 20;
   * values above 20 will be coerced to 20. Negative values will return an
   * INVALID_ARGUMENT error. If both `max_result_count` and `page_size` are
   * specified, `max_result_count` will be ignored.
   *
   * @deprecated
   * @param int $maxResultCount
   */
  public function setMaxResultCount($maxResultCount)
  {
    $this->maxResultCount = $maxResultCount;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getMaxResultCount()
  {
    return $this->maxResultCount;
  }
  public function setMinRating($minRating)
  {
    $this->minRating = $minRating;
  }
  public function getMinRating()
  {
    return $this->minRating;
  }
  /**
   * Used to restrict the search to places that are currently open. The default
   * is false.
   *
   * @param bool $openNow
   */
  public function setOpenNow($openNow)
  {
    $this->openNow = $openNow;
  }
  /**
   * @return bool
   */
  public function getOpenNow()
  {
    return $this->openNow;
  }
  /**
   * Optional. The maximum number of results per page that can be returned. If
   * the number of available results is larger than `page_size`, a
   * `next_page_token` is returned which can be passed to `page_token` to get
   * the next page of results in subsequent requests. If 0 or no value is
   * provided, a default of 20 is used. The maximum value is 20; values above 20
   * will be set to 20. Negative values will return an INVALID_ARGUMENT error.
   * If both `max_result_count` and `page_size` are specified,
   * `max_result_count` will be ignored.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Optional. A page token, received from a previous TextSearch call. Provide
   * this to retrieve the subsequent page. When paginating, all parameters other
   * than `page_token`, `page_size`, and `max_result_count` provided to
   * TextSearch must match the initial call that provided the page token.
   * Otherwise an INVALID_ARGUMENT error is returned.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Used to restrict the search to places that are marked as certain price
   * levels. Users can choose any combinations of price levels. Default to
   * select all price levels.
   *
   * @param string[] $priceLevels
   */
  public function setPriceLevels($priceLevels)
  {
    $this->priceLevels = $priceLevels;
  }
  /**
   * @return string[]
   */
  public function getPriceLevels()
  {
    return $this->priceLevels;
  }
  /**
   * How results will be ranked in the response.
   *
   * Accepted values: RANK_PREFERENCE_UNSPECIFIED, DISTANCE, RELEVANCE
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
   * Optional. Additional parameters for routing to results.
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
  /**
   * Optional. Additional parameters proto for searching along a route.
   *
   * @param GoogleMapsPlacesV1SearchTextRequestSearchAlongRouteParameters $searchAlongRouteParameters
   */
  public function setSearchAlongRouteParameters(GoogleMapsPlacesV1SearchTextRequestSearchAlongRouteParameters $searchAlongRouteParameters)
  {
    $this->searchAlongRouteParameters = $searchAlongRouteParameters;
  }
  /**
   * @return GoogleMapsPlacesV1SearchTextRequestSearchAlongRouteParameters
   */
  public function getSearchAlongRouteParameters()
  {
    return $this->searchAlongRouteParameters;
  }
  /**
   * Used to set strict type filtering for included_type. If set to true, only
   * results of the same type will be returned. Default to false.
   *
   * @param bool $strictTypeFiltering
   */
  public function setStrictTypeFiltering($strictTypeFiltering)
  {
    $this->strictTypeFiltering = $strictTypeFiltering;
  }
  /**
   * @return bool
   */
  public function getStrictTypeFiltering()
  {
    return $this->strictTypeFiltering;
  }
  /**
   * Required. The text query for textual search.
   *
   * @param string $textQuery
   */
  public function setTextQuery($textQuery)
  {
    $this->textQuery = $textQuery;
  }
  /**
   * @return string
   */
  public function getTextQuery()
  {
    return $this->textQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleMapsPlacesV1SearchTextRequest::class, 'Google_Service_MapsPlaces_GoogleMapsPlacesV1SearchTextRequest');
