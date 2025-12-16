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

class ImportContext extends \Google\Model
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
  protected $bakImportOptionsType = ImportContextBakImportOptions::class;
  protected $bakImportOptionsDataType = '';
  protected $csvImportOptionsType = ImportContextCsvImportOptions::class;
  protected $csvImportOptionsDataType = '';
  /**
   * The target database for the import. If `fileType` is `SQL`, this field is
   * required only if the import file does not specify a database, and is
   * overridden by any database specification in the import file. For entire
   * instance parallel import operations, the database is overridden by the
   * database name stored in subdirectory name. If `fileType` is `CSV`, one
   * database must be specified.
   *
   * @var string
   */
  public $database;
  /**
   * The file type for the specified uri.\`SQL`: The file contains SQL
   * statements. \`CSV`: The file contains CSV data.
   *
   * @var string
   */
  public $fileType;
  /**
   * The PostgreSQL user for this import operation. PostgreSQL instances only.
   *
   * @var string
   */
  public $importUser;
  /**
   * This is always `sql#importContext`.
   *
   * @var string
   */
  public $kind;
  protected $sqlImportOptionsType = ImportContextSqlImportOptions::class;
  protected $sqlImportOptionsDataType = '';
  protected $tdeImportOptionsType = ImportContextTdeImportOptions::class;
  protected $tdeImportOptionsDataType = '';
  /**
   * Path to the import file in Cloud Storage, in the form
   * `gs://bucketName/fileName`. Compressed gzip files (.gz) are supported when
   * `fileType` is `SQL`. The instance must have write permissions to the bucket
   * and read access to the file.
   *
   * @var string
   */
  public $uri;

  /**
   * Import parameters specific to SQL Server .BAK files
   *
   * @param ImportContextBakImportOptions $bakImportOptions
   */
  public function setBakImportOptions(ImportContextBakImportOptions $bakImportOptions)
  {
    $this->bakImportOptions = $bakImportOptions;
  }
  /**
   * @return ImportContextBakImportOptions
   */
  public function getBakImportOptions()
  {
    return $this->bakImportOptions;
  }
  /**
   * Options for importing data as CSV.
   *
   * @param ImportContextCsvImportOptions $csvImportOptions
   */
  public function setCsvImportOptions(ImportContextCsvImportOptions $csvImportOptions)
  {
    $this->csvImportOptions = $csvImportOptions;
  }
  /**
   * @return ImportContextCsvImportOptions
   */
  public function getCsvImportOptions()
  {
    return $this->csvImportOptions;
  }
  /**
   * The target database for the import. If `fileType` is `SQL`, this field is
   * required only if the import file does not specify a database, and is
   * overridden by any database specification in the import file. For entire
   * instance parallel import operations, the database is overridden by the
   * database name stored in subdirectory name. If `fileType` is `CSV`, one
   * database must be specified.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * The file type for the specified uri.\`SQL`: The file contains SQL
   * statements. \`CSV`: The file contains CSV data.
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
   * The PostgreSQL user for this import operation. PostgreSQL instances only.
   *
   * @param string $importUser
   */
  public function setImportUser($importUser)
  {
    $this->importUser = $importUser;
  }
  /**
   * @return string
   */
  public function getImportUser()
  {
    return $this->importUser;
  }
  /**
   * This is always `sql#importContext`.
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
   * Optional. Options for importing data from SQL statements.
   *
   * @param ImportContextSqlImportOptions $sqlImportOptions
   */
  public function setSqlImportOptions(ImportContextSqlImportOptions $sqlImportOptions)
  {
    $this->sqlImportOptions = $sqlImportOptions;
  }
  /**
   * @return ImportContextSqlImportOptions
   */
  public function getSqlImportOptions()
  {
    return $this->sqlImportOptions;
  }
  /**
   * Optional. Import parameters specific to SQL Server TDE certificates
   *
   * @param ImportContextTdeImportOptions $tdeImportOptions
   */
  public function setTdeImportOptions(ImportContextTdeImportOptions $tdeImportOptions)
  {
    $this->tdeImportOptions = $tdeImportOptions;
  }
  /**
   * @return ImportContextTdeImportOptions
   */
  public function getTdeImportOptions()
  {
    return $this->tdeImportOptions;
  }
  /**
   * Path to the import file in Cloud Storage, in the form
   * `gs://bucketName/fileName`. Compressed gzip files (.gz) are supported when
   * `fileType` is `SQL`. The instance must have write permissions to the bucket
   * and read access to the file.
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
class_alias(ImportContext::class, 'Google_Service_SQLAdmin_ImportContext');
