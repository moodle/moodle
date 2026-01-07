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

namespace Google\Service\Bigquery;

class BigLakeConfiguration extends \Google\Model
{
  /**
   * Default Value.
   */
  public const FILE_FORMAT_FILE_FORMAT_UNSPECIFIED = 'FILE_FORMAT_UNSPECIFIED';
  /**
   * Apache Parquet format.
   */
  public const FILE_FORMAT_PARQUET = 'PARQUET';
  /**
   * Default Value.
   */
  public const TABLE_FORMAT_TABLE_FORMAT_UNSPECIFIED = 'TABLE_FORMAT_UNSPECIFIED';
  /**
   * Apache Iceberg format.
   */
  public const TABLE_FORMAT_ICEBERG = 'ICEBERG';
  /**
   * Optional. The connection specifying the credentials to be used to read and
   * write to external storage, such as Cloud Storage. The connection_id can
   * have the form `{project}.{location}.{connection_id}` or
   * `projects/{project}/locations/{location}/connections/{connection_id}".
   *
   * @var string
   */
  public $connectionId;
  /**
   * Optional. The file format the table data is stored in.
   *
   * @var string
   */
  public $fileFormat;
  /**
   * Optional. The fully qualified location prefix of the external folder where
   * table data is stored. The '*' wildcard character is not allowed. The URI
   * should be in the format `gs://bucket/path_to_table/`
   *
   * @var string
   */
  public $storageUri;
  /**
   * Optional. The table format the metadata only snapshots are stored in.
   *
   * @var string
   */
  public $tableFormat;

  /**
   * Optional. The connection specifying the credentials to be used to read and
   * write to external storage, such as Cloud Storage. The connection_id can
   * have the form `{project}.{location}.{connection_id}` or
   * `projects/{project}/locations/{location}/connections/{connection_id}".
   *
   * @param string $connectionId
   */
  public function setConnectionId($connectionId)
  {
    $this->connectionId = $connectionId;
  }
  /**
   * @return string
   */
  public function getConnectionId()
  {
    return $this->connectionId;
  }
  /**
   * Optional. The file format the table data is stored in.
   *
   * Accepted values: FILE_FORMAT_UNSPECIFIED, PARQUET
   *
   * @param self::FILE_FORMAT_* $fileFormat
   */
  public function setFileFormat($fileFormat)
  {
    $this->fileFormat = $fileFormat;
  }
  /**
   * @return self::FILE_FORMAT_*
   */
  public function getFileFormat()
  {
    return $this->fileFormat;
  }
  /**
   * Optional. The fully qualified location prefix of the external folder where
   * table data is stored. The '*' wildcard character is not allowed. The URI
   * should be in the format `gs://bucket/path_to_table/`
   *
   * @param string $storageUri
   */
  public function setStorageUri($storageUri)
  {
    $this->storageUri = $storageUri;
  }
  /**
   * @return string
   */
  public function getStorageUri()
  {
    return $this->storageUri;
  }
  /**
   * Optional. The table format the metadata only snapshots are stored in.
   *
   * Accepted values: TABLE_FORMAT_UNSPECIFIED, ICEBERG
   *
   * @param self::TABLE_FORMAT_* $tableFormat
   */
  public function setTableFormat($tableFormat)
  {
    $this->tableFormat = $tableFormat;
  }
  /**
   * @return self::TABLE_FORMAT_*
   */
  public function getTableFormat()
  {
    return $this->tableFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BigLakeConfiguration::class, 'Google_Service_Bigquery_BigLakeConfiguration');
