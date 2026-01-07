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

namespace Google\Service\Config;

class AutoMigrationConfig extends \Google\Model
{
  /**
   * Optional. Whether the auto migration is enabled for the project.
   *
   * @var bool
   */
  public $autoMigrationEnabled;
  /**
   * Identifier. The name of the AutoMigrationConfig. Format:
   * 'projects/{project_id}/locations/{location}/AutoMigrationConfig'.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Time the AutoMigrationConfig was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Whether the auto migration is enabled for the project.
   *
   * @param bool $autoMigrationEnabled
   */
  public function setAutoMigrationEnabled($autoMigrationEnabled)
  {
    $this->autoMigrationEnabled = $autoMigrationEnabled;
  }
  /**
   * @return bool
   */
  public function getAutoMigrationEnabled()
  {
    return $this->autoMigrationEnabled;
  }
  /**
   * Identifier. The name of the AutoMigrationConfig. Format:
   * 'projects/{project_id}/locations/{location}/AutoMigrationConfig'.
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
   * Output only. Time the AutoMigrationConfig was last updated.
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
class_alias(AutoMigrationConfig::class, 'Google_Service_Config_AutoMigrationConfig');
