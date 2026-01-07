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

namespace Google\Service\CloudHealthcare;

class GoogleCloudHealthcareV1FhirBigQueryDestination extends \Google\Model
{
  /**
   * Default behavior is the same as WRITE_EMPTY.
   */
  public const WRITE_DISPOSITION_WRITE_DISPOSITION_UNSPECIFIED = 'WRITE_DISPOSITION_UNSPECIFIED';
  /**
   * Only export data if the destination tables are empty.
   */
  public const WRITE_DISPOSITION_WRITE_EMPTY = 'WRITE_EMPTY';
  /**
   * Erase all existing data in the destination tables before writing the FHIR
   * resources.
   */
  public const WRITE_DISPOSITION_WRITE_TRUNCATE = 'WRITE_TRUNCATE';
  /**
   * Append data to the destination tables.
   */
  public const WRITE_DISPOSITION_WRITE_APPEND = 'WRITE_APPEND';
  /**
   * Optional. BigQuery URI to an existing dataset, up to 2000 characters long,
   * in the format `bq://projectId.bqDatasetId`.
   *
   * @var string
   */
  public $datasetUri;
  /**
   * Optional. The default value is false. If this flag is `TRUE`, all tables
   * are deleted from the dataset before the new exported tables are written. If
   * the flag is not set and the destination dataset contains tables, the export
   * call returns an error. If `write_disposition` is specified, this parameter
   * is ignored. force=false is equivalent to write_disposition=WRITE_EMPTY and
   * force=true is equivalent to write_disposition=WRITE_TRUNCATE.
   *
   * @var bool
   */
  public $force;
  protected $schemaConfigType = SchemaConfig::class;
  protected $schemaConfigDataType = '';
  /**
   * Optional. Determines if existing data in the destination dataset is
   * overwritten, appended to, or not written if the tables contain data. If a
   * write_disposition is specified, the `force` parameter is ignored.
   *
   * @var string
   */
  public $writeDisposition;

  /**
   * Optional. BigQuery URI to an existing dataset, up to 2000 characters long,
   * in the format `bq://projectId.bqDatasetId`.
   *
   * @param string $datasetUri
   */
  public function setDatasetUri($datasetUri)
  {
    $this->datasetUri = $datasetUri;
  }
  /**
   * @return string
   */
  public function getDatasetUri()
  {
    return $this->datasetUri;
  }
  /**
   * Optional. The default value is false. If this flag is `TRUE`, all tables
   * are deleted from the dataset before the new exported tables are written. If
   * the flag is not set and the destination dataset contains tables, the export
   * call returns an error. If `write_disposition` is specified, this parameter
   * is ignored. force=false is equivalent to write_disposition=WRITE_EMPTY and
   * force=true is equivalent to write_disposition=WRITE_TRUNCATE.
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
   * Optional. The configuration for the exported BigQuery schema.
   *
   * @param SchemaConfig $schemaConfig
   */
  public function setSchemaConfig(SchemaConfig $schemaConfig)
  {
    $this->schemaConfig = $schemaConfig;
  }
  /**
   * @return SchemaConfig
   */
  public function getSchemaConfig()
  {
    return $this->schemaConfig;
  }
  /**
   * Optional. Determines if existing data in the destination dataset is
   * overwritten, appended to, or not written if the tables contain data. If a
   * write_disposition is specified, the `force` parameter is ignored.
   *
   * Accepted values: WRITE_DISPOSITION_UNSPECIFIED, WRITE_EMPTY,
   * WRITE_TRUNCATE, WRITE_APPEND
   *
   * @param self::WRITE_DISPOSITION_* $writeDisposition
   */
  public function setWriteDisposition($writeDisposition)
  {
    $this->writeDisposition = $writeDisposition;
  }
  /**
   * @return self::WRITE_DISPOSITION_*
   */
  public function getWriteDisposition()
  {
    return $this->writeDisposition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudHealthcareV1FhirBigQueryDestination::class, 'Google_Service_CloudHealthcare_GoogleCloudHealthcareV1FhirBigQueryDestination');
