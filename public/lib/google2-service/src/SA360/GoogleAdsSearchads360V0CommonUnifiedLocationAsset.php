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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonUnifiedLocationAsset extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const LOCATION_OWNERSHIP_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const LOCATION_OWNERSHIP_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Business Owner of location(legacy location extension - LE).
   */
  public const LOCATION_OWNERSHIP_TYPE_BUSINESS_OWNER = 'BUSINESS_OWNER';
  /**
   * Affiliate location(Third party location extension - ALE).
   */
  public const LOCATION_OWNERSHIP_TYPE_AFFILIATE = 'AFFILIATE';
  protected $collection_key = 'businessProfileLocations';
  protected $businessProfileLocationsType = GoogleAdsSearchads360V0CommonBusinessProfileLocation::class;
  protected $businessProfileLocationsDataType = 'array';
  /**
   * The type of location ownership. If the type is BUSINESS_OWNER, it will be
   * served as a location extension. If the type is AFFILIATE, it will be served
   * as an affiliate location.
   *
   * @var string
   */
  public $locationOwnershipType;
  /**
   * Place IDs uniquely identify a place in the Google Places database and on
   * Google Maps. This field is unique for a given customer ID and asset type.
   * See https://developers.google.com/places/web-service/place-id to learn more
   * about Place ID.
   *
   * @var string
   */
  public $placeId;

  /**
   * The list of business locations for the customer. This will only be returned
   * if the Location Asset is syncing from the Business Profile account. It is
   * possible to have multiple Business Profile listings under the same account
   * that point to the same Place ID.
   *
   * @param GoogleAdsSearchads360V0CommonBusinessProfileLocation[] $businessProfileLocations
   */
  public function setBusinessProfileLocations($businessProfileLocations)
  {
    $this->businessProfileLocations = $businessProfileLocations;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonBusinessProfileLocation[]
   */
  public function getBusinessProfileLocations()
  {
    return $this->businessProfileLocations;
  }
  /**
   * The type of location ownership. If the type is BUSINESS_OWNER, it will be
   * served as a location extension. If the type is AFFILIATE, it will be served
   * as an affiliate location.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, BUSINESS_OWNER, AFFILIATE
   *
   * @param self::LOCATION_OWNERSHIP_TYPE_* $locationOwnershipType
   */
  public function setLocationOwnershipType($locationOwnershipType)
  {
    $this->locationOwnershipType = $locationOwnershipType;
  }
  /**
   * @return self::LOCATION_OWNERSHIP_TYPE_*
   */
  public function getLocationOwnershipType()
  {
    return $this->locationOwnershipType;
  }
  /**
   * Place IDs uniquely identify a place in the Google Places database and on
   * Google Maps. This field is unique for a given customer ID and asset type.
   * See https://developers.google.com/places/web-service/place-id to learn more
   * about Place ID.
   *
   * @param string $placeId
   */
  public function setPlaceId($placeId)
  {
    $this->placeId = $placeId;
  }
  /**
   * @return string
   */
  public function getPlaceId()
  {
    return $this->placeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonUnifiedLocationAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonUnifiedLocationAsset');
