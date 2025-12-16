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

namespace Google\Service\VMMigrationService;

class Group extends \Google\Model
{
  /**
   * Group type is not specified. This defaults to Compute Engine targets.
   */
  public const MIGRATION_TARGET_TYPE_MIGRATION_TARGET_TYPE_UNSPECIFIED = 'MIGRATION_TARGET_TYPE_UNSPECIFIED';
  /**
   * All MigratingVMs in the group must have Compute Engine targets.
   */
  public const MIGRATION_TARGET_TYPE_MIGRATION_TARGET_TYPE_GCE = 'MIGRATION_TARGET_TYPE_GCE';
  /**
   * All MigratingVMs in the group must have Compute Engine Disks targets.
   */
  public const MIGRATION_TARGET_TYPE_MIGRATION_TARGET_TYPE_DISKS = 'MIGRATION_TARGET_TYPE_DISKS';
  /**
   * Output only. The create time timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * User-provided description of the group.
   *
   * @var string
   */
  public $description;
  /**
   * Display name is a user defined name for this group which can be updated.
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. The target type of this group.
   *
   * @var string
   */
  public $migrationTargetType;
  /**
   * Output only. The Group name.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The update time timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The create time timestamp.
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
   * User-provided description of the group.
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
   * Display name is a user defined name for this group which can be updated.
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
   * Immutable. The target type of this group.
   *
   * Accepted values: MIGRATION_TARGET_TYPE_UNSPECIFIED,
   * MIGRATION_TARGET_TYPE_GCE, MIGRATION_TARGET_TYPE_DISKS
   *
   * @param self::MIGRATION_TARGET_TYPE_* $migrationTargetType
   */
  public function setMigrationTargetType($migrationTargetType)
  {
    $this->migrationTargetType = $migrationTargetType;
  }
  /**
   * @return self::MIGRATION_TARGET_TYPE_*
   */
  public function getMigrationTargetType()
  {
    return $this->migrationTargetType;
  }
  /**
   * Output only. The Group name.
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
   * Output only. The update time timestamp.
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
class_alias(Group::class, 'Google_Service_VMMigrationService_Group');
