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

class BigQueryDestination extends \Google\Model
{
  /**
   * Required. The BigQuery dataset in format
   * "projects/projectId/datasets/datasetId", to which the snapshot result
   * should be exported. If this dataset does not exist, the export call returns
   * an INVALID_ARGUMENT error. Setting the `contentType` for `exportAssets`
   * determines the [schema](/asset-inventory/docs/exporting-to-
   * bigquery#bigquery-schema) of the BigQuery table. Setting
   * `separateTablesPerAssetType` to `TRUE` also influences the schema.
   *
   * @var string
   */
  public $dataset;
  /**
   * If the destination table already exists and this flag is `TRUE`, the table
   * will be overwritten by the contents of assets snapshot. If the flag is
   * `FALSE` or unset and the destination table already exists, the export call
   * returns an INVALID_ARGUMENT error.
   *
   * @var bool
   */
  public $force;
  protected $partitionSpecType = PartitionSpec::class;
  protected $partitionSpecDataType = '';
  /**
   * If this flag is `TRUE`, the snapshot results will be written to one or
   * multiple tables, each of which contains results of one asset type. The
   * [force] and [partition_spec] fields will apply to each of them. Field
   * [table] will be concatenated with "_" and the asset type names (see
   * https://cloud.google.com/asset-inventory/docs/supported-asset-types for
   * supported asset types) to construct per-asset-type table names, in which
   * all non-alphanumeric characters like "." and "/" will be substituted by
   * "_". Example: if field [table] is "mytable" and snapshot results contain
   * "storage.googleapis.com/Bucket" assets, the corresponding table name will
   * be "mytable_storage_googleapis_com_Bucket". If any of these tables does not
   * exist, a new table with the concatenated name will be created. When
   * [content_type] in the ExportAssetsRequest is `RESOURCE`, the schema of each
   * table will include RECORD-type columns mapped to the nested fields in the
   * Asset.resource.data field of that asset type (up to the 15 nested level
   * BigQuery supports (https://cloud.google.com/bigquery/docs/nested-
   * repeated#limitations)). The fields in >15 nested levels will be stored in
   * JSON format string as a child column of its parent RECORD column. If error
   * occurs when exporting to any table, the whole export call will return an
   * error but the export results that already succeed will persist. Example: if
   * exporting to table_type_A succeeds when exporting to table_type_B fails
   * during one export call, the results in table_type_A will persist and there
   * will not be partial results persisting in a table.
   *
   * @var bool
   */
  public $separateTablesPerAssetType;
  /**
   * Required. The BigQuery table to which the snapshot result should be
   * written. If this table does not exist, a new table with the given name will
   * be created.
   *
   * @var string
   */
  public $table;

  /**
   * Required. The BigQuery dataset in format
   * "projects/projectId/datasets/datasetId", to which the snapshot result
   * should be exported. If this dataset does not exist, the export call returns
   * an INVALID_ARGUMENT error. Setting the `contentType` for `exportAssets`
   * determines the [schema](/asset-inventory/docs/exporting-to-
   * bigquery#bigquery-schema) of the BigQuery table. Setting
   * `separateTablesPerAssetType` to `TRUE` also influences the schema.
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
   * If the destination table already exists and this flag is `TRUE`, the table
   * will be overwritten by the contents of assets snapshot. If the flag is
   * `FALSE` or unset and the destination table already exists, the export call
   * returns an INVALID_ARGUMENT error.
   *
   * @param bool $force
   */
  public function setForce($force)
  {
    $this->force = $force;
  }
  /**
   * @return bool
   */
  public function getForce()
  {
    return $this->force;
  }
  /**
   * [partition_spec] determines whether to export to partitioned table(s) and
   * how to partition the data. If [partition_spec] is unset or
   * [partition_spec.partition_key] is unset or `PARTITION_KEY_UNSPECIFIED`, the
   * snapshot results will be exported to non-partitioned table(s). [force] will
   * decide whether to overwrite existing table(s). If [partition_spec] is
   * specified. First, the snapshot results will be written to partitioned
   * table(s) with two additional timestamp columns, readTime and requestTime,
   * one of which will be the partition key. Secondly, in the case when any
   * destination table already exists, it will first try to update existing
   * table's schema as necessary by appending additional columns. Then, if
   * [force] is `TRUE`, the corresponding partition will be overwritten by the
   * snapshot results (data in different partitions will remain intact); if
   * [force] is unset or `FALSE`, it will append the data. An error will be
   * returned if the schema update or data appension fails.
   *
   * @param PartitionSpec $partitionSpec
   */
  public function setPartitionSpec(PartitionSpec $partitionSpec)
  {
    $this->partitionSpec = $partitionSpec;
  }
  /**
   * @return PartitionSpec
   */
  public function getPartitionSpec()
  {
    return $this->partitionSpec;
  }
  /**
   * If this flag is `TRUE`, the snapshot results will be written to one or
   * multiple tables, each of which contains results of one asset type. The
   * [force] and [partition_spec] fields will apply to each of them. Field
   * [table] will be concatenated with "_" and the asset type names (see
   * https://cloud.google.com/asset-inventory/docs/supported-asset-types for
   * supported asset types) to construct per-asset-type table names, in which
   * all non-alphanumeric characters like "." and "/" will be substituted by
   * "_". Example: if field [table] is "mytable" and snapshot results contain
   * "storage.googleapis.com/Bucket" assets, the corresponding table name will
   * be "mytable_storage_googleapis_com_Bucket". If any of these tables does not
   * exist, a new table with the concatenated name will be created. When
   * [content_type] in the ExportAssetsRequest is `RESOURCE`, the schema of each
   * table will include RECORD-type columns mapped to the nested fields in the
   * Asset.resource.data field of that asset type (up to the 15 nested level
   * BigQuery supports (https://cloud.google.com/bigquery/docs/nested-
   * repeated#limitations)). The fields in >15 nested levels will be stored in
   * JSON format string as a child column of its parent RECORD column. If error
   * occurs when exporting to any table, the whole export call will return an
   * error but the export results that already succeed will persist. Example: if
   * exporting to table_type_A succeeds when exporting to table_type_B fails
   * during one export call, the results in table_type_A will persist and there
   * will not be partial results persisting in a table.
   *
   * @param bool $separateTablesPerAssetType
   */
  public function setSeparateTablesPerAssetType($separateTablesPerAssetType)
  {
    $this->separateTablesPerAssetType = $separateTablesPerAssetType;
  }
  /**
   * @return bool
   */
  public function getSeparateTablesPerAssetType()
  {
    return $this->separateTablesPerAssetType;
  }
  /**
   * Required. The BigQuery table to which the snapshot result should be
   * written. If this table does not exist, a new table with the given name will
   * be created.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigQueryDestination::class, 'Google_Service_CloudAsset_BigQueryDestination');
