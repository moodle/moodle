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

class BusinessChainTargetingOptionDetails extends \Google\Model
{
  /**
   * The geographic region type is unknown.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_UNKNOWN = 'GEO_REGION_TYPE_UNKNOWN';
  /**
   * The geographic region type is other.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_OTHER = 'GEO_REGION_TYPE_OTHER';
  /**
   * The geographic region is a country.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_COUNTRY = 'GEO_REGION_TYPE_COUNTRY';
  /**
   * The geographic region type is region.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_REGION = 'GEO_REGION_TYPE_REGION';
  /**
   * The geographic region is a territory.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_TERRITORY = 'GEO_REGION_TYPE_TERRITORY';
  /**
   * The geographic region is a province.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_PROVINCE = 'GEO_REGION_TYPE_PROVINCE';
  /**
   * The geographic region is a state.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_STATE = 'GEO_REGION_TYPE_STATE';
  /**
   * The geographic region is a prefecture.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_PREFECTURE = 'GEO_REGION_TYPE_PREFECTURE';
  /**
   * The geographic region is a governorate.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_GOVERNORATE = 'GEO_REGION_TYPE_GOVERNORATE';
  /**
   * The geographic region is a canton.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_CANTON = 'GEO_REGION_TYPE_CANTON';
  /**
   * The geographic region is a union territory.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_UNION_TERRITORY = 'GEO_REGION_TYPE_UNION_TERRITORY';
  /**
   * The geographic region is an autonomous community.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_AUTONOMOUS_COMMUNITY = 'GEO_REGION_TYPE_AUTONOMOUS_COMMUNITY';
  /**
   * The geographic region is a designated market area (DMA) region.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_DMA_REGION = 'GEO_REGION_TYPE_DMA_REGION';
  /**
   * The geographic region type is metro.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_METRO = 'GEO_REGION_TYPE_METRO';
  /**
   * The geographic region is a congressional district.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_CONGRESSIONAL_DISTRICT = 'GEO_REGION_TYPE_CONGRESSIONAL_DISTRICT';
  /**
   * The geographic region is a county.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_COUNTY = 'GEO_REGION_TYPE_COUNTY';
  /**
   * The geographic region is a municipality.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_MUNICIPALITY = 'GEO_REGION_TYPE_MUNICIPALITY';
  /**
   * The geographic region is a city.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_CITY = 'GEO_REGION_TYPE_CITY';
  /**
   * The geographic region targeting type is postal code.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_POSTAL_CODE = 'GEO_REGION_TYPE_POSTAL_CODE';
  /**
   * The geographic region targeting type is department.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_DEPARTMENT = 'GEO_REGION_TYPE_DEPARTMENT';
  /**
   * The geographic region is an airport.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_AIRPORT = 'GEO_REGION_TYPE_AIRPORT';
  /**
   * The geographic region is a TV region.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_TV_REGION = 'GEO_REGION_TYPE_TV_REGION';
  /**
   * The geographic region is an okrug.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_OKRUG = 'GEO_REGION_TYPE_OKRUG';
  /**
   * The geographic region is a borough.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_BOROUGH = 'GEO_REGION_TYPE_BOROUGH';
  /**
   * The geographic region is a city region.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_CITY_REGION = 'GEO_REGION_TYPE_CITY_REGION';
  /**
   * The geographic region is an arrondissement.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_ARRONDISSEMENT = 'GEO_REGION_TYPE_ARRONDISSEMENT';
  /**
   * The geographic region is a neighborhood.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_NEIGHBORHOOD = 'GEO_REGION_TYPE_NEIGHBORHOOD';
  /**
   * The geographic region is a university.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_UNIVERSITY = 'GEO_REGION_TYPE_UNIVERSITY';
  /**
   * The geographic region is a district.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_DISTRICT = 'GEO_REGION_TYPE_DISTRICT';
  /**
   * The geographic region is a national park.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_NATIONAL_PARK = 'GEO_REGION_TYPE_NATIONAL_PARK';
  /**
   * The geographic region is a barrio.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_BARRIO = 'GEO_REGION_TYPE_BARRIO';
  /**
   * The geographic region is a sub ward.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_SUB_WARD = 'GEO_REGION_TYPE_SUB_WARD';
  /**
   * The geographic region is a municipality district.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_MUNICIPALITY_DISTRICT = 'GEO_REGION_TYPE_MUNICIPALITY_DISTRICT';
  /**
   * The geographic region is a sub district.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_SUB_DISTRICT = 'GEO_REGION_TYPE_SUB_DISTRICT';
  /**
   * The geographic region is a quarter.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_QUARTER = 'GEO_REGION_TYPE_QUARTER';
  /**
   * The geographic region is a division.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_DIVISION = 'GEO_REGION_TYPE_DIVISION';
  /**
   * The geographic region is a commune.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_COMMUNE = 'GEO_REGION_TYPE_COMMUNE';
  /**
   * The geographic region is a colloquial area.
   */
  public const GEO_REGION_TYPE_GEO_REGION_TYPE_COLLOQUIAL_AREA = 'GEO_REGION_TYPE_COLLOQUIAL_AREA';
  /**
   * Output only. The display name of the business chain, e.g. "KFC", "Chase
   * Bank".
   *
   * @var string
   */
  public $businessChain;
  /**
   * Output only. The display name of the geographic region, e.g. "Ontario,
   * Canada".
   *
   * @var string
   */
  public $geoRegion;
  /**
   * Output only. The type of the geographic region.
   *
   * @var string
   */
  public $geoRegionType;

