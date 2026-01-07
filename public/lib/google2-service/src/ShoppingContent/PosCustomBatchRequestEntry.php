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

class PosCustomBatchRequestEntry extends \Google\Model
{
  /**
   * An entry ID, unique within the batch request.
   *
   * @var string
   */
  public $batchId;
  protected $inventoryType = PosInventory::class;
  protected $inventoryDataType = '';
  /**
   * The ID of the POS data provider.
   *
   * @var string
   */
  public $merchantId;
  /**
   * The method of the batch entry. Acceptable values are: - "`delete`" -
   * "`get`" - "`insert`" - "`inventory`" - "`sale`"
   *
   * @var string
   */
  public $method;
  protected $saleType = PosSale::class;
  protected $saleDataType = '';
  protected $storeType = PosStore::class;
  protected $storeDataType = '';
  /**
   * The store code. This should be set only if the method is `delete` or `get`.
   *
   * @var string
   */
  public $storeCode;
  /**
   * The ID of the account for which to get/submit data.
   *
   * @var string
   */
  public $targetMerchantId;

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
   * The inventory to submit. This should be set only if the method is
   * `inventory`.
   *
   * @param PosInventory $inventory
   */
  public function setInventory(PosInventory $inventory)
  {
    $this->inventory = $inventory;
  }
  /**
   * @return PosInventory
   */
  public function getInventory()
  {
    return $this->inventory;
  }
  /**
   * The ID of the POS data provider.
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
   * "`get`" - "`insert`" - "`inventory`" - "`sale`"
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
   * The sale information to submit. This should be set only if the method is
   * `sale`.
   *
   * @param PosSale $sale
   */
  public function setSale(PosSale $sale)
  {
    $this->sale = $sale;
  }
  /**
   * @return PosSale
   */
  public function getSale()
  {
    return $this->sale;
  }
  /**
   * The store information to submit. This should be set only if the method is
   * `insert`.
   *
   * @param PosStore $store
   */
  public function setStore(PosStore $store)
  {
    $this->store = $store;
  }
  /**
   * @return PosStore
   */
  public function getStore()
  {
    return $this->store;
  }
  /**
   * The store code. This should be set only if the method is `delete` or `get`.
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
   * The ID of the account for which to get/submit data.
   *
   * @param string $targetMerchantId
   */
  public function setTargetMerchantId($targetMerchantId)
  {
    $this->targetMerchantId = $targetMerchantId;
  }
  /**
   * @return string
   */
  public function getTargetMerchantId()
  {
    return $this->targetMerchantId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PosCustomBatchRequestEntry::class, 'Google_Service_ShoppingContent_PosCustomBatchRequestEntry');
