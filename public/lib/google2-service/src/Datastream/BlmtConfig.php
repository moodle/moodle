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

namespace Google\Service\Datastream;

class BlmtConfig extends \Google\Model
{
  /**
   * Default value.
   */
  public const FILE_FORMAT_FILE_FORMAT_UNSPECIFIED = 'FILE_FORMAT_UNSPECIFIED';
  /**
   * Parquet file format.
   */
  public const FILE_FORMAT_PARQUET = 'PARQUET';
  /**
   * Default value.
   */
  public const TABLE_FORMAT_TABLE_FORMAT_UNSPECIFIED = 'TABLE_FORMAT_UNSPECIFIED';
  /**
   * Iceberg table format.
   */
  public const TABLE_FORMAT_ICEBERG = 'ICEBERG';
  /**
   * Required. The Cloud Storage bucket name.
   *
   * @var string
   */
  public $bucket;
  /**
   * Required. The bigquery connection. Format: `{project}.{location}.{name}`
   *
   * @var string
   */
  public $connectionName;
  /**
   * Required. The file format.
   *
   * @var string
   */
  public $fileFormat;
  /**
   * The root path inside the Cloud Storage bucket.
   *
   * @var string
   */
  public $rootPath;
  /**
   * Required. The table format.
   *
   * @var string
   */
  public $tableFormat;

  /**
   * Required. The Cloud Storage bucket name.
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Required. The bigquery connection. Format: `{project}.{location}.{name}`
   *
   * @param string $connectionName
   */
  public function setConnectionName($connectionName)
  {
    $this->connectionName = $connectionName;
  }
  /**
   * @return string
   */
  public function getConnectionName()
  {
    return $this->connectionName;
  }
  /**
   * Required. The file format.
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
   * The root path inside the Cloud Storage bucket.
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
   * Required. The table format.
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
class_alias(BlmtConfig::class, 'Google_Service_Datastream_BlmtConfig');
