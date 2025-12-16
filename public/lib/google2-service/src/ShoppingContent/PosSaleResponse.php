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

class PosSaleResponse extends \Google\Model
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
   * "`content#posSaleResponse`".
   *
   * @var string
   */
  public $kind;
  protected $priceType = Price::class;
  protected $priceDataType = '';
  /**
   * Required. The relative change of the available quantity. Negative for items
   * returned.
   *
   * @var string
   */
  public $quantity;
  /**
   * A unique ID to group items from the same sale event.
   *
   * @var string
   */
  public $saleId;
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
   * "`content#posSaleResponse`".
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
   * Required. The price of the item.
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
   * Required. The relative change of the available quantity. Negative for items
   * returned.
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
   * A unique ID to group items from the same sale event.
   *
   * @param string $saleId
   */
  public function setSaleId($saleId)
  {
    $this->saleId = $saleId;
  }
  /**
   * @return string
   */
  public function getSaleId()
  {
    return $this->saleId;
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
class_alias(PosSaleResponse::class, 'Google_Service_ShoppingContent_PosSaleResponse');
