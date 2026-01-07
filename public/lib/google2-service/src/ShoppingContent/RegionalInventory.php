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

class RegionalInventory extends \Google\Collection
{
  protected $collection_key = 'customAttributes';
  /**
   * The availability of the product.
   *
   * @var string
   */
  public $availability;
  protected $customAttributesType = CustomAttribute::class;
  protected $customAttributesDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#regionalInventory`".
   *
   * @var string
   */
  public $kind;
  protected $priceType = Price::class;
  protected $priceDataType = '';
  /**
   * The ID uniquely identifying each region.
   *
   * @var string
   */
  public $regionId;
  protected $salePriceType = Price::class;
  protected $salePriceDataType = '';
  /**
   * A date range represented by a pair of ISO 8601 dates separated by a space,
   * comma, or slash. Both dates might be specified as 'null' if undecided.
   *
   * @var string
   */
  public $salePriceEffectiveDate;

  /**
   * The availability of the product.
   *
   * @param string $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return string
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * A list of custom (merchant-provided) attributes. It can also be used for
   * submitting any attribute of the feed specification in its generic form.
   *
   * @param CustomAttribute[] $customAttributes
   */
  public function setCustomAttributes($customAttributes)
  {
    $this->customAttributes = $customAttributes;
  }
  /**
   * @return CustomAttribute[]
   */
  public function getCustomAttributes()
  {
    return $this->customAttributes;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#regionalInventory`".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The price of the product.
   *
   * @param Price $price
   */
  public function setPrice(Price $price)
  {
    $this->price = $price;
  }
  /**
   * @return Price
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * The ID uniquely identifying each region.
   *
   * @param string $regionId
   */
  public function setRegionId($regionId)
  {
    $this->regionId = $regionId;
  }
  /**
   * @return string
   */
  public function getRegionId()
  {
    return $this->regionId;
  }
  /**
   * The sale price of the product. Mandatory if `sale_price_effective_date` is
   * defined.
   *
   * @param Price $salePrice
   */
  public function setSalePrice(Price $salePrice)
  {
    $this->salePrice = $salePrice;
  }
  /**
   * @return Price
   */
  public function getSalePrice()
  {
    return $this->salePrice;
  }
  /**
   * A date range represented by a pair of ISO 8601 dates separated by a space,
   * comma, or slash. Both dates might be specified as 'null' if undecided.
   *
   * @param string $salePriceEffectiveDate
   */
  public function setSalePriceEffectiveDate($salePriceEffectiveDate)
  {
    $this->salePriceEffectiveDate = $salePriceEffectiveDate;
  }
  /**
   * @return string
   */
  public function getSalePriceEffectiveDate()
  {
    return $this->salePriceEffectiveDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionalInventory::class, 'Google_Service_ShoppingContent_RegionalInventory');
