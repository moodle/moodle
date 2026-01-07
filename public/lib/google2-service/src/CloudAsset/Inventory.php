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

namespace Google\Service\CloudAsset;

class Inventory extends \Google\Model
{
  protected $itemsType = Item::class;
  protected $itemsDataType = 'map';
  /**
   * Output only. The `Inventory` API resource name. Format: `projects/{project_
   * number}/locations/{location}/instances/{instance_id}/inventory`
   *
   * @var string
   */
  public $name;
  protected $osInfoType = OsInfo::class;
  protected $osInfoDataType = '';
  /**
   * Output only. Timestamp of the last reported inventory for the VM.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Inventory items related to the VM keyed by an opaque unique identifier for
   * each inventory item. The identifier is unique to each distinct and
   * addressable inventory item and will change, when there is a new package
   * version.
   *
   * @param Item[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Item[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Output only. The `Inventory` API resource name. Format: `projects/{project_
   * number}/locations/{location}/instances/{instance_id}/inventory`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Base level operating system information for the VM.
   *
   * @param OsInfo $osInfo
   */
  public function setOsInfo(OsInfo $osInfo)
  {
    $this->osInfo = $osInfo;
  }
  /**
   * @return OsInfo
   */
  public function getOsInfo()
  {
    return $this->osInfo;
  }
  /**
   * Output only. Timestamp of the last reported inventory for the VM.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Inventory::class, 'Google_Service_CloudAsset_Inventory');
