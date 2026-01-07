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

namespace Google\Service\MigrationCenterAPI;

class ReportConfig extends \Google\Collection
{
  protected $collection_key = 'groupPreferencesetAssignments';
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Free-text description.
   *
   * @var string
   */
  public $description;
  /**
   * User-friendly display name. Maximum length is 63 characters.
   *
   * @var string
   */
  public $displayName;
  protected $groupPreferencesetAssignmentsType = ReportConfigGroupPreferenceSetAssignment::class;
  protected $groupPreferencesetAssignmentsDataType = 'array';
  /**
   * Output only. Name of resource.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The timestamp when the resource was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp when the resource was created.
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
   * Free-text description.
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
   * User-friendly display name. Maximum length is 63 characters.
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
   * Required. Collection of combinations of groups and preference sets.
   *
   * @param ReportConfigGroupPreferenceSetAssignment[] $groupPreferencesetAssignments
   */
  public function setGroupPreferencesetAssignments($groupPreferencesetAssignments)
  {
    $this->groupPreferencesetAssignments = $groupPreferencesetAssignments;
  }
  /**
   * @return ReportConfigGroupPreferenceSetAssignment[]
   */
  public function getGroupPreferencesetAssignments()
  {
    return $this->groupPreferencesetAssignments;
  }
  /**
   * Output only. Name of resource.
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
   * Output only. The timestamp when the resource was last updated.
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
class_alias(ReportConfig::class, 'Google_Service_MigrationCenterAPI_ReportConfig');
