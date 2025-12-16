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

class OrderTrackingSignalLineItemDetails extends \Google\Model
{
  /**
   * Brand of the product.
   *
   * @var string
   */
  public $brand;
  /**
   * The Global Trade Item Number.
   *
   * @var string
   */
  public $gtin;
  /**
   * Required. The ID for this line item.
   *
   * @var string
   */
  public $lineItemId;
  /**
   * The manufacturer part number.
   *
   * @var string
   */
  public $mpn;
  /**
   * Plain text description of this product (deprecated: Please use
   * product_title instead).
   *
   * @deprecated
   * @var string
   */
  public $productDescription;
  /**
   * Required. The Content API REST ID of the product, in the form
   * channel:contentLanguage:targetCountry:offerId.
   *
   * @var string
   */
  public $productId;
  /**
   * Plain text title of this product.
   *
   * @var string
   */
  public $productTitle;
  /**
   * The quantity of the line item in the order.
   *
   * @var string
   */
  public $quantity;
  /**
   * Merchant SKU for this item (deprecated).
   *
   * @deprecated
   * @var string
   */
  public $sku;
  /**
   * Universal product code for this item (deprecated: Please use GTIN instead).
   *
   * @deprecated
   * @var string
   */
  public $upc;

  /**
   * Brand of the product.
   *
   * @param string $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * The Global Trade Item Number.
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
   * Required. The ID for this line item.
   *
   * @param string $lineItemId
   */
  public function setLineItemId($lineItemId)
  {
    $this->lineItemId = $lineItemId;
  }
  /**
   * @return string
   */
  public function getLineItemId()
  {
    return $this->lineItemId;
  }
  /**
   * The manufacturer part number.
   *
   * @param string $mpn
   */
  public function setMpn($mpn)
  {
    $this->mpn = $mpn;
  }
  /**
   * @return string
   */
  public function getMpn()
  {
    return $this->mpn;
  }
  /**
   * Plain text description of this product (deprecated: Please use
   * product_title instead).
   *
   * @deprecated
   * @param string $productDescription
   */
  public function setProductDescription($productDescription)
  {
    $this->productDescription = $productDescription;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProductDescription()
  {
    return $this->productDescription;
  }
  /**
   * Required. The Content API REST ID of the product, in the form
   * channel:contentLanguage:targetCountry:offerId.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * Plain text title of this product.
   *
   * @param string $productTitle
   */
  public function setProductTitle($productTitle)
  {
    $this->productTitle = $productTitle;
  }
  /**
   * @return string
   */
  public function getProductTitle()
  {
    return $this->productTitle;
  }
  /**
   * The quantity of the line item in the order.
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
   * Merchant SKU for this item (deprecated).
   *
   * @deprecated
   * @param string $sku
   */
  public function setSku($sku)
  {
    $this->sku = $sku;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getSku()
  {
    return $this->sku;
  }
  /**
   * Universal product code for this item (deprecated: Please use GTIN instead).
   *
   * @deprecated
   * @param string $upc
   */
  public function setUpc($upc)
  {
    $this->upc = $upc;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getUpc()
  {
    return $this->upc;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrderTrackingSignalLineItemDetails::class, 'Google_Service_ShoppingContent_OrderTrackingSignalLineItemDetails');
