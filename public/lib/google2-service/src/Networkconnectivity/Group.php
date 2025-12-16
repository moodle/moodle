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

namespace Google\Service\Networkconnectivity;

class Group extends \Google\Model
{
  /**
   * No state information available
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The resource's create operation is in progress.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The resource is active
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource's delete operation is in progress.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource's accept operation is in progress.
   */
  public const STATE_ACCEPTING = 'ACCEPTING';
  /**
   * The resource's reject operation is in progress.
   */
  public const STATE_REJECTING = 'REJECTING';
  /**
   * The resource's update operation is in progress.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The resource is inactive.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The hub associated with this spoke resource has been deleted. This state
   * applies to spoke resources only.
   */
  public const STATE_OBSOLETE = 'OBSOLETE';
  /**
   * The resource is in an undefined state due to resource creation or deletion
   * failure. You can try to delete the resource later or contact support for
   * help.
   */
  public const STATE_FAILED = 'FAILED';
  protected $autoAcceptType = AutoAccept::class;
  protected $autoAcceptDataType = '';
  /**
   * Output only. The time the group was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the group.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Labels in key-value pair format. For more information about
   * labels, see [Requirements for labels](https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels#requirements).
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. The name of the group. Group names must be unique. They use the
   * following form:
   * `projects/{project_number}/locations/global/hubs/{hub}/groups/{group_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The name of the route table that corresponds to this group.
   * They use the following form: `projects/{project_number}/locations/global/hu
   * bs/{hub_id}/routeTables/{route_table_id}`
   *
   * @var string
   */
  public $routeTable;
  /**
   * Output only. The current lifecycle state of this group.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The Google-generated UUID for the group. This value is unique
   * across all group resources. If a group is deleted and another with the same
   * name is created, the new route table is assigned a different unique_id.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time the group was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The auto-accept setting for this group.
   *
   * @param AutoAccept $autoAccept
   */
  public function setAutoAccept(AutoAccept $autoAccept)
  {
    $this->autoAccept = $autoAccept;
  }
  /**
   * @return AutoAccept
   */
  public function getAutoAccept()
  {
    return $this->autoAccept;
  }
  /**
   * Output only. The time the group was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The description of the group.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Labels in key-value pair format. For more information about
   * labels, see [Requirements for labels](https://cloud.google.com/resource-
   * manager/docs/creating-managing-labels#requirements).
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Immutable. The name of the group. Group names must be unique. They use the
   * following form:
   * `projects/{project_number}/locations/global/hubs/{hub}/groups/{group_id}`
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
   * Output only. The name of the route table that corresponds to this group.
   * They use the following form: `projects/{project_number}/locations/global/hu
   * bs/{hub_id}/routeTables/{route_table_id}`
   *
   * @param string $routeTable
   */
  public function setRouteTable($routeTable)
  {
    $this->routeTable = $routeTable;
  }
  /**
   * @return string
   */
  public function getRouteTable()
  {
    return $this->routeTable;
  }
  /**
   * Output only. The current lifecycle state of this group.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, ACCEPTING,
   * REJECTING, UPDATING, INACTIVE, OBSOLETE, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The Google-generated UUID for the group. This value is unique
   * across all group resources. If a group is deleted and another with the same
   * name is created, the new route table is assigned a different unique_id.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time the group was last updated.
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
class_alias(Group::class, 'Google_Service_Networkconnectivity_Group');
