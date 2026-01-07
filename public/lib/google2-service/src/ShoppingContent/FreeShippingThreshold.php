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

class FreeShippingThreshold extends \Google\Model
{
  /**
   * Required. The [CLDR territory
   * code](http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) of
   * the country to which an item will ship.
   *
   * @var string
   */
  public $country;
  protected $priceThresholdType = Price::class;
  protected $priceThresholdDataType = '';

  /**
   * Required. The [CLDR territory
   * code](http://www.unicode.org/repos/cldr/tags/latest/common/main/en.xml) of
   * the country to which an item will ship.
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
   * Required. The minimum product price for the shipping cost to become free.
   * Represented as a number.
   *
   * @param Price $priceThreshold
   */
  public function setPriceThreshold(Price $priceThreshold)
  {
    $this->priceThreshold = $priceThreshold;
  }
  /**
   * @return Price
   */
  public function getPriceThreshold()
  {
    return $this->priceThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FreeShippingThreshold::class, 'Google_Service_ShoppingContent_FreeShippingThreshold');
