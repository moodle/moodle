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

namespace Google\Service\MyBusinessBusinessInformation;

class ServiceAreaBusiness extends \Google\Model
{
  /**
   * Output only. Not specified.
   */
  public const BUSINESS_TYPE_BUSINESS_TYPE_UNSPECIFIED = 'BUSINESS_TYPE_UNSPECIFIED';
  /**
   * Offers service only in the surrounding area (not at the business address).
   * If a business is being updated from a CUSTOMER_AND_BUSINESS_LOCATION to a
   * CUSTOMER_LOCATION_ONLY, the location update must include field mask
   * `storefront_address` and set the field to empty.
   */
  public const BUSINESS_TYPE_CUSTOMER_LOCATION_ONLY = 'CUSTOMER_LOCATION_ONLY';
  /**
   * Offers service at the business address and the surrounding area.
   */
  public const BUSINESS_TYPE_CUSTOMER_AND_BUSINESS_LOCATION = 'CUSTOMER_AND_BUSINESS_LOCATION';
  /**
   * Required. Indicates the type of the service area business.
   *
   * @var string
   */
  public $businessType;
  protected $placesType = Places::class;
  protected $placesDataType = '';
  /**
   * Immutable. CLDR region code of the country/region that this service area
   * business is based in. See http://cldr.unicode.org/ and http://www.unicode.o
   * rg/cldr/charts/30/supplemental/territory_information.html for details.
   * Example: "CH" for Switzerland. This field is required for
   * CUSTOMER_LOCATION_ONLY businesses, and is ignored otherwise. The region
   * specified here can be different from regions for the areas that this
   * business serves (e.g. service area businesses that provide services in
   * regions other than the one that they are based in). If this location
   * requires verification after creation, the address provided for verification
   * purposes *must* be located within this region, and the business owner or
   * their authorized representative *must* be able to receive postal mail at
   * the provided verification address.
   *
   * @var string
   */
  public $regionCode;

  /**
   * Required. Indicates the type of the service area business.
   *
   * Accepted values: BUSINESS_TYPE_UNSPECIFIED, CUSTOMER_LOCATION_ONLY,
   * CUSTOMER_AND_BUSINESS_LOCATION
   *
   * @param self::BUSINESS_TYPE_* $businessType
   */
  public function setBusinessType($businessType)
  {
    $this->businessType = $businessType;
  }
  /**
   * @return self::BUSINESS_TYPE_*
   */
  public function getBusinessType()
  {
    return $this->businessType;
  }
  /**
   * The area that this business serves defined through a set of places.
   *
   * @param Places $places
   */
  public function setPlaces(Places $places)
  {
    $this->places = $places;
  }
  /**
   * @return Places
   */
  public function getPlaces()
  {
    return $this->places;
  }
  /**
   * Immutable. CLDR region code of the country/region that this service area
   * business is based in. See http://cldr.unicode.org/ and http://www.unicode.o
   * rg/cldr/charts/30/supplemental/territory_information.html for details.
   * Example: "CH" for Switzerland. This field is required for
   * CUSTOMER_LOCATION_ONLY businesses, and is ignored otherwise. The region
   * specified here can be different from regions for the areas that this
   * business serves (e.g. service area businesses that provide services in
   * regions other than the one that they are based in). If this location
   * requires verification after creation, the address provided for verification
   * purposes *must* be located within this region, and the business owner or
   * their authorized representative *must* be able to receive postal mail at
   * the provided verification address.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceAreaBusiness::class, 'Google_Service_MyBusinessBusinessInformation_ServiceAreaBusiness');
