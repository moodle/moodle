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

class AssignedInventorySource extends \Google\Model
{
  /**
   * Output only. The unique ID of the assigned inventory source. The ID is only
   * unique within a given inventory source group. It may be reused in other
   * contexts.
   *
   * @var string
   */
  public $assignedInventorySourceId;
  /**
   * Required. The ID of the inventory source entity being targeted.
   *
   * @var string
   */
  public $inventorySourceId;
  /**
   * Output only. The resource name of the assigned inventory source.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The unique ID of the assigned inventory source. The ID is only
   * unique within a given inventory source group. It may be reused in other
   * contexts.
   *
   * @param string $assignedInventorySourceId
   */
  public function setAssignedInventorySourceId($assignedInventorySourceId)
  {
    $this->assignedInventorySourceId = $assignedInventorySourceId;
  }
  /**
   * @return string
   */
  public function getAssignedInventorySourceId()
  {
    return $this->assignedInventorySourceId;
  }
  /**
   * Required. The ID of the inventory source entity being targeted.
   *
   * @param string $inventorySourceId
   */
  public function setInventorySourceId($inventorySourceId)
  {
    $this->inventorySourceId = $inventorySourceId;
  }
  /**
   * @return string
   */
  public function getInventorySourceId()
  {
    return $this->inventorySourceId;
  }
  /**
   * Output only. The resource name of the assigned inventory source.
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
class_alias(AssignedInventorySource::class, 'Google_Service_DisplayVideo_AssignedInventorySource');
