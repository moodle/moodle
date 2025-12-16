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

namespace Google\Service\SecurityCommandCenter;

class GroupMembership extends \Google\Model
{
  /**
   * Default value.
   */
  public const GROUP_TYPE_GROUP_TYPE_UNSPECIFIED = 'GROUP_TYPE_UNSPECIFIED';
  /**
   * Group represents a toxic combination.
   */
  public const GROUP_TYPE_GROUP_TYPE_TOXIC_COMBINATION = 'GROUP_TYPE_TOXIC_COMBINATION';
  /**
   * Group represents a chokepoint.
   */
  public const GROUP_TYPE_GROUP_TYPE_CHOKEPOINT = 'GROUP_TYPE_CHOKEPOINT';
  /**
   * ID of the group.
   *
   * @var string
   */
  public $groupId;
  /**
   * Type of group.
   *
   * @var string
   */
  public $groupType;

  /**
   * ID of the group.
   *
   * @param string $groupId
   */
  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }
  /**
   * @return string
   */
  public function getGroupId()
  {
    return $this->groupId;
  }
  /**
   * Type of group.
   *
   * Accepted values: GROUP_TYPE_UNSPECIFIED, GROUP_TYPE_TOXIC_COMBINATION,
   * GROUP_TYPE_CHOKEPOINT
   *
   * @param self::GROUP_TYPE_* $groupType
   */
  public function setGroupType($groupType)
  {
    $this->groupType = $groupType;
  }
  /**
   * @return self::GROUP_TYPE_*
   */
  public function getGroupType()
  {
    return $this->groupType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupMembership::class, 'Google_Service_SecurityCommandCenter_GroupMembership');
