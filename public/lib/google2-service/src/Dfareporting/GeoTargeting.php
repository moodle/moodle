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

namespace Google\Service\Dfareporting;

class GeoTargeting extends \Google\Collection
{
  protected $collection_key = 'regions';
  protected $citiesType = City::class;
  protected $citiesDataType = 'array';
  protected $countriesType = Country::class;
  protected $countriesDataType = 'array';
  /**
   * Whether or not to exclude the countries in the countries field from
   * targeting. If false, the countries field refers to countries which will be
   * targeted by the ad.
   *
   * @var bool
   */
  public $excludeCountries;
  protected $metrosType = Metro::class;
  protected $metrosDataType = 'array';
  protected $postalCodesType = PostalCode::class;
  protected $postalCodesDataType = 'array';
  protected $regionsType = Region::class;
  protected $regionsDataType = 'array';

  /**
   * Cities to be targeted. For each city only dartId is required. The other
   * fields are populated automatically when the ad is inserted or updated. If
   * targeting a city, do not target or exclude the country of the city, and do
   * not target the metro or region of the city.
   *
   * @param City[] $cities
   */
  public function setCities($cities)
  {
    $this->cities = $cities;
  }
  /**
   * @return City[]
   */
  public function getCities()
  {
    return $this->cities;
  }
  /**
   * Countries to be targeted or excluded from targeting, depending on the
   * setting of the excludeCountries field. For each country only dartId is
   * required. The other fields are populated automatically when the ad is
   * inserted or updated. If targeting or excluding a country, do not target
   * regions, cities, metros, or postal codes in the same country.
   *
   * @param Country[] $countries
   */
  public function setCountries($countries)
  {
    $this->countries = $countries;
  }
  /**
   * @return Country[]
   */
  public function getCountries()
  {
    return $this->countries;
  }
  /**
   * Whether or not to exclude the countries in the countries field from
   * targeting. If false, the countries field refers to countries which will be
   * targeted by the ad.
   *
   * @param bool $excludeCountries
   */
  public function setExcludeCountries($excludeCountries)
  {
    $this->excludeCountries = $excludeCountries;
  }
  /**
   * @return bool
   */
  public function getExcludeCountries()
  {
    return $this->excludeCountries;
  }
  /**
   * Metros to be targeted. For each metro only dmaId is required. The other
   * fields are populated automatically when the ad is inserted or updated. If
   * targeting a metro, do not target or exclude the country of the metro.
   *
   * @param Metro[] $metros
   */
  public function setMetros($metros)
  {
    $this->metros = $metros;
  }
  /**
   * @return Metro[]
   */
  public function getMetros()
  {
    return $this->metros;
  }
  /**
   * Postal codes to be targeted. For each postal code only id is required. The
   * other fields are populated automatically when the ad is inserted or
   * updated. If targeting a postal code, do not target or exclude the country
   * of the postal code.
   *
   * @param PostalCode[] $postalCodes
   */
  public function setPostalCodes($postalCodes)
  {
    $this->postalCodes = $postalCodes;
  }
  /**
   * @return PostalCode[]
   */
  public function getPostalCodes()
  {
    return $this->postalCodes;
  }
  /**
   * Regions to be targeted. For each region only dartId is required. The other
   * fields are populated automatically when the ad is inserted or updated. If
   * targeting a region, do not target or exclude the country of the region.
   *
   * @param Region[] $regions
   */
  public function setRegions($regions)
  {
    $this->regions = $regions;
  }
  /**
   * @return Region[]
   */
  public function getRegions()
  {
    return $this->regions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeoTargeting::class, 'Google_Service_Dfareporting_GeoTargeting');
