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

namespace Google\Service\SQLAdmin;

class ExportContext extends \Google\Collection
{
  /**
   * Unknown file type.
   */
  public const FILE_TYPE_SQL_FILE_TYPE_UNSPECIFIED = 'SQL_FILE_TYPE_UNSPECIFIED';
  /**
   * File containing SQL statements.
   */
  public const FILE_TYPE_SQL = 'SQL';
  /**
   * File in CSV format.
   */
  public const FILE_TYPE_CSV = 'CSV';
  public const FILE_TYPE_BAK = 'BAK';
  /**
   * TDE certificate.
   */
  public const FILE_TYPE_TDE = 'TDE';
  protected $collection_key = 'databases';
  protected $bakExportOptionsType = ExportContextBakExportOptions::class;
  protected $bakExportOptionsDataType = '';
  protected $csvExportOptionsType = ExportContextCsvExportOptions::class;
  protected $csvExportOptionsDataType = '';
  /**
   * Databases to be exported. `MySQL instances:` If `fileType` is `SQL` and no
   * database is specified, all databases are exported, except for the `mysql`
   * system database. If `fileType` is `CSV`, you can specify one database,
   * either by using this property or by using the
   * `csvExportOptions.selectQuery` property, which takes precedence over this
   * property. `PostgreSQL instances:` If you don't specify a database by name,
   * all user databases in the instance are exported. This excludes system
   * databases and Cloud SQL databases used to manage internal operations.
   * Exporting all user databases is only available for directory-formatted
   * parallel export. If `fileType` is `CSV`, this database must match the one
   * specified in the `csvExportOptions.selectQuery` property. `SQL Server
   * instances:` You must specify one database to be exported, and the
   * `fileType` must be `BAK`.
   *
   * @var string[]
   */
  public $databases;
  /**
   * The file type for the specified uri.
   *
   * @var string
   */
  public $fileType;
  /**
   * This is always `sql#exportContext`.
   *
   * @var string
   */
  public $kind;
  /**
   * Whether to perform a serverless export.
   *
   * @var bool
   */
  public $offload;
  protected $sqlExportOptionsType = ExportContextSqlExportOptions::class;
  protected $sqlExportOptionsDataType = '';
  protected $tdeExportOptionsType = ExportContextTdeExportOptions::class;
  protected $tdeExportOptionsDataType = '';
  /**
   * The path to the file in Google Cloud Storage where the export will be
   * stored. The URI is in the form `gs://bucketName/fileName`. If the file
   * already exists, the request succeeds, but the operation fails. If
   * `fileType` is `SQL` and the filename ends with .gz, the contents are
   * compressed.
   *
   * @var string
   */
  public $uri;

  /**
   * Options for exporting BAK files (SQL Server-only)
   *
   * @param ExportContextBakExportOptions $bakExportOptions
   */
  public function setBakExportOptions(ExportContextBakExportOptions $bakExportOptions)
  {
    $this->bakExportOptions = $bakExportOptions;
  }
  /**
   * @return ExportContextBakExportOptions
   */
  public function getBakExportOptions()
  {
    return $this->bakExportOptions;
  }
  /**
   * Options for exporting data as CSV. `MySQL` and `PostgreSQL` instances only.
   *
   * @param ExportContextCsvExportOptions $csvExportOptions
   */
  public function setCsvExportOptions(ExportContextCsvExportOptions $csvExportOptions)
  {
    $this->csvExportOptions = $csvExportOptions;
  }
  /**
   * @return ExportContextCsvExportOptions
   */
  public function getCsvExportOptions()
  {
    return $this->csvExportOptions;
  }
  /**
   * Databases to be exported. `MySQL instances:` If `fileType` is `SQL` and no
   * database is specified, all databases are exported, except for the `mysql`
   * system database. If `fileType` is `CSV`, you can specify one database,
   * either by using this property or by using the
   * `csvExportOptions.selectQuery` property, which takes precedence over this
   * property. `PostgreSQL instances:` If you don't specify a database by name,
   * all user databases in the instance are exported. This excludes system
   * databases and Cloud SQL databases used to manage internal operations.
   * Exporting all user databases is only available for directory-formatted
   * parallel export. If `fileType` is `CSV`, this database must match the one
   * specified in the `csvExportOptions.selectQuery` property. `SQL Server
   * instances:` You must specify one database to be exported, and the
   * `fileType` must be `BAK`.
   *
   * @param string[] $databases
   */
  public function setDatabases($databases)
  {
    $this->databases = $databases;
  }
  /**
   * @return string[]
   */
  public function getDatabases()
  {
    return $this->databases;
  }
  /**
   * The file type for the specified uri.
   *
   * Accepted values: SQL_FILE_TYPE_UNSPECIFIED, SQL, CSV, BAK, TDE
   *
   * @param self::FILE_TYPE_* $fileType
   */
  public function setFileType($fileType)
  {
    $this->fileType = $fileType;
  }
  /**
   * @return self::FILE_TYPE_*
   */
  public function getFileType()
  {
    return $this->fileType;
  }
  /**
   * This is always `sql#exportContext`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Whether to perform a serverless export.
   *
   * @param bool $offload
   */
  public function setOffload($offload)
  {
    $this->offload = $offload;
  }
  /**
   * @return bool
   */
  public function getOffload()
  {
    return $this->offload;
  }
  /**
   * Options for exporting data as SQL statements.
   *
   * @param ExportContextSqlExportOptions $sqlExportOptions
   */
  public function setSqlExportOptions(ExportContextSqlExportOptions $sqlExportOptions)
  {
    $this->sqlExportOptions = $sqlExportOptions;
  }
  /**
   * @return ExportContextSqlExportOptions
   */
  public function getSqlExportOptions()
  {
    return $this->sqlExportOptions;
  }
  /**
   * Optional. Export parameters specific to SQL Server TDE certificates
   *
   * @param ExportContextTdeExportOptions $tdeExportOptions
   */
  public function setTdeExportOptions(ExportContextTdeExportOptions $tdeExportOptions)
  {
    $this->tdeExportOptions = $tdeExportOptions;
  }
  /**
   * @return ExportContextTdeExportOptions
   */
  public function getTdeExportOptions()
  {
    return $this->tdeExportOptions;
  }
  /**
   * The path to the file in Google Cloud Storage where the export will be
   * stored. The URI is in the form `gs://bucketName/fileName`. If the file
   * already exists, the request succeeds, but the operation fails. If
   * `fileType` is `SQL` and the filename ends with .gz, the contents are
   * compressed.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportContext::class, 'Google_Service_SQLAdmin_ExportContext');
