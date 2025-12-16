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

namespace Google\Service\AndroidEnterprise;

class ProductSet extends \Google\Collection
{
  /**
   * This value should never be sent and ignored if received.
   */
  public const PRODUCT_SET_BEHAVIOR_unknown = 'unknown';
  /**
   * This product set constitutes a whitelist.
   */
  public const PRODUCT_SET_BEHAVIOR_whitelist = 'whitelist';
  /**
   * This product set represents all products. For Android app it represents
   * only "production" track. (The value of the productId field is therefore
   * ignored).
   */
  public const PRODUCT_SET_BEHAVIOR_includeAll = 'includeAll';
  /**
   * This product set represents all approved products. For Android app it
   * represents only "production" track. (The value of the product_id field is
   * therefore ignored).
   */
  public const PRODUCT_SET_BEHAVIOR_allApproved = 'allApproved';
  protected $collection_key = 'productVisibility';
  /**
   * The list of product IDs making up the set of products.
   *
   * @var string[]
   */
  public $productId;
  /**
   * The interpretation of this product set. "unknown" should never be sent and
   * is ignored if received. "whitelist" means that the user is entitled to
   * access the product set. "includeAll" means that all products are
   * accessible, including products that are approved, products with revoked
   * approval, and products that have never been approved. "allApproved" means
   * that the user is entitled to access all products that are approved for the
   * enterprise. If the value is "allApproved" or "includeAll", the productId
   * field is ignored. If no value is provided, it is interpreted as "whitelist"
   * for backwards compatibility. Further "allApproved" or "includeAll" does not
   * enable automatic visibility of "alpha" or "beta" tracks for Android app.
   * Use ProductVisibility to enable "alpha" or "beta" tracks per user.
   *
   * @var string
   */
  public $productSetBehavior;
  protected $productVisibilityType = ProductVisibility::class;
  protected $productVisibilityDataType = 'array';

  /**
   * The list of product IDs making up the set of products.
   *
   * @param string[] $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string[]
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The interpretation of this product set. "unknown" should never be sent and
   * is ignored if received. "whitelist" means that the user is entitled to
   * access the product set. "includeAll" means that all products are
   * accessible, including products that are approved, products with revoked
   * approval, and products that have never been approved. "allApproved" means
   * that the user is entitled to access all products that are approved for the
   * enterprise. If the value is "allApproved" or "includeAll", the productId
   * field is ignored. If no value is provided, it is interpreted as "whitelist"
   * for backwards compatibility. Further "allApproved" or "includeAll" does not
   * enable automatic visibility of "alpha" or "beta" tracks for Android app.
   * Use ProductVisibility to enable "alpha" or "beta" tracks per user.
   *
   * Accepted values: unknown, whitelist, includeAll, allApproved
   *
   * @param self::PRODUCT_SET_BEHAVIOR_* $productSetBehavior
   */
  public function setProductSetBehavior($productSetBehavior)
  {
    $this->productSetBehavior = $productSetBehavior;
  }
  /**
   * @return self::PRODUCT_SET_BEHAVIOR_*
   */
  public function getProductSetBehavior()
  {
    return $this->productSetBehavior;
  }
  /**
   * Additional list of product IDs making up the product set. Unlike the
   * productID array, in this list It's possible to specify which tracks (alpha,
   * beta, production) of a product are visible to the user. See
   * ProductVisibility and its fields for more information. Specifying the same
   * product ID both here and in the productId array is not allowed and it will
   * result in an error.
   *
   * @param ProductVisibility[] $productVisibility
   */
  public function setProductVisibility($productVisibility)
  {
    $this->productVisibility = $productVisibility;
  }
  /**
   * @return ProductVisibility[]
   */
  public function getProductVisibility()
  {
    return $this->productVisibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductSet::class, 'Google_Service_AndroidEnterprise_ProductSet');
