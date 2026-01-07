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

class ProductstatusesCustomBatchRequestEntry extends \Google\Collection
{
  protected $collection_key = 'destinations';
  /**
   * An entry ID, unique within the batch request.
   *
   * @var string
   */
  public $batchId;
  /**
   * If set, only issues for the specified destinations are returned, otherwise
   * only issues for the Shopping destination.
   *
   * @var string[]
   */
  public $destinations;
  /**
   * Deprecated: Setting this field has no effect and attributes are never
   * included.
   *
   * @deprecated
   * @var bool
   */
  public $includeAttributes;
  /**
   * The ID of the managing account.
   *
   * @var string
   */
  public $merchantId;
  /**
   * The method of the batch entry. Acceptable values are: - "`get`"
   *
   * @var string
   */
  public $method;
  /**
   * The ID of the product whose status to get.
   *
   * @var string
   */
  public $productId;

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
   * If set, only issues for the specified destinations are returned, otherwise
   * only issues for the Shopping destination.
   *
   * @param string[] $destinations
   */
  public function setDestinations($destinations)
  {
    $this->destinations = $destinations;
  }
  /**
   * @return string[]
   */
  public function getDestinations()
  {
    return $this->destinations;
  }
  /**
   * Deprecated: Setting this field has no effect and attributes are never
   * included.
   *
   * @deprecated
   * @param bool $includeAttributes
   */
  public function setIncludeAttributes($includeAttributes)
  {
    $this->includeAttributes = $includeAttributes;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIncludeAttributes()
  {
    return $this->includeAttributes;
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
   * The method of the batch entry. Acceptable values are: - "`get`"
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
   * The ID of the product whose status to get.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductstatusesCustomBatchRequestEntry::class, 'Google_Service_ShoppingContent_ProductstatusesCustomBatchRequestEntry');
