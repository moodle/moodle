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

namespace Google\Service\Monitoring;

class Group extends \Google\Model
{
  /**
   * A user-assigned name for this group, used only for display purposes.
   *
   * @var string
   */
  public $displayName;
  /**
   * The filter used to determine which monitored resources belong to this
   * group.
   *
   * @var string
   */
  public $filter;
  /**
   * If true, the members of this group are considered to be a cluster. The
   * system can perform additional analysis on groups that are clusters.
   *
   * @var bool
   */
  public $isCluster;
  /**
   * Output only. The name of this group. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/groups/[GROUP_ID] When creating a group,
   * this field is ignored and a new name is created consisting of the project
   * specified in the call to CreateGroup and a unique [GROUP_ID] that is
   * generated automatically.
   *
   * @var string
   */
  public $name;
  /**
   * The name of the group's parent, if it has one. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/groups/[GROUP_ID] For groups with no
   * parent, parent_name is the empty string, "".
   *
   * @var string
   */
  public $parentName;

  /**
   * A user-assigned name for this group, used only for display purposes.
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
   * The filter used to determine which monitored resources belong to this
   * group.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * If true, the members of this group are considered to be a cluster. The
   * system can perform additional analysis on groups that are clusters.
   *
   * @param bool $isCluster
   */
  public function setIsCluster($isCluster)
  {
    $this->isCluster = $isCluster;
  }
  /**
   * @return bool
   */
  public function getIsCluster()
  {
    return $this->isCluster;
  }
  /**
   * Output only. The name of this group. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/groups/[GROUP_ID] When creating a group,
   * this field is ignored and a new name is created consisting of the project
   * specified in the call to CreateGroup and a unique [GROUP_ID] that is
   * generated automatically.
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
   * The name of the group's parent, if it has one. The format is:
   * projects/[PROJECT_ID_OR_NUMBER]/groups/[GROUP_ID] For groups with no
   * parent, parent_name is the empty string, "".
   *
   * @param string $parentName
   */
  public function setParentName($parentName)
  {
    $this->parentName = $parentName;
  }
  /**
   * @return string
   */
  public function getParentName()
  {
    return $this->parentName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Group::class, 'Google_Service_Monitoring_Group');
