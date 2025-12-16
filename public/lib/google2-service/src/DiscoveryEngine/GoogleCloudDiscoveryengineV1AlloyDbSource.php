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

class GoogleCloudDiscoveryengineV1AlloyDbSource extends \Google\Model
{
  /**
   * Required. The AlloyDB cluster to copy the data from with a length limit of
   * 256 characters.
   *
   * @var string
   */
  public $clusterId;
  /**
   * Required. The AlloyDB database to copy the data from with a length limit of
   * 256 characters.
   *
   * @var string
   */
  public $databaseId;
  /**
   * Intermediate Cloud Storage directory used for the import with a length
   * limit of 2,000 characters. Can be specified if one wants to have the
   * AlloyDB export to a specific Cloud Storage directory. Ensure that the
   * AlloyDB service account has the necessary Cloud Storage Admin permissions
   * to access the specified Cloud Storage directory.
   *
   * @var string
   */
  public $gcsStagingDir;
  /**
   * Required. The AlloyDB location to copy the data from with a length limit of
   * 256 characters.
   *
   * @var string
   */
  public $locationId;
  /**
   * The project ID that contains the AlloyDB source. Has a length limit of 128
   * characters. If not specified, inherits the project ID from the parent
   * request.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. The AlloyDB table to copy the data from with a length limit of
   * 256 characters.
   *
   * @var string
   */
  public $tableId;

  /**
   * Required. The AlloyDB cluster to copy the data from with a length limit of
   * 256 characters.
   *
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * Required. The AlloyDB database to copy the data from with a length limit of
   * 256 characters.
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
   * limit of 2,000 characters. Can be specified if one wants to have the
   * AlloyDB export to a specific Cloud Storage directory. Ensure that the
   * AlloyDB service account has the necessary Cloud Storage Admin permissions
   * to access the specified Cloud Storage directory.
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
   * Required. The AlloyDB location to copy the data from with a length limit of
   * 256 characters.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * The project ID that contains the AlloyDB source. Has a length limit of 128
   * characters. If not specified, inherits the project ID from the parent
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
   * Required. The AlloyDB table to copy the data from with a length limit of
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
class_alias(GoogleCloudDiscoveryengineV1AlloyDbSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AlloyDbSource');
