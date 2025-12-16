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

namespace Google\Service\ShoppingContent;

class ProductTax extends \Google\Model
{
  /**
   * The country within which the item is taxed, specified as a CLDR territory
   * code.
   *
   * @var string
   */
  public $country;
  /**
   * The numeric ID of a location that the tax rate applies to as defined in the
   * Google Ads API.
   *
   * @var string
   */
  public $locationId;
  /**
   * The postal code range that the tax rate applies to, represented by a ZIP
   * code, a ZIP code prefix using * wildcard, a range between two ZIP codes or
   * two ZIP code prefixes of equal length. Examples: 94114, 94*, 94002-95460,
   * 94*-95*.
   *
   * @var string
   */
  public $postalCode;
  /**
   * The percentage of tax rate that applies to the item price.
   *
   * @var 
   */
  public $rate;
  /**
   * The geographic region to which the tax rate applies.
   *
   * @var string
   */
  public $region;
  /**
   * Should be set to true if tax is charged on shipping.
   *
   * @var bool
   */
  public $taxShip;

  /**
   * The country within which the item is taxed, specified as a CLDR territory
   * code.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * The numeric ID of a location that the tax rate applies to as defined in the
   * Google Ads API.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * The postal code range that the tax rate applies to, represented by a ZIP
   * code, a ZIP code prefix using * wildcard, a range between two ZIP codes or
   * two ZIP code prefixes of equal length. Examples: 94114, 94*, 94002-95460,
   * 94*-95*.
   *
   * @param string $postalCode
   */
  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }
  /**
   * @return string
   */
  public function getPostalCode()
  {
    return $this->postalCode;
  }
  public function setRate($rate)
  {
    $this->rate = $rate;
  }
  public function getRate()
  {
    return $this->rate;
  }
  /**
   * The geographic region to which the tax rate applies.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Should be set to true if tax is charged on shipping.
   *
   * @param bool $taxShip
   */
  public function setTaxShip($taxShip)
  {
    $this->taxShip = $taxShip;
  }
  /**
   * @return bool
   */
  public function getTaxShip()
  {
    return $this->taxShip;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductTax::class, 'Google_Service_ShoppingContent_ProductTax');
