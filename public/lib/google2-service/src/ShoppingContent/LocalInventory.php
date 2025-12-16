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

class LocalInventory extends \Google\Collection
{
  protected $collection_key = 'customAttributes';
  /**
   * The availability of the product. For accepted attribute values, see the
   * local product inventory feed specification.
   *
   * @var string
   */
  public $availability;
  protected $customAttributesType = CustomAttribute::class;
  protected $customAttributesDataType = 'array';
  /**
   * The in-store product location.
   *
   * @var string
   */
  public $instoreProductLocation;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#localInventory`"
   *
   * @var string
   */
  public $kind;
  /**
   * The supported pickup method for this offer. Unless the value is "not
   * supported", this field must be submitted together with `pickupSla`. For
   * accepted attribute values, see the local product inventory feed
   * specification.
   *
   * @var string
   */
  public $pickupMethod;
  /**
   * The expected date that an order will be ready for pickup relative to the
   * order date. Must be submitted together with `pickupMethod`. For accepted
   * attribute values, see the local product inventory feed specification.
   *
   * @var string
   */
  public $pickupSla;
  protected $priceType = Price::class;
  protected $priceDataType = '';
  /**
   * The quantity of the product. Must be nonnegative.
   *
   * @var string
   */
  public $quantity;
  protected $salePriceType = Price::class;
  protected $salePriceDataType = '';
  /**
   * A date range represented by a pair of ISO 8601 dates separated by a space,
   * comma, or slash. Both dates may be specified as 'null' if undecided.
   *
   * @var string
   */
  public $salePriceEffectiveDate;
  /**
   * Required. The store code of this local inventory resource.
   *
   * @var string
   */
  public $storeCode;

  /**
   * The availability of the product. For accepted attribute values, see the
   * local product inventory feed specification.
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
   * A list of custom (merchant-provided) attributes. Can also be used to submit
   * any attribute of the feed specification in its generic form, for example,
   * `{ "name": "size type", "value": "regular" }`.
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
   * The in-store product location.
   *
   * @param string $instoreProductLocation
   */
  public function setInstoreProductLocation($instoreProductLocation)
  {
    $this->instoreProductLocation = $instoreProductLocation;
  }
  /**
   * @return string
   */
  public function getInstoreProductLocation()
  {
    return $this->instoreProductLocation;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#localInventory`"
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
   * The supported pickup method for this offer. Unless the value is "not
   * supported", this field must be submitted together with `pickupSla`. For
   * accepted attribute values, see the local product inventory feed
   * specification.
   *
   * @param string $pickupMethod
   */
  public function setPickupMethod($pickupMethod)
  {
    $this->pickupMethod = $pickupMethod;
  }
  /**
   * @return string
   */
  public function getPickupMethod()
  {
    return $this->pickupMethod;
  }
  /**
   * The expected date that an order will be ready for pickup relative to the
   * order date. Must be submitted together with `pickupMethod`. For accepted
   * attribute values, see the local product inventory feed specification.
   *
   * @param string $pickupSla
   */
  public function setPickupSla($pickupSla)
  {
    $this->pickupSla = $pickupSla;
  }
  /**
   * @return string
   */
  public function getPickupSla()
  {
    return $this->pickupSla;
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
   * The quantity of the product. Must be nonnegative.
   *
   * @param string $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  /**
   * @return string
   */
  public function getQuantity()
  {
    return $this->quantity;
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
   * comma, or slash. Both dates may be specified as 'null' if undecided.
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
  /**
   * Required. The store code of this local inventory resource.
   *
   * @param string $storeCode
   */
  public function setStoreCode($storeCode)
  {
    $this->storeCode = $storeCode;
  }
  /**
   * @return string
   */
  public function getStoreCode()
  {
    return $this->storeCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocalInventory::class, 'Google_Service_ShoppingContent_LocalInventory');
