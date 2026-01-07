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

namespace Google\Service\Backupdr;

class CloudSqlInstanceDataSourceProperties extends \Google\Model
{
  /**
   * Output only. The installed database version of the Cloud SQL instance.
   *
   * @var string
   */
  public $databaseInstalledVersion;
  /**
   * Output only. The instance creation timestamp.
   *
   * @var string
   */
  public $instanceCreateTime;
  /**
   * Output only. The tier (or machine type) for this instance. Example: `db-
   * custom-1-3840`
   *
   * @var string
   */
  public $instanceTier;
  /**
   * Output only. Name of the Cloud SQL instance backed up by the datasource.
   * Format: projects/{project}/instances/{instance}
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The installed database version of the Cloud SQL instance.
   *
   * @param string $databaseInstalledVersion
   */
  public function setDatabaseInstalledVersion($databaseInstalledVersion)
  {
    $this->databaseInstalledVersion = $databaseInstalledVersion;
  }
  /**
   * @return string
   */
  public function getDatabaseInstalledVersion()
  {
    return $this->databaseInstalledVersion;
  }
  /**
   * Output only. The instance creation timestamp.
   *
   * @param string $instanceCreateTime
   */
  public function setInstanceCreateTime($instanceCreateTime)
  {
    $this->instanceCreateTime = $instanceCreateTime;
  }
  /**
   * @return string
   */
  public function getInstanceCreateTime()
  {
    return $this->instanceCreateTime;
  }
  /**
   * Output only. The tier (or machine type) for this instance. Example: `db-
   * custom-1-3840`
   *
   * @param string $instanceTier
   */
  public function setInstanceTier($instanceTier)
  {
    $this->instanceTier = $instanceTier;
  }
  /**
   * @return string
   */
  public function getInstanceTier()
  {
    return $this->instanceTier;
  }
  /**
   * Output only. Name of the Cloud SQL instance backed up by the datasource.
   * Format: projects/{project}/instances/{instance}
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
class_alias(CloudSqlInstanceDataSourceProperties::class, 'Google_Service_Backupdr_CloudSqlInstanceDataSourceProperties');
