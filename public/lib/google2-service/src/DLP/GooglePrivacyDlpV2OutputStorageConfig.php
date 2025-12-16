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

class GooglePrivacyDlpV2OutputStorageConfig extends \Google\Model
{
  /**
   * Unused.
   */
  public const OUTPUT_SCHEMA_OUTPUT_SCHEMA_UNSPECIFIED = 'OUTPUT_SCHEMA_UNSPECIFIED';
  /**
   * Basic schema including only `info_type`, `quote`, `certainty`, and
   * `timestamp`.
   */
  public const OUTPUT_SCHEMA_BASIC_COLUMNS = 'BASIC_COLUMNS';
  /**
   * Schema tailored to findings from scanning Cloud Storage.
   */
  public const OUTPUT_SCHEMA_GCS_COLUMNS = 'GCS_COLUMNS';
  /**
   * Schema tailored to findings from scanning Google Datastore.
   */
  public const OUTPUT_SCHEMA_DATASTORE_COLUMNS = 'DATASTORE_COLUMNS';
  /**
   * Schema tailored to findings from scanning Google BigQuery.
   */
  public const OUTPUT_SCHEMA_BIG_QUERY_COLUMNS = 'BIG_QUERY_COLUMNS';
  /**
   * Schema containing all columns.
   */
  public const OUTPUT_SCHEMA_ALL_COLUMNS = 'ALL_COLUMNS';
  /**
   * Schema used for writing the findings for Inspect jobs. This field is only
   * used for Inspect and must be unspecified for Risk jobs. Columns are derived
   * from the `Finding` object. If appending to an existing table, any columns
   * from the predefined schema that are missing will be added. No columns in
   * the existing table will be deleted. If unspecified, then all available
   * columns will be used for a new table or an (existing) table with no schema,
   * and no changes will be made to an existing table that has a schema. Only
   * for use with external storage.
   *
   * @var string
   */
  public $outputSchema;
  protected $storagePathType = GooglePrivacyDlpV2CloudStoragePath::class;
  protected $storagePathDataType = '';
  protected $tableType = GooglePrivacyDlpV2BigQueryTable::class;
  protected $tableDataType = '';

  /**
   * Schema used for writing the findings for Inspect jobs. This field is only
   * used for Inspect and must be unspecified for Risk jobs. Columns are derived
   * from the `Finding` object. If appending to an existing table, any columns
   * from the predefined schema that are missing will be added. No columns in
   * the existing table will be deleted. If unspecified, then all available
   * columns will be used for a new table or an (existing) table with no schema,
   * and no changes will be made to an existing table that has a schema. Only
   * for use with external storage.
   *
   * Accepted values: OUTPUT_SCHEMA_UNSPECIFIED, BASIC_COLUMNS, GCS_COLUMNS,
   * DATASTORE_COLUMNS, BIG_QUERY_COLUMNS, ALL_COLUMNS
   *
   * @param self::OUTPUT_SCHEMA_* $outputSchema
   */
  public function setOutputSchema($outputSchema)
  {
    $this->outputSchema = $outputSchema;
  }
  /**
   * @return self::OUTPUT_SCHEMA_*
   */
  public function getOutputSchema()
  {
    return $this->outputSchema;
  }
  /**
   * Store findings in an existing Cloud Storage bucket. Files will be generated
   * with the job ID and file part number as the filename and will contain
   * findings in textproto format as SaveToGcsFindingsOutput. The filename will
   * follow the naming convention `-`. Example: `my-job-id-2`. Supported for
   * Inspect jobs. The bucket must not be the same as the bucket being
   * inspected. If storing findings to Cloud Storage, the output schema field
   * should not be set. If set, it will be ignored.
   *
   * @param GooglePrivacyDlpV2CloudStoragePath $storagePath
   */
  public function setStoragePath(GooglePrivacyDlpV2CloudStoragePath $storagePath)
  {
    $this->storagePath = $storagePath;
  }
  /**
   * @return GooglePrivacyDlpV2CloudStoragePath
   */
  public function getStoragePath()
  {
    return $this->storagePath;
  }
  /**
   * Store findings in an existing table or a new table in an existing dataset.
   * If table_id is not set a new one will be generated for you with the
   * following format: dlp_googleapis_yyyy_mm_dd_[dlp_job_id]. Pacific time zone
   * will be used for generating the date details. For Inspect, each column in
   * an existing output table must have the same name, type, and mode of a field
   * in the `Finding` object. For Risk, an existing output table should be the
   * output of a previous Risk analysis job run on the same source table, with
   * the same privacy metric and quasi-identifiers. Risk jobs that analyze the
   * same table but compute a different privacy metric, or use different sets of
   * quasi-identifiers, cannot store their results in the same table.
   *
   * @param GooglePrivacyDlpV2BigQueryTable $table
   */
  public function setTable(GooglePrivacyDlpV2BigQueryTable $table)
  {
    $this->table = $table;
  }
  /**
   * @return GooglePrivacyDlpV2BigQueryTable
   */
  public function getTable()
  {
    return $this->table;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2OutputStorageConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2OutputStorageConfig');
