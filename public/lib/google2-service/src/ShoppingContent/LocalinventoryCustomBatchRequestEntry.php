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

class LocalinventoryCustomBatchRequestEntry extends \Google\Model
{
  /**
   * An entry ID, unique within the batch request.
   *
   * @var string
   */
  public $batchId;
  protected $localInventoryType = LocalInventory::class;
  protected $localInventoryDataType = '';
  /**
   * The ID of the managing account.
   *
   * @var string
   */
  public $merchantId;
  /**
   * Method of the batch request entry. Acceptable values are: - "`insert`"
   *
   * @var string
   */
  public $method;
  /**
   * The ID of the product for which to update local inventory.
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
   * Local inventory of the product.
   *
   * @param LocalInventory $localInventory
   */
  public function setLocalInventory(LocalInventory $localInventory)
  {
    $this->localInventory = $localInventory;
  }
  /**
   * @return LocalInventory
   */
  public function getLocalInventory()
  {
    return $this->localInventory;
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
   * Method of the batch request entry. Acceptable values are: - "`insert`"
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
   * The ID of the product for which to update local inventory.
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
class_alias(LocalinventoryCustomBatchRequestEntry::class, 'Google_Service_ShoppingContent_LocalinventoryCustomBatchRequestEntry');
