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

class DatabaseDetails extends \Google\Collection
{
  protected $collection_key = 'schemas';
  /**
   * Optional. The allocated storage for the database in bytes.
   *
   * @var string
   */
  public $allocatedStorageBytes;
  /**
   * Required. The name of the database.
   *
   * @var string
   */
  public $databaseName;
  protected $parentDatabaseDeploymentType = DatabaseDetailsParentDatabaseDeployment::class;
  protected $parentDatabaseDeploymentDataType = '';
  protected $schemasType = DatabaseSchema::class;
  protected $schemasDataType = 'array';

  /**
   * Optional. The allocated storage for the database in bytes.
   *
   * @param string $allocatedStorageBytes
   */
  public function setAllocatedStorageBytes($allocatedStorageBytes)
  {
    $this->allocatedStorageBytes = $allocatedStorageBytes;
  }
  /**
   * @return string
   */
  public function getAllocatedStorageBytes()
  {
    return $this->allocatedStorageBytes;
  }
  /**
   * Required. The name of the database.
   *
   * @param string $databaseName
   */
  public function setDatabaseName($databaseName)
  {
    $this->databaseName = $databaseName;
  }
  /**
   * @return string
   */
  public function getDatabaseName()
  {
    return $this->databaseName;
  }
  /**
   * Required. The parent database deployment that contains the logical
   * database.
   *
   * @param DatabaseDetailsParentDatabaseDeployment $parentDatabaseDeployment
   */
  public function setParentDatabaseDeployment(DatabaseDetailsParentDatabaseDeployment $parentDatabaseDeployment)
  {
    $this->parentDatabaseDeployment = $parentDatabaseDeployment;
  }
  /**
   * @return DatabaseDetailsParentDatabaseDeployment
   */
  public function getParentDatabaseDeployment()
  {
    return $this->parentDatabaseDeployment;
  }
  /**
   * Optional. The database schemas.
   *
   * @param DatabaseSchema[] $schemas
   */
  public function setSchemas($schemas)
  {
    $this->schemas = $schemas;
  }
  /**
   * @return DatabaseSchema[]
   */
  public function getSchemas()
  {
    return $this->schemas;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseDetails::class, 'Google_Service_MigrationCenterAPI_DatabaseDetails');
