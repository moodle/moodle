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

namespace Google\Service\PaymentsResellerSubscription;

class EntitleSubscriptionRequestLineItemEntitlementDetails extends \Google\Collection
{
  protected $collection_key = 'products';
  /**
   * Required. The index of the line item to be entitled.
   *
   * @var int
   */
  public $lineItemIndex;
  /**
   * Optional. Only applicable if the line item corresponds to a hard bundle.
   * Product resource names that identify the bundle elements to be entitled in
   * the line item. If unspecified, all bundle elements will be entitled. The
   * format is 'partners/{partner_id}/products/{product_id}'.
   *
   * @var string[]
   */
  public $products;

  /**
   * Required. The index of the line item to be entitled.
   *
   * @param int $lineItemIndex
   */
  public function setLineItemIndex($lineItemIndex)
  {
    $this->lineItemIndex = $lineItemIndex;
  }
  /**
   * @return int
   */
  public function getLineItemIndex()
  {
    return $this->lineItemIndex;
  }
  /**
   * Optional. Only applicable if the line item corresponds to a hard bundle.
   * Product resource names that identify the bundle elements to be entitled in
   * the line item. If unspecified, all bundle elements will be entitled. The
   * format is 'partners/{partner_id}/products/{product_id}'.
   *
   * @param string[] $products
   */
  public function setProducts($products)
  {
    $this->products = $products;
  }
  /**
   * @return string[]
   */
  public function getProducts()
  {
    return $this->products;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EntitleSubscriptionRequestLineItemEntitlementDetails::class, 'Google_Service_PaymentsResellerSubscription_EntitleSubscriptionRequestLineItemEntitlementDetails');