  /**
   * Output only. The display name of the business chain, e.g. "KFC", "Chase
   * Bank".
   *
   * @param string $businessChain
   */
  public function setBusinessChain($businessChain)
  {
    $this->businessChain = $businessChain;
  }
  /**
   * @return string
   */
  public function getBusinessChain()
  {
    return $this->businessChain;
  }
  /**
   * Output only. The display name of the geographic region, e.g. "Ontario,
   * Canada".
   *
   * @param string $geoRegion
   */
  public function setGeoRegion($geoRegion)
  {
    $this->geoRegion = $geoRegion;
  }
  /**
   * @return string
   */
  public function getGeoRegion()
  {
    return $this->geoRegion;
  }
  /**
   * Output only. The type of the geographic region.
   *
   * Accepted values: GEO_REGION_TYPE_UNKNOWN, GEO_REGION_TYPE_OTHER,
   * GEO_REGION_TYPE_COUNTRY, GEO_REGION_TYPE_REGION, GEO_REGION_TYPE_TERRITORY,
   * GEO_REGION_TYPE_PROVINCE, GEO_REGION_TYPE_STATE,
   * GEO_REGION_TYPE_PREFECTURE, GEO_REGION_TYPE_GOVERNORATE,
   * GEO_REGION_TYPE_CANTON, GEO_REGION_TYPE_UNION_TERRITORY,
   * GEO_REGION_TYPE_AUTONOMOUS_COMMUNITY, GEO_REGION_TYPE_DMA_REGION,
   * GEO_REGION_TYPE_METRO, GEO_REGION_TYPE_CONGRESSIONAL_DISTRICT,
   * GEO_REGION_TYPE_COUNTY, GEO_REGION_TYPE_MUNICIPALITY, GEO_REGION_TYPE_CITY,
   * GEO_REGION_TYPE_POSTAL_CODE, GEO_REGION_TYPE_DEPARTMENT,
   * GEO_REGION_TYPE_AIRPORT, GEO_REGION_TYPE_TV_REGION, GEO_REGION_TYPE_OKRUG,
   * GEO_REGION_TYPE_BOROUGH, GEO_REGION_TYPE_CITY_REGION,
   * GEO_REGION_TYPE_ARRONDISSEMENT, GEO_REGION_TYPE_NEIGHBORHOOD,
   * GEO_REGION_TYPE_UNIVERSITY, GEO_REGION_TYPE_DISTRICT,
   * GEO_REGION_TYPE_NATIONAL_PARK, GEO_REGION_TYPE_BARRIO,
   * GEO_REGION_TYPE_SUB_WARD, GEO_REGION_TYPE_MUNICIPALITY_DISTRICT,
   * GEO_REGION_TYPE_SUB_DISTRICT, GEO_REGION_TYPE_QUARTER,
   * GEO_REGION_TYPE_DIVISION, GEO_REGION_TYPE_COMMUNE,
   * GEO_REGION_TYPE_COLLOQUIAL_AREA
   *
   * @param self::GEO_REGION_TYPE_* $geoRegionType
   */
  public function setGeoRegionType($geoRegionType)
  {
    $this->geoRegionType = $geoRegionType;
  }
  /**
   * @return self::GEO_REGION_TYPE_*
   */
  public function getGeoRegionType()
  {
    return $this->geoRegionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BusinessChainTargetingOptionDetails::class, 'Google_Service_DisplayVideo_BusinessChainTargetingOptionDetails');
