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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2ProductDetail extends \Google\Model
{
  protected $productType = GoogleCloudRetailV2Product::class;
  protected $productDataType = '';
  /**
   * Quantity of the product associated with the user event. For example, this
   * field will be 2 if two products are added to the shopping cart for
   * `purchase-complete` event. Required for `add-to-cart` and `purchase-
   * complete` event types.
   *
   * @var int
   */
  public $quantity;

  /**
   * Required. Product information. Required field(s): * Product.id Optional
   * override field(s): * Product.price_info If any supported optional fields
   * are provided, we will treat them as a full override when looking up product
   * information from the catalog. Thus, it is important to ensure that the
   * overriding fields are accurate and complete. All other product fields are
   * ignored and instead populated via catalog lookup after event ingestion.
   *
   * @param GoogleCloudRetailV2Product $product
   */
  public function setProduct(GoogleCloudRetailV2Product $product)
  {
    $this->product = $product;
  }
  /**
   * @return GoogleCloudRetailV2Product
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * Quantity of the product associated with the user event. For example, this
   * field will be 2 if two products are added to the shopping cart for
   * `purchase-complete` event. Required for `add-to-cart` and `purchase-
   * complete` event types.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ProductDetail::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ProductDetail');
