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

namespace Google\Service\CloudAsset;

class GoogleCloudAssetV1QueryAssetsOutputConfigBigQueryDestination extends \Google\Model
{
  /**
   * Required. The BigQuery dataset where the query results will be saved. It
   * has the format of "projects/{projectId}/datasets/{datasetId}".
   *
   * @var string
   */
  public $dataset;
  /**
   * Required. The BigQuery table where the query results will be saved. If this
   * table does not exist, a new table with the given name will be created.
   *
   * @var string
   */
  public $table;
  /**
   * Specifies the action that occurs if the destination table or partition
   * already exists. The following values are supported: * WRITE_TRUNCATE: If
   * the table or partition already exists, BigQuery overwrites the entire table
   * or all the partitions data. * WRITE_APPEND: If the table or partition
   * already exists, BigQuery appends the data to the table or the latest
   * partition. * WRITE_EMPTY: If the table already exists and contains data, a
   * 'duplicate' error is returned in the job result. The default value is
   * WRITE_EMPTY.
   *
   * @var string
   */
  public $writeDisposition;

  /**
   * Required. The BigQuery dataset where the query results will be saved. It
   * has the format of "projects/{projectId}/datasets/{datasetId}".
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * Required. The BigQuery table where the query results will be saved. If this
   * table does not exist, a new table with the given name will be created.
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * Specifies the action that occurs if the destination table or partition
   * already exists. The following values are supported: * WRITE_TRUNCATE: If
   * the table or partition already exists, BigQuery overwrites the entire table
   * or all the partitions data. * WRITE_APPEND: If the table or partition
   * already exists, BigQuery appends the data to the table or the latest
   * partition. * WRITE_EMPTY: If the table already exists and contains data, a
   * 'duplicate' error is returned in the job result. The default value is
   * WRITE_EMPTY.
   *
   * @param string $writeDisposition
   */
  public function setWriteDisposition($writeDisposition)
  {
    $this->writeDisposition = $writeDisposition;
  }
  /**
   * @return string
   */
  public function getWriteDisposition()
  {
    return $this->writeDisposition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssetV1QueryAssetsOutputConfigBigQueryDestination::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1QueryAssetsOutputConfigBigQueryDestination');
