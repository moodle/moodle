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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1CloudSqlSource extends \Google\Model
{
  /**
   * Required. The Cloud SQL database to copy the data from with a length limit
   * of 256 characters.
   *
   * @var string
   */
  public $databaseId;
  /**
   * Intermediate Cloud Storage directory used for the import with a length
   * limit of 2,000 characters. Can be specified if one wants to have the Cloud
   * SQL export to a specific Cloud Storage directory. Ensure that the Cloud SQL
   * service account has the necessary Cloud Storage Admin permissions to access
   * the specified Cloud Storage directory.
   *
   * @var string
   */
  public $gcsStagingDir;
  /**
   * Required. The Cloud SQL instance to copy the data from with a length limit
   * of 256 characters.
   *
   * @var string
   */
  public $instanceId;
  /**
   * Option for serverless export. Enabling this option will incur additional
   * cost. More info can be found
   * [here](https://cloud.google.com/sql/pricing#serverless).
   *
   * @var bool
   */
  public $offload;
  /**
   * The project ID that contains the Cloud SQL source. Has a length limit of
   * 128 characters. If not specified, inherits the project ID from the parent
   * request.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. The Cloud SQL table to copy the data from with a length limit of
   * 256 characters.
   *
   * @var string
   */
  public $tableId;

  /**
   * Required. The Cloud SQL database to copy the data from with a length limit
   * of 256 characters.
   *
   * @param string $databaseId
   */
  public function setDatabaseId($databaseId)
  {
    $this->databaseId = $databaseId;
  }
  /**
   * @return string
   */
  public function getDatabaseId()
  {
    return $this->databaseId;
  }
  /**
   * Intermediate Cloud Storage directory used for the import with a length
   * limit of 2,000 characters. Can be specified if one wants to have the Cloud
   * SQL export to a specific Cloud Storage directory. Ensure that the Cloud SQL
   * service account has the necessary Cloud Storage Admin permissions to access
   * the specified Cloud Storage directory.
   *
   * @param string $gcsStagingDir
   */
  public function setGcsStagingDir($gcsStagingDir)
  {
    $this->gcsStagingDir = $gcsStagingDir;
  }
  /**
   * @return string
   */
  public function getGcsStagingDir()
  {
    return $this->gcsStagingDir;
  }
  /**
   * Required. The Cloud SQL instance to copy the data from with a length limit
   * of 256 characters.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * Option for serverless export. Enabling this option will incur additional
   * cost. More info can be found
   * [here](https://cloud.google.com/sql/pricing#serverless).
   *
   * @param bool $offload
   */
  public function setOffload($offload)
  {
    $this->offload = $offload;
  }
  /**
   * @return bool
   */
  public function getOffload()
  {
    return $this->offload;
  }
  /**
   * The project ID that contains the Cloud SQL source. Has a length limit of
   * 128 characters. If not specified, inherits the project ID from the parent
   * request.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Required. The Cloud SQL table to copy the data from with a length limit of
   * 256 characters.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CloudSqlSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CloudSqlSource');
