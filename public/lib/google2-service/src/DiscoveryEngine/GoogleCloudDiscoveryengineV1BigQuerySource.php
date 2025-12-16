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

class GoogleCloudDiscoveryengineV1BigQuerySource extends \Google\Model
{
  /**
   * The schema to use when parsing the data from the source. Supported values
   * for user event imports: * `user_event` (default): One UserEvent per row.
   * Supported values for document imports: * `document` (default): One Document
   * format per row. Each document must have a valid Document.id and one of
   * Document.json_data or Document.struct_data. * `custom`: One custom data per
   * row in arbitrary format that conforms to the defined Schema of the data
   * store. This can only be used by the GENERIC Data Store vertical.
   *
   * @var string
   */
  public $dataSchema;
  /**
   * Required. The BigQuery data set to copy the data from with a length limit
   * of 1,024 characters.
   *
   * @var string
   */
  public $datasetId;
  /**
   * Intermediate Cloud Storage directory used for the import with a length
   * limit of 2,000 characters. Can be specified if one wants to have the
   * BigQuery export to a specific Cloud Storage directory.
   *
   * @var string
   */
  public $gcsStagingDir;
  protected $partitionDateType = GoogleTypeDate::class;
  protected $partitionDateDataType = '';
  /**
   * The project ID or the project number that contains the BigQuery source. Has
   * a length limit of 128 characters. If not specified, inherits the project ID
   * from the parent request.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. The BigQuery table to copy the data from with a length limit of
   * 1,024 characters.
   *
   * @var string
   */
  public $tableId;

  /**
   * The schema to use when parsing the data from the source. Supported values
   * for user event imports: * `user_event` (default): One UserEvent per row.
   * Supported values for document imports: * `document` (default): One Document
   * format per row. Each document must have a valid Document.id and one of
   * Document.json_data or Document.struct_data. * `custom`: One custom data per
   * row in arbitrary format that conforms to the defined Schema of the data
   * store. This can only be used by the GENERIC Data Store vertical.
   *
   * @param string $dataSchema
   */
  public function setDataSchema($dataSchema)
  {
    $this->dataSchema = $dataSchema;
  }
  /**
   * @return string
   */
  public function getDataSchema()
  {
    return $this->dataSchema;
  }
  /**
   * Required. The BigQuery data set to copy the data from with a length limit
   * of 1,024 characters.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * Intermediate Cloud Storage directory used for the import with a length
   * limit of 2,000 characters. Can be specified if one wants to have the
   * BigQuery export to a specific Cloud Storage directory.
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
   * BigQuery time partitioned table's _PARTITIONDATE in YYYY-MM-DD format.
   *
   * @param GoogleTypeDate $partitionDate
   */
  public function setPartitionDate(GoogleTypeDate $partitionDate)
  {
    $this->partitionDate = $partitionDate;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getPartitionDate()
  {
    return $this->partitionDate;
  }
  /**
   * The project ID or the project number that contains the BigQuery source. Has
   * a length limit of 128 characters. If not specified, inherits the project ID
   * from the parent request.
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
   * Required. The BigQuery table to copy the data from with a length limit of
   * 1,024 characters.
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
class_alias(GoogleCloudDiscoveryengineV1BigQuerySource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1BigQuerySource');
