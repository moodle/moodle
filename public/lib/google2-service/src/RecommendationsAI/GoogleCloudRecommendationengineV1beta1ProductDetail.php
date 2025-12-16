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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1ProductDetail extends \Google\Model
{
  /**
   * Default item stock status. Should never be used.
   */
  public const STOCK_STATE_STOCK_STATE_UNSPECIFIED = 'STOCK_STATE_UNSPECIFIED';
  /**
   * Item in stock.
   */
  public const STOCK_STATE_IN_STOCK = 'IN_STOCK';
  /**
   * Item out of stock.
   */
  public const STOCK_STATE_OUT_OF_STOCK = 'OUT_OF_STOCK';
  /**
   * Item that is in pre-order state.
   */
  public const STOCK_STATE_PREORDER = 'PREORDER';
  /**
   * Item that is back-ordered (i.e. temporarily out of stock).
   */
  public const STOCK_STATE_BACKORDER = 'BACKORDER';
  /**
   * Optional. Quantity of the products in stock when a user event happens.
   * Optional. If provided, this overrides the available quantity in Catalog for
   * this event. and can only be set if `stock_status` is set to `IN_STOCK`.
   * Note that if an item is out of stock, you must set the `stock_state` field
   * to be `OUT_OF_STOCK`. Leaving this field unspecified / as zero is not
   * sufficient to mark the item out of stock.
   *
   * @var int
   */
  public $availableQuantity;
  /**
   * Optional. Currency code for price/costs. Use three-character ISO-4217 code.
   * Required only if originalPrice or displayPrice is set.
   *
   * @var string
   */
  public $currencyCode;
  /**
   * Optional. Display price of the product (e.g. discounted price). If
   * provided, this will override the display price in Catalog for this product.
   *
   * @var float
   */
  public $displayPrice;
  /**
   * Required. Catalog item ID. UTF-8 encoded string with a length limit of 128
   * characters.
   *
   * @var string
   */
  public $id;
  protected $itemAttributesType = GoogleCloudRecommendationengineV1beta1FeatureMap::class;
  protected $itemAttributesDataType = '';
  /**
   * Optional. Original price of the product. If provided, this will override
   * the original price in Catalog for this product.
   *
   * @var float
   */
  public $originalPrice;
  /**
   * Optional. Quantity of the product associated with the user event. For
   * example, this field will be 2 if two products are added to the shopping
   * cart for `add-to-cart` event. Required for `add-to-cart`, `add-to-list`,
   * `remove-from-cart`, `checkout-start`, `purchase-complete`, `refund` event
   * types.
   *
   * @var int
   */
  public $quantity;
  /**
   * Optional. Item stock state. If provided, this overrides the stock state in
   * Catalog for items in this event.
   *
   * @var string
   */
  public $stockState;

  /**
   * Optional. Quantity of the products in stock when a user event happens.
   * Optional. If provided, this overrides the available quantity in Catalog for
   * this event. and can only be set if `stock_status` is set to `IN_STOCK`.
   * Note that if an item is out of stock, you must set the `stock_state` field
   * to be `OUT_OF_STOCK`. Leaving this field unspecified / as zero is not
   * sufficient to mark the item out of stock.
   *
   * @param int $availableQuantity
   */
  public function setAvailableQuantity($availableQuantity)
  {
    $this->availableQuantity = $availableQuantity;
  }
  /**
   * @return int
   */
  public function getAvailableQuantity()
  {
    return $this->availableQuantity;
  }
  /**
   * Optional. Currency code for price/costs. Use three-character ISO-4217 code.
   * Required only if originalPrice or displayPrice is set.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * Optional. Display price of the product (e.g. discounted price). If
   * provided, this will override the display price in Catalog for this product.
   *
   * @param float $displayPrice
   */
  public function setDisplayPrice($displayPrice)
  {
    $this->displayPrice = $displayPrice;
  }
  /**
   * @return float
   */
  public function getDisplayPrice()
  {
    return $this->displayPrice;
  }
  /**
   * Required. Catalog item ID. UTF-8 encoded string with a length limit of 128
   * characters.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. Extra features associated with a product in the user event.
   *
   * @param GoogleCloudRecommendationengineV1beta1FeatureMap $itemAttributes
   */
  public function setItemAttributes(GoogleCloudRecommendationengineV1beta1FeatureMap $itemAttributes)
  {
    $this->itemAttributes = $itemAttributes;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1FeatureMap
   */
  public function getItemAttributes()
  {
    return $this->itemAttributes;
  }
  /**
   * Optional. Original price of the product. If provided, this will override
   * the original price in Catalog for this product.
   *
   * @param float $originalPrice
   */
  public function setOriginalPrice($originalPrice)
  {
    $this->originalPrice = $originalPrice;
  }
  /**
   * @return float
   */
  public function getOriginalPrice()
  {
    return $this->originalPrice;
  }
  /**
   * Optional. Quantity of the product associated with the user event. For
   * example, this field will be 2 if two products are added to the shopping
   * cart for `add-to-cart` event. Required for `add-to-cart`, `add-to-list`,
   * `remove-from-cart`, `checkout-start`, `purchase-complete`, `refund` event
   * types.
   *
   * @param int $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
  /**
   * Optional. Item stock state. If provided, this overrides the stock state in
   * Catalog for items in this event.
   *
   * Accepted values: STOCK_STATE_UNSPECIFIED, IN_STOCK, OUT_OF_STOCK, PREORDER,
   * BACKORDER
   *
   * @param self::STOCK_STATE_* $stockState
   */
  public function setStockState($stockState)
  {
    $this->stockState = $stockState;
  }
  /**
   * @return self::STOCK_STATE_*
   */
  public function getStockState()
  {
    return $this->stockState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1ProductDetail::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1ProductDetail');
