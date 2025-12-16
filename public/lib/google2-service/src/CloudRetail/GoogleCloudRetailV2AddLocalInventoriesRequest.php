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

class GoogleCloudRetailV2AddLocalInventoriesRequest extends \Google\Collection
{
  protected $collection_key = 'localInventories';
  /**
   * Indicates which inventory fields in the provided list of LocalInventory to
   * update. The field is updated to the provided value. If a field is set while
   * the place does not have a previous local inventory, the local inventory at
   * that store is created. If a field is set while the value of that field is
   * not provided, the original field value, if it exists, is deleted. If the
   * mask is not set or set with empty paths, all inventory fields will be
   * updated. If an unsupported or unknown field is provided, an
   * INVALID_ARGUMENT error is returned and the entire update will be ignored.
   *
   * @var string
   */
  public $addMask;
  /**
   * The time when the inventory updates are issued. Used to prevent out-of-
   * order updates on local inventory fields. If not provided, the internal
   * system time will be used.
   *
   * @var string
   */
  public $addTime;
  /**
   * If set to true, and the Product is not found, the local inventory will
   * still be processed and retained for at most 1 day and processed once the
   * Product is created. If set to false, a NOT_FOUND error is returned if the
   * Product is not found.
   *
   * @var bool
   */
  public $allowMissing;
  protected $localInventoriesType = GoogleCloudRetailV2LocalInventory::class;
  protected $localInventoriesDataType = 'array';

  /**
   * Indicates which inventory fields in the provided list of LocalInventory to
   * update. The field is updated to the provided value. If a field is set while
   * the place does not have a previous local inventory, the local inventory at
   * that store is created. If a field is set while the value of that field is
   * not provided, the original field value, if it exists, is deleted. If the
   * mask is not set or set with empty paths, all inventory fields will be
   * updated. If an unsupported or unknown field is provided, an
   * INVALID_ARGUMENT error is returned and the entire update will be ignored.
   *
   * @param string $addMask
   */
  public function setAddMask($addMask)
  {
    $this->addMask = $addMask;
  }
  /**
   * @return string
   */
  public function getAddMask()
  {
    return $this->addMask;
  }
  /**
   * The time when the inventory updates are issued. Used to prevent out-of-
   * order updates on local inventory fields. If not provided, the internal
   * system time will be used.
   *
   * @param string $addTime
   */
  public function setAddTime($addTime)
  {
    $this->addTime = $addTime;
  }
  /**
   * @return string
   */
  public function getAddTime()
  {
    return $this->addTime;
  }
  /**
   * If set to true, and the Product is not found, the local inventory will
   * still be processed and retained for at most 1 day and processed once the
   * Product is created. If set to false, a NOT_FOUND error is returned if the
   * Product is not found.
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
   * Required. A list of inventory information at difference places. Each place
   * is identified by its place ID. At most 3000 inventories are allowed per
   * request.
   *
   * @param GoogleCloudRetailV2LocalInventory[] $localInventories
   */
  public function setLocalInventories($localInventories)
  {
    $this->localInventories = $localInventories;
  }
  /**
   * @return GoogleCloudRetailV2LocalInventory[]
   */
  public function getLocalInventories()
  {
    return $this->localInventories;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2AddLocalInventoriesRequest::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2AddLocalInventoriesRequest');
