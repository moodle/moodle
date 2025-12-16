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

namespace Google\Service\OracleDatabase;

class AutonomousDatabaseBackup extends \Google\Model
{
  /**
   * Required. The name of the Autonomous Database resource for which the backup
   * is being created. Format: projects/{project}/locations/{region}/autonomousD
   * atabases/{autonomous_database}
   *
   * @var string
   */
  public $autonomousDatabase;
  /**
   * Optional. User friendly name for the Backup. The name does not have to be
   * unique.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. labels or tags associated with the resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The name of the Autonomous Database Backup resource with the
   * format: projects/{project}/locations/{region}/autonomousDatabaseBackups/{au
   * tonomous_database_backup}
   *
   * @var string
   */
  public $name;
  protected $propertiesType = AutonomousDatabaseBackupProperties::class;
  protected $propertiesDataType = '';

  /**
   * Required. The name of the Autonomous Database resource for which the backup
   * is being created. Format: projects/{project}/locations/{region}/autonomousD
   * atabases/{autonomous_database}
   *
   * @param string $autonomousDatabase
   */
  public function setAutonomousDatabase($autonomousDatabase)
  {
    $this->autonomousDatabase = $autonomousDatabase;
  }
  /**
   * @return string
   */
  public function getAutonomousDatabase()
  {
    return $this->autonomousDatabase;
  }
  /**
   * Optional. User friendly name for the Backup. The name does not have to be
   * unique.
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
   * Optional. labels or tags associated with the resource.
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
   * Identifier. The name of the Autonomous Database Backup resource with the
   * format: projects/{project}/locations/{region}/autonomousDatabaseBackups/{au
   * tonomous_database_backup}
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
   * Optional. Various properties of the backup.
   *
   * @param AutonomousDatabaseBackupProperties $properties
   */
  public function setProperties(AutonomousDatabaseBackupProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return AutonomousDatabaseBackupProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutonomousDatabaseBackup::class, 'Google_Service_OracleDatabase_AutonomousDatabaseBackup');
