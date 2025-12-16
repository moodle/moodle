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

class GoogleCloudRetailV2SetInventoryRequest extends \Google\Model
{
  /**
   * If set to true, and the Product with name Product.name is not found, the
   * inventory update will still be processed and retained for at most 1 day
   * until the Product is created. If set to false, a NOT_FOUND error is
   * returned if the Product is not found.
   *
   * @var bool
   */
  public $allowMissing;
  protected $inventoryType = GoogleCloudRetailV2Product::class;
  protected $inventoryDataType = '';
  /**
   * Indicates which inventory fields in the provided Product to update. At
   * least one field must be provided. If an unsupported or unknown field is
   * provided, an INVALID_ARGUMENT error is returned and the entire update will
   * be ignored.
   *
   * @var string
   */
  public $setMask;
  /**
   * The time when the request is issued, used to prevent out-of-order updates
   * on inventory fields with the last update time recorded. If not provided,
   * the internal system time will be used.
   *
   * @var string
   */
  public $setTime;

  /**
   * If set to true, and the Product with name Product.name is not found, the
   * inventory update will still be processed and retained for at most 1 day
   * until the Product is created. If set to false, a NOT_FOUND error is
   * returned if the Product is not found.
   *
   * @param bool $allowMissing
   */
  public function setAllowMissing($allowMissing)
  {
    $this->allowMissing = $allowMissing;
  }
  /**
   * @return bool
   */
  public function getAllowMissing()
  {
    return $this->allowMissing;
  }
  /**
   * Required. The inventory information to update. The allowable fields to
   * update are: * Product.price_info * Product.availability *
   * Product.available_quantity * Product.fulfillment_info The updated inventory
   * fields must be specified in SetInventoryRequest.set_mask. If
   * SetInventoryRequest.inventory.name is empty or invalid, an INVALID_ARGUMENT
   * error is returned. If the caller does not have permission to update the
   * Product named in Product.name, regardless of whether or not it exists, a
   * PERMISSION_DENIED error is returned. If the Product to update does not have
   * existing inventory information, the provided inventory information will be
   * inserted. If the Product to update has existing inventory information, the
   * provided inventory information will be merged while respecting the last
   * update time for each inventory field, using the provided or default value
   * for SetInventoryRequest.set_time. The caller can replace place IDs for a
   * subset of fulfillment types in the following ways: * Adds
   * "fulfillment_info" in SetInventoryRequest.set_mask * Specifies only the
   * desired fulfillment types and corresponding place IDs to update in
   * SetInventoryRequest.inventory.fulfillment_info The caller can clear all
   * place IDs from a subset of fulfillment types in the following ways: * Adds
   * "fulfillment_info" in SetInventoryRequest.set_mask * Specifies only the
   * desired fulfillment types to clear in
   * SetInventoryRequest.inventory.fulfillment_info * Checks that only the
   * desired fulfillment info types have empty
   * SetInventoryRequest.inventory.fulfillment_info.place_ids The last update
   * time is recorded for the following inventory fields: * Product.price_info *
   * Product.availability * Product.available_quantity *
   * Product.fulfillment_info If a full overwrite of inventory information while
   * ignoring timestamps is needed, ProductService.UpdateProduct should be
   * invoked instead.
   *
   * @param GoogleCloudRetailV2Product $inventory
   */
  public function setInventory(GoogleCloudRetailV2Product $inventory)
  {
    $this->inventory = $inventory;
  }
  /**
   * @return GoogleCloudRetailV2Product
   */
  public function getInventory()
  {
    return $this->inventory;
  }
  /**
   * Indicates which inventory fields in the provided Product to update. At
   * least one field must be provided. If an unsupported or unknown field is
   * provided, an INVALID_ARGUMENT error is returned and the entire update will
   * be ignored.
   *
   * @param string $setMask
   */
  public function setSetMask($setMask)
  {
    $this->setMask = $setMask;
  }
  /**
   * @return string
   */
  public function getSetMask()
  {
    return $this->setMask;
  }
  /**
   * The time when the request is issued, used to prevent out-of-order updates
   * on inventory fields with the last update time recorded. If not provided,
   * the internal system time will be used.
   *
   * @param string $setTime
   */
  public function setSetTime($setTime)
  {
    $this->setTime = $setTime;
  }
  /**
   * @return string
   */
  public function getSetTime()
  {
    return $this->setTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SetInventoryRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SetInventoryRequest');
