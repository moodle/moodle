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

class PosCustomBatchResponseEntry extends \Google\Model
{
  /**
   * The ID of the request entry to which this entry responds.
   *
   * @var string
   */
  public $batchId;
  protected $errorsType = Errors::class;
  protected $errorsDataType = '';
  protected $inventoryType = PosInventory::class;
  protected $inventoryDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#posCustomBatchResponseEntry`"
   *
   * @var string
   */
  public $kind;
  protected $saleType = PosSale::class;
  protected $saleDataType = '';
  protected $storeType = PosStore::class;
  protected $storeDataType = '';

  /**
   * The ID of the request entry to which this entry responds.
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
   * A list of errors defined if, and only if, the request failed.
   *
   * @param Errors $errors
   */
  public function setErrors(Errors $errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Errors
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The updated inventory information.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#posCustomBatchResponseEntry`"
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
   * The updated sale information.
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
   * The retrieved or updated store information.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PosCustomBatchResponseEntry::class, 'Google_Service_ShoppingContent_PosCustomBatchResponseEntry');
