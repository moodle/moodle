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

namespace Google\Service\SmartDeviceManagement;

class GoogleHomeEnterpriseSdmV1ParentRelation extends \Google\Model
{
  /**
   * Output only. The custom name of the relation -- e.g., structure/room where
   * the device is assigned to.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The name of the relation -- e.g., structure/room where the
   * device is assigned to. For example: "enterprises/XYZ/structures/ABC" or
   * "enterprises/XYZ/structures/ABC/rooms/123"
   *
   * @var string
   */
  public $parent;

  /**
   * Output only. The custom name of the relation -- e.g., structure/room where
   * the device is assigned to.
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
   * Output only. The name of the relation -- e.g., structure/room where the
   * device is assigned to. For example: "enterprises/XYZ/structures/ABC" or
   * "enterprises/XYZ/structures/ABC/rooms/123"
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleHomeEnterpriseSdmV1ParentRelation::class, 'Google_Service_SmartDeviceManagement_GoogleHomeEnterpriseSdmV1ParentRelation');
