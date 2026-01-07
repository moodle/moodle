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

namespace Google\Service\DisplayVideo;

class InventorySourceGroup extends \Google\Model
{
  /**
   * Required. The display name of the inventory source group. Must be UTF-8
   * encoded with a maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The unique ID of the inventory source group. Assigned by the
   * system.
   *
   * @var string
   */
  public $inventorySourceGroupId;
  /**
   * Output only. The resource name of the inventory source group.
   *
   * @var string
   */
  public $name;

  /**
   * Required. The display name of the inventory source group. Must be UTF-8
   * encoded with a maximum size of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The unique ID of the inventory source group. Assigned by the
   * system.
   *
   * @param string $inventorySourceGroupId
   */
  public function setInventorySourceGroupId($inventorySourceGroupId)
  {
    $this->inventorySourceGroupId = $inventorySourceGroupId;
  }
  /**
   * @return string
   */
  public function getInventorySourceGroupId()
  {
    return $this->inventorySourceGroupId;
  }
  /**
   * Output only. The resource name of the inventory source group.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InventorySourceGroup::class, 'Google_Service_DisplayVideo_InventorySourceGroup');
