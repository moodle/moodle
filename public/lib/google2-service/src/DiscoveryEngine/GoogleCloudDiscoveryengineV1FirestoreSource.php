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

class GoogleCloudDiscoveryengineV1FirestoreSource extends \Google\Model
{
  /**
   * Required. The Firestore collection (or entity) to copy the data from with a
   * length limit of 1,500 characters.
   *
   * @var string
   */
  public $collectionId;
  /**
   * Required. The Firestore database to copy the data from with a length limit
   * of 256 characters.
   *
   * @var string
   */
  public $databaseId;
  /**
   * Intermediate Cloud Storage directory used for the import with a length
   * limit of 2,000 characters. Can be specified if one wants to have the
   * Firestore export to a specific Cloud Storage directory. Ensure that the
   * Firestore service account has the necessary Cloud Storage Admin permissions
   * to access the specified Cloud Storage directory.
   *
   * @var string
   */
  public $gcsStagingDir;
  /**
   * The project ID that the Cloud SQL source is in with a length limit of 128
   * characters. If not specified, inherits the project ID from the parent
   * request.
   *
   * @var string
   */
  public $projectId;

  /**
   * Required. The Firestore collection (or entity) to copy the data from with a
   * length limit of 1,500 characters.
   *
   * @param string $collectionId
   */
  public function setCollectionId($collectionId)
  {
    $this->collectionId = $collectionId;
  }
  /**
   * @return string
   */
  public function getCollectionId()
  {
    return $this->collectionId;
  }
  /**
   * Required. The Firestore database to copy the data from with a length limit
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
   * limit of 2,000 characters. Can be specified if one wants to have the
   * Firestore export to a specific Cloud Storage directory. Ensure that the
   * Firestore service account has the necessary Cloud Storage Admin permissions
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
   * The project ID that the Cloud SQL source is in with a length limit of 128
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1FirestoreSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1FirestoreSource');
