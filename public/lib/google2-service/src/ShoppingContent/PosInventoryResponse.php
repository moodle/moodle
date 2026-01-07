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

class PosInventoryResponse extends \Google\Model
{
  /**
   * Required. The two-letter ISO 639-1 language code for the item.
   *
   * @var string
   */
  public $contentLanguage;
  /**
   * Global Trade Item Number.
   *
   * @var string
   */
  public $gtin;
  /**
   * Required. A unique identifier for the item.
   *
   * @var string
   */
  public $itemId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#posInventoryResponse`".
   *
   * @var string
   */
  public $kind;
  /**
   * Optional. Supported pickup method for this offer. Unless the value is "not
   * supported", this field must be submitted together with `pickupSla`. For
   * accepted attribute values, see the [local product inventory feed
   * specification](https://support.google.com/merchants/answer/3061342).
   *
   * @var string
   */
  public $pickupMethod;
  /**
   * Optional. Expected date that an order will be ready for pickup relative to
   * the order date. Must be submitted together with `pickupMethod`. For
   * accepted attribute values, see the [local product inventory feed
   * specification](https://support.google.com/merchants/answer/3061342).
   *
   * @var string
   */
  public $pickupSla;
  protected $priceType = Price::class;
  protected $priceDataType = '';
  /**
   * Required. The available quantity of the item.
   *
   * @var string
   */
  public $quantity;
  /**
   * Required. The identifier of the merchant's store. Either a `storeCode`
   * inserted through the API or the code of the store in a Business Profile.
   *
   * @var string
   */
  public $storeCode;
  /**
   * Required. The CLDR territory code for the item.
   *
   * @var string
   */
  public $targetCountry;
  /**
   * Required. The inventory timestamp, in ISO 8601 format.
   *
   * @var string
   */
  public $timestamp;

  /**
   * Required. The two-letter ISO 639-1 language code for the item.
   *
   * @param string $contentLanguage
   */
  public function setContentLanguage($contentLanguage)
  {
    $this->contentLanguage = $contentLanguage;
  }
  /**
   * @return string
   */
  public function getContentLanguage()
  {
    return $this->contentLanguage;
  }
  /**
   * Global Trade Item Number.
   *
   * @param string $gtin
   */
  public function setGtin($gtin)
  {
    $this->gtin = $gtin;
  }
  /**
   * @return string
   */
  public function getGtin()
  {
    return $this->gtin;
  }
  /**
   * Required. A unique identifier for the item.
   *
   * @param string $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string
   */
  public function getItemId()
  {
    return $this->itemId;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#posInventoryResponse`".
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
   * Optional. Supported pickup method for this offer. Unless the value is "not
   * supported", this field must be submitted together with `pickupSla`. For
   * accepted attribute values, see the [local product inventory feed
   * specification](https://support.google.com/merchants/answer/3061342).
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
   * Optional. Expected date that an order will be ready for pickup relative to
   * the order date. Must be submitted together with `pickupMethod`. For
   * accepted attribute values, see the [local product inventory feed
   * specification](https://support.google.com/merchants/answer/3061342).
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
   * Required. The current price of the item.
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
   * Required. The available quantity of the item.
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
   * Required. The identifier of the merchant's store. Either a `storeCode`
   * inserted through the API or the code of the store in a Business Profile.
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
  /**
   * Required. The CLDR territory code for the item.
   *
   * @param string $targetCountry
   */
  public function setTargetCountry($targetCountry)
  {
    $this->targetCountry = $targetCountry;
  }
  /**
   * @return string
   */
  public function getTargetCountry()
  {
    return $this->targetCountry;
  }
  /**
   * Required. The inventory timestamp, in ISO 8601 format.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PosInventoryResponse::class, 'Google_Service_ShoppingContent_PosInventoryResponse');
