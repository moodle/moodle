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

namespace Google\Service\CloudTalentSolution;

class Location extends \Google\Model
{
  /**
   * Default value if the type isn't specified.
   */
  public const LOCATION_TYPE_LOCATION_TYPE_UNSPECIFIED = 'LOCATION_TYPE_UNSPECIFIED';
  /**
   * A country level location.
   */
  public const LOCATION_TYPE_COUNTRY = 'COUNTRY';
  /**
   * A state or equivalent level location.
   */
  public const LOCATION_TYPE_ADMINISTRATIVE_AREA = 'ADMINISTRATIVE_AREA';
  /**
   * A county or equivalent level location.
   */
  public const LOCATION_TYPE_SUB_ADMINISTRATIVE_AREA = 'SUB_ADMINISTRATIVE_AREA';
  /**
   * A city or equivalent level location.
   */
  public const LOCATION_TYPE_LOCALITY = 'LOCALITY';
  /**
   * A postal code level location.
   */
  public const LOCATION_TYPE_POSTAL_CODE = 'POSTAL_CODE';
  /**
   * A sublocality is a subdivision of a locality, for example a city borough,
   * ward, or arrondissement. Sublocalities are usually recognized by a local
   * political authority. For example, Manhattan and Brooklyn are recognized as
   * boroughs by the City of New York, and are therefore modeled as
   * sublocalities.
   */
  public const LOCATION_TYPE_SUB_LOCALITY = 'SUB_LOCALITY';
  /**
   * A district or equivalent level location.
   */
  public const LOCATION_TYPE_SUB_LOCALITY_1 = 'SUB_LOCALITY_1';
  /**
   * A smaller district or equivalent level display.
   */
  public const LOCATION_TYPE_SUB_LOCALITY_2 = 'SUB_LOCALITY_2';
  /**
   * A neighborhood level location.
   */
  public const LOCATION_TYPE_NEIGHBORHOOD = 'NEIGHBORHOOD';
  /**
   * A street address level location.
   */
  public const LOCATION_TYPE_STREET_ADDRESS = 'STREET_ADDRESS';
  protected $latLngType = LatLng::class;
  protected $latLngDataType = '';
  /**
   * The type of a location, which corresponds to the address lines field of
   * google.type.PostalAddress. For example, "Downtown, Atlanta, GA, USA" has a
   * type of LocationType.NEIGHBORHOOD, and "Kansas City, KS, USA" has a type of
   * LocationType.LOCALITY.
   *
   * @var string
   */
  public $locationType;
  protected $postalAddressType = PostalAddress::class;
  protected $postalAddressDataType = '';
  /**
   * Radius in miles of the job location. This value is derived from the
   * location bounding box in which a circle with the specified radius centered
   * from google.type.LatLng covers the area associated with the job location.
   * For example, currently, "Mountain View, CA, USA" has a radius of 6.17
   * miles.
   *
   * @var 
   */
  public $radiusMiles;

  /**
   * An object representing a latitude/longitude pair.
   *
   * @param LatLng $latLng
   */
  public function setLatLng(LatLng $latLng)
  {
    $this->latLng = $latLng;
  }
  /**
   * @return LatLng
   */
  public function getLatLng()
  {
    return $this->latLng;
  }
  /**
   * The type of a location, which corresponds to the address lines field of
   * google.type.PostalAddress. For example, "Downtown, Atlanta, GA, USA" has a
   * type of LocationType.NEIGHBORHOOD, and "Kansas City, KS, USA" has a type of
   * LocationType.LOCALITY.
   *
   * Accepted values: LOCATION_TYPE_UNSPECIFIED, COUNTRY, ADMINISTRATIVE_AREA,
   * SUB_ADMINISTRATIVE_AREA, LOCALITY, POSTAL_CODE, SUB_LOCALITY,
   * SUB_LOCALITY_1, SUB_LOCALITY_2, NEIGHBORHOOD, STREET_ADDRESS
   *
   * @param self::LOCATION_TYPE_* $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return self::LOCATION_TYPE_*
   */
  public function getLocationType()
  {
    return $this->locationType;
  }
  /**
   * Postal address of the location that includes human readable information,
   * such as postal delivery and payments addresses. Given a postal address, a
   * postal service can deliver items to a premises, P.O. Box, or other delivery
   * location.
   *
   * @param PostalAddress $postalAddress
   */
  public function setPostalAddress(PostalAddress $postalAddress)
  {
    $this->postalAddress = $postalAddress;
  }
  /**
   * @return PostalAddress
   */
  public function getPostalAddress()
  {
    return $this->postalAddress;
  }
  public function setRadiusMiles($radiusMiles)
  {
    $this->radiusMiles = $radiusMiles;
  }
  public function getRadiusMiles()
  {
    return $this->radiusMiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Location::class, 'Google_Service_CloudTalentSolution_Location');
