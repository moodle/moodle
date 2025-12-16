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

class GoogleCloudAssetV1BigQueryDestination extends \Google\Model
{
  /**
   * Unspecified partition key. Tables won't be partitioned using this option.
   */
  public const PARTITION_KEY_PARTITION_KEY_UNSPECIFIED = 'PARTITION_KEY_UNSPECIFIED';
  /**
   * The time when the request is received. If specified as partition key, the
   * result table(s) is partitioned by the RequestTime column, an additional
   * timestamp column representing when the request was received.
   */
  public const PARTITION_KEY_REQUEST_TIME = 'REQUEST_TIME';
  /**
   * Required. The BigQuery dataset in format
   * "projects/projectId/datasets/datasetId", to which the analysis results
   * should be exported. If this dataset does not exist, the export call will
   * return an INVALID_ARGUMENT error.
   *
   * @var string
   */
  public $dataset;
  /**
   * The partition key for BigQuery partitioned table.
   *
   * @var string
   */
  public $partitionKey;
  /**
   * Required. The prefix of the BigQuery tables to which the analysis results
   * will be written. Tables will be created based on this table_prefix if not
   * exist: * _analysis table will contain export operation's metadata. *
   * _analysis_result will contain all the IamPolicyAnalysisResult. When
   * [partition_key] is specified, both tables will be partitioned based on the
   * [partition_key].
   *
   * @var string
   */
  public $tablePrefix;
  /**
   * Optional. Specifies the action that occurs if the destination table or
   * partition already exists. The following values are supported: *
   * WRITE_TRUNCATE: If the table or partition already exists, BigQuery
   * overwrites the entire table or all the partitions data. * WRITE_APPEND: If
   * the table or partition already exists, BigQuery appends the data to the
   * table or the latest partition. * WRITE_EMPTY: If the table already exists
   * and contains data, an error is returned. The default value is WRITE_APPEND.
   * Each action is atomic and only occurs if BigQuery is able to complete the
   * job successfully. Details are at
   * https://cloud.google.com/bigquery/docs/loading-data-
   * local#appending_to_or_overwriting_a_table_using_a_local_file.
   *
   * @var string
   */
  public $writeDisposition;

  /**
   * Required. The BigQuery dataset in format
   * "projects/projectId/datasets/datasetId", to which the analysis results
   * should be exported. If this dataset does not exist, the export call will
   * return an INVALID_ARGUMENT error.
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
   * The partition key for BigQuery partitioned table.
   *
   * Accepted values: PARTITION_KEY_UNSPECIFIED, REQUEST_TIME
   *
   * @param self::PARTITION_KEY_* $partitionKey
   */
  public function setPartitionKey($partitionKey)
  {
    $this->partitionKey = $partitionKey;
  }
  /**
   * @return self::PARTITION_KEY_*
   */
  public function getPartitionKey()
  {
    return $this->partitionKey;
  }
  /**
   * Required. The prefix of the BigQuery tables to which the analysis results
   * will be written. Tables will be created based on this table_prefix if not
   * exist: * _analysis table will contain export operation's metadata. *
   * _analysis_result will contain all the IamPolicyAnalysisResult. When
   * [partition_key] is specified, both tables will be partitioned based on the
   * [partition_key].
   *
   * @param string $tablePrefix
   */
  public function setTablePrefix($tablePrefix)
  {
    $this->tablePrefix = $tablePrefix;
  }
  /**
   * @return string
   */
  public function getTablePrefix()
  {
    return $this->tablePrefix;
  }
  /**
   * Optional. Specifies the action that occurs if the destination table or
   * partition already exists. The following values are supported: *
   * WRITE_TRUNCATE: If the table or partition already exists, BigQuery
   * overwrites the entire table or all the partitions data. * WRITE_APPEND: If
   * the table or partition already exists, BigQuery appends the data to the
   * table or the latest partition. * WRITE_EMPTY: If the table already exists
   * and contains data, an error is returned. The default value is WRITE_APPEND.
   * Each action is atomic and only occurs if BigQuery is able to complete the
   * job successfully. Details are at
   * https://cloud.google.com/bigquery/docs/loading-data-
   * local#appending_to_or_overwriting_a_table_using_a_local_file.
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
class_alias(GoogleCloudAssetV1BigQueryDestination::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1BigQueryDestination');
