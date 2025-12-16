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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Container extends \Google\Model
{
  /**
   * A string representation of the full container name. Examples: - BigQuery:
   * 'Project:DataSetId.TableId' - Cloud Storage:
   * 'gs://Bucket/folders/filename.txt'
   *
   * @var string
   */
  public $fullPath;
  /**
   * Project where the finding was found. Can be different from the project that
   * owns the finding.
   *
   * @var string
   */
  public $projectId;
  /**
   * The rest of the path after the root. Examples: - For BigQuery table
   * `project_id:dataset_id.table_id`, the relative path is `table_id` - For
   * Cloud Storage file `gs://bucket/folder/filename.txt`, the relative path is
   * `folder/filename.txt`
   *
   * @var string
   */
  public $relativePath;
  /**
   * The root of the container. Examples: - For BigQuery table
   * `project_id:dataset_id.table_id`, the root is `dataset_id` - For Cloud
   * Storage file `gs://bucket/folder/filename.txt`, the root is `gs://bucket`
   *
   * @var string
   */
  public $rootPath;
  /**
   * Container type, for example BigQuery or Cloud Storage.
   *
   * @var string
   */
  public $type;
  /**
   * Findings container modification timestamp, if applicable. For Cloud
   * Storage, this field contains the last file modification timestamp. For a
   * BigQuery table, this field contains the last_modified_time property. For
   * Datastore, this field isn't populated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Findings container version, if available ("generation" for Cloud Storage).
   *
   * @var string
   */
  public $version;

  /**
   * A string representation of the full container name. Examples: - BigQuery:
   * 'Project:DataSetId.TableId' - Cloud Storage:
   * 'gs://Bucket/folders/filename.txt'
   *
   * @param string $fullPath
   */
  public function setFullPath($fullPath)
  {
    $this->fullPath = $fullPath;
  }
  /**
   * @return string
   */
  public function getFullPath()
  {
    return $this->fullPath;
  }
  /**
   * Project where the finding was found. Can be different from the project that
   * owns the finding.
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
   * The rest of the path after the root. Examples: - For BigQuery table
   * `project_id:dataset_id.table_id`, the relative path is `table_id` - For
   * Cloud Storage file `gs://bucket/folder/filename.txt`, the relative path is
   * `folder/filename.txt`
   *
   * @param string $relativePath
   */
  public function setRelativePath($relativePath)
  {
    $this->relativePath = $relativePath;
  }
  /**
   * @return string
   */
  public function getRelativePath()
  {
    return $this->relativePath;
  }
  /**
   * The root of the container. Examples: - For BigQuery table
   * `project_id:dataset_id.table_id`, the root is `dataset_id` - For Cloud
   * Storage file `gs://bucket/folder/filename.txt`, the root is `gs://bucket`
   *
   * @param string $rootPath
   */
  public function setRootPath($rootPath)
  {
    $this->rootPath = $rootPath;
  }
  /**
   * @return string
   */
  public function getRootPath()
  {
    return $this->rootPath;
  }
  /**
   * Container type, for example BigQuery or Cloud Storage.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Findings container modification timestamp, if applicable. For Cloud
   * Storage, this field contains the last file modification timestamp. For a
   * BigQuery table, this field contains the last_modified_time property. For
   * Datastore, this field isn't populated.
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
  /**
   * Findings container version, if available ("generation" for Cloud Storage).
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Container::class, 'Google_Service_DLP_GooglePrivacyDlpV2Container');
