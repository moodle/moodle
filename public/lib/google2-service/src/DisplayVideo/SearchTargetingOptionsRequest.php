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

namespace Google\Service\DisplayVideo;

class SearchTargetingOptionsRequest extends \Google\Model
{
  /**
   * Required. The Advertiser this request is being made in the context of.
   *
   * @var string
   */
  public $advertiserId;
  protected $businessChainSearchTermsType = BusinessChainSearchTerms::class;
  protected $businessChainSearchTermsDataType = '';
  protected $geoRegionSearchTermsType = GeoRegionSearchTerms::class;
  protected $geoRegionSearchTermsDataType = '';
  /**
   * Requested page size. Must be between `1` and `200`. If unspecified will
   * default to `100`. Returns error code `INVALID_ARGUMENT` if an invalid value
   * is specified.
   *
   * @var int
   */
  public $pageSize;
  /**
   * A token identifying a page of results the server should return. Typically,
   * this is the value of next_page_token returned from the previous call to
   * `SearchTargetingOptions` method. If not specified, the first page of
   * results will be returned.
   *
   * @var string
   */
  public $pageToken;
  protected $poiSearchTermsType = PoiSearchTerms::class;
  protected $poiSearchTermsDataType = '';

  /**
   * Required. The Advertiser this request is being made in the context of.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Search terms for Business Chain targeting options. Can only be used when
   * targeting_type is `TARGETING_TYPE_BUSINESS_CHAIN`.
   *
   * @param BusinessChainSearchTerms $businessChainSearchTerms
   */
  public function setBusinessChainSearchTerms(BusinessChainSearchTerms $businessChainSearchTerms)
  {
    $this->businessChainSearchTerms = $businessChainSearchTerms;
  }
  /**
   * @return BusinessChainSearchTerms
   */
  public function getBusinessChainSearchTerms()
  {
    return $this->businessChainSearchTerms;
  }
  /**
   * Search terms for geo region targeting options. Can only be used when
   * targeting_type is `TARGETING_TYPE_GEO_REGION`.
   *
   * @param GeoRegionSearchTerms $geoRegionSearchTerms
   */
  public function setGeoRegionSearchTerms(GeoRegionSearchTerms $geoRegionSearchTerms)
  {
    $this->geoRegionSearchTerms = $geoRegionSearchTerms;
  }
  /**
   * @return GeoRegionSearchTerms
   */
  public function getGeoRegionSearchTerms()
  {
    return $this->geoRegionSearchTerms;
  }
  /**
   * Requested page size. Must be between `1` and `200`. If unspecified will
   * default to `100`. Returns error code `INVALID_ARGUMENT` if an invalid value
   * is specified.
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
   * A token identifying a page of results the server should return. Typically,
   * this is the value of next_page_token returned from the previous call to
   * `SearchTargetingOptions` method. If not specified, the first page of
   * results will be returned.
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
   * Search terms for POI targeting options. Can only be used when
   * targeting_type is `TARGETING_TYPE_POI`.
   *
   * @param PoiSearchTerms $poiSearchTerms
   */
  public function setPoiSearchTerms(PoiSearchTerms $poiSearchTerms)
  {
    $this->poiSearchTerms = $poiSearchTerms;
  }
  /**
   * @return PoiSearchTerms
   */
  public function getPoiSearchTerms()
  {
    return $this->poiSearchTerms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SearchTargetingOptionsRequest::class, 'Google_Service_DisplayVideo_SearchTargetingOptionsRequest');
