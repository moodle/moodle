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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainExportInsightsDataRequest extends \Google\Model
{
  /**
   * Unspecified. Defaults to EXPORT_V3.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_SCHEMA_VERSION_UNSPECIFIED = 'EXPORT_SCHEMA_VERSION_UNSPECIFIED';
  /**
   * Export schema version 1.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V1 = 'EXPORT_V1';
  /**
   * Export schema version 2.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V2 = 'EXPORT_V2';
  /**
   * Export schema version 3.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V3 = 'EXPORT_V3';
  /**
   * Export schema version 4.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V4 = 'EXPORT_V4';
  /**
   * Export schema version 5.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V5 = 'EXPORT_V5';
  /**
   * Export schema version 6.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V6 = 'EXPORT_V6';
  /**
   * Export schema version 7.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V7 = 'EXPORT_V7';
  /**
   * Export schema version 8.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V8 = 'EXPORT_V8';
  /**
   * Export schema version 9.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V9 = 'EXPORT_V9';
  /**
   * Export schema version 10.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V10 = 'EXPORT_V10';
  /**
   * Export schema version 11.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V11 = 'EXPORT_V11';
  /**
   * Export schema version 12.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V12 = 'EXPORT_V12';
  /**
   * Export schema version 13.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V13 = 'EXPORT_V13';
  /**
   * Export schema version 14.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_V14 = 'EXPORT_V14';
  /**
   * Export schema version latest available.
   */
  public const EXPORT_SCHEMA_VERSION_EXPORT_VERSION_LATEST_AVAILABLE = 'EXPORT_VERSION_LATEST_AVAILABLE';
  /**
   * Write disposition is not specified. Defaults to WRITE_TRUNCATE.
   */
  public const WRITE_DISPOSITION_WRITE_DISPOSITION_UNSPECIFIED = 'WRITE_DISPOSITION_UNSPECIFIED';
  /**
   * If the table already exists, BigQuery will overwrite the table data and use
   * the schema from the load.
   */
  public const WRITE_DISPOSITION_WRITE_TRUNCATE = 'WRITE_TRUNCATE';
  /**
   * If the table already exists, BigQuery will append data to the table.
   */
  public const WRITE_DISPOSITION_WRITE_APPEND = 'WRITE_APPEND';
  protected $bigQueryDestinationType = GoogleCloudContactcenterinsightsV1mainExportInsightsDataRequestBigQueryDestination::class;
  protected $bigQueryDestinationDataType = '';
  /**
   * Optional. Version of the export schema.
   *
   * @var string
   */
  public $exportSchemaVersion;
  /**
   * A filter to reduce results to a specific subset. Useful for exporting
   * conversations with specific properties.
   *
   * @var string
   */
  public $filter;
  /**
   * A fully qualified KMS key name for BigQuery tables protected by CMEK.
   * Format: projects/{project}/locations/{location}/keyRings/{keyring}/cryptoKe
   * ys/{key}/cryptoKeyVersions/{version}
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Required. The parent resource to export data from.
   *
   * @var string
   */
  public $parent;
  /**
   * Options for what to do if the destination table already exists.
   *
   * @var string
   */
  public $writeDisposition;

  /**
   * Specified if sink is a BigQuery table.
   *
   * @param GoogleCloudContactcenterinsightsV1mainExportInsightsDataRequestBigQueryDestination $bigQueryDestination
   */
  public function setBigQueryDestination(GoogleCloudContactcenterinsightsV1mainExportInsightsDataRequestBigQueryDestination $bigQueryDestination)
  {
    $this->bigQueryDestination = $bigQueryDestination;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainExportInsightsDataRequestBigQueryDestination
   */
  public function getBigQueryDestination()
  {
    return $this->bigQueryDestination;
  }
  /**
   * Optional. Version of the export schema.
   *
   * Accepted values: EXPORT_SCHEMA_VERSION_UNSPECIFIED, EXPORT_V1, EXPORT_V2,
   * EXPORT_V3, EXPORT_V4, EXPORT_V5, EXPORT_V6, EXPORT_V7, EXPORT_V8,
   * EXPORT_V9, EXPORT_V10, EXPORT_V11, EXPORT_V12, EXPORT_V13, EXPORT_V14,
   * EXPORT_VERSION_LATEST_AVAILABLE
   *
   * @param self::EXPORT_SCHEMA_VERSION_* $exportSchemaVersion
   */
  public function setExportSchemaVersion($exportSchemaVersion)
  {
    $this->exportSchemaVersion = $exportSchemaVersion;
  }
  /**
   * @return self::EXPORT_SCHEMA_VERSION_*
   */
  public function getExportSchemaVersion()
  {
    return $this->exportSchemaVersion;
  }
  /**
   * A filter to reduce results to a specific subset. Useful for exporting
   * conversations with specific properties.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * A fully qualified KMS key name for BigQuery tables protected by CMEK.
   * Format: projects/{project}/locations/{location}/keyRings/{keyring}/cryptoKe
   * ys/{key}/cryptoKeyVersions/{version}
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Required. The parent resource to export data from.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Options for what to do if the destination table already exists.
   *
   * Accepted values: WRITE_DISPOSITION_UNSPECIFIED, WRITE_TRUNCATE,
   * WRITE_APPEND
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
class_alias(GoogleCloudContactcenterinsightsV1mainExportInsightsDataRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainExportInsightsDataRequest');
