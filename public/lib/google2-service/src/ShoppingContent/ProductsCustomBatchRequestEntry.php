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

class ProductsCustomBatchRequestEntry extends \Google\Model
{
  /**
   * An entry ID, unique within the batch request.
   *
   * @var string
   */
  public $batchId;
  /**
   * The Content API Supplemental Feed ID. If present then product insertion or
   * deletion applies to a supplemental feed instead of primary Content API
   * feed.
   *
   * @var string
   */
  public $feedId;
  /**
   * The ID of the managing account.
   *
   * @var string
   */
  public $merchantId;
  /**
   * The method of the batch entry. Acceptable values are: - "`delete`" -
   * "`get`" - "`insert`" - "`update`"
   *
   * @var string
   */
  public $method;
  protected $productType = Product::class;
  protected $productDataType = '';
  /**
   * The ID of the product to get or mutate. Only defined if the method is
   * `get`, `delete`, or `update`.
   *
   * @var string
   */
  public $productId;
  /**
   * The comma-separated list of product attributes to be updated. Example:
   * `"title,salePrice"`. Attributes specified in the update mask without a
   * value specified in the body will be deleted from the product. *You must
   * specify the update mask to delete attributes.* Only top-level product
   * attributes can be updated. If not defined, product attributes with set
   * values will be updated and other attributes will stay unchanged. Only
   * defined if the method is `update`.
   *
   * @var string
   */
  public $updateMask;

  /**
   * An entry ID, unique within the batch request.
   *
   * @param string $batchId
   */
  public function setBatchId($batchId)
  {
    $this->batchId = $batchId;
  }
  /**
   * @return string
   */
  public function getBatchId()
  {
    return $this->batchId;
  }
  /**
   * The Content API Supplemental Feed ID. If present then product insertion or
   * deletion applies to a supplemental feed instead of primary Content API
   * feed.
   *
   * @param string $feedId
   */
  public function setFeedId($feedId)
  {
    $this->feedId = $feedId;
  }
  /**
   * @return string
   */
  public function getFeedId()
  {
    return $this->feedId;
  }
  /**
   * The ID of the managing account.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * The method of the batch entry. Acceptable values are: - "`delete`" -
   * "`get`" - "`insert`" - "`update`"
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * The product to insert or update. Only required if the method is `insert` or
   * `update`. If the `update` method is used with `updateMask` only to delete a
   * field, then this isn't required. For example, setting `salePrice` on the
   * `updateMask` and not providing a `product` will result in an existing sale
   * price on the product specified by `productId` being deleted.
   *
   * @param Product $product
   */
  public function setProduct(Product $product)
  {
    $this->product = $product;
  }
  /**
   * @return Product
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * The ID of the product to get or mutate. Only defined if the method is
   * `get`, `delete`, or `update`.
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
   * The comma-separated list of product attributes to be updated. Example:
   * `"title,salePrice"`. Attributes specified in the update mask without a
   * value specified in the body will be deleted from the product. *You must
   * specify the update mask to delete attributes.* Only top-level product
   * attributes can be updated. If not defined, product attributes with set
   * values will be updated and other attributes will stay unchanged. Only
   * defined if the method is `update`.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductsCustomBatchRequestEntry::class, 'Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry');
