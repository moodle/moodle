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

class ExportContextSqlExportOptions extends \Google\Collection
{
  protected $collection_key = 'tables';
  protected $mysqlExportOptionsType = ExportContextSqlExportOptionsMysqlExportOptions::class;
  protected $mysqlExportOptionsDataType = '';
  /**
   * Optional. Whether or not the export should be parallel.
   *
   * @var bool
   */
  public $parallel;
  protected $postgresExportOptionsType = ExportContextSqlExportOptionsPostgresExportOptions::class;
  protected $postgresExportOptionsDataType = '';
  /**
   * Export only schemas.
   *
   * @var bool
   */
  public $schemaOnly;
  /**
   * Tables to export, or that were exported, from the specified database. If
   * you specify tables, specify one and only one database. For PostgreSQL
   * instances, you can specify only one table.
   *
   * @var string[]
   */
  public $tables;
  /**
   * Optional. The number of threads to use for parallel export.
   *
   * @var int
   */
  public $threads;

  /**
   * Options for exporting from MySQL.
   *
   * @param ExportContextSqlExportOptionsMysqlExportOptions $mysqlExportOptions
   */
  public function setMysqlExportOptions(ExportContextSqlExportOptionsMysqlExportOptions $mysqlExportOptions)
  {
    $this->mysqlExportOptions = $mysqlExportOptions;
  }
  /**
   * @return ExportContextSqlExportOptionsMysqlExportOptions
   */
  public function getMysqlExportOptions()
  {
    return $this->mysqlExportOptions;
  }
  /**
   * Optional. Whether or not the export should be parallel.
   *
   * @param bool $parallel
   */
  public function setParallel($parallel)
  {
    $this->parallel = $parallel;
  }
  /**
   * @return bool
   */
  public function getParallel()
  {
    return $this->parallel;
  }
  /**
   * Options for exporting from a Cloud SQL for PostgreSQL instance.
   *
   * @param ExportContextSqlExportOptionsPostgresExportOptions $postgresExportOptions
   */
  public function setPostgresExportOptions(ExportContextSqlExportOptionsPostgresExportOptions $postgresExportOptions)
  {
    $this->postgresExportOptions = $postgresExportOptions;
  }
  /**
   * @return ExportContextSqlExportOptionsPostgresExportOptions
   */
  public function getPostgresExportOptions()
  {
    return $this->postgresExportOptions;
  }
  /**
   * Export only schemas.
   *
   * @param bool $schemaOnly
   */
  public function setSchemaOnly($schemaOnly)
  {
    $this->schemaOnly = $schemaOnly;
  }
  /**
   * @return bool
   */
  public function getSchemaOnly()
  {
    return $this->schemaOnly;
  }
  /**
   * Tables to export, or that were exported, from the specified database. If
   * you specify tables, specify one and only one database. For PostgreSQL
   * instances, you can specify only one table.
   *
   * @param string[] $tables
   */
  public function setTables($tables)
  {
    $this->tables = $tables;
  }
  /**
   * @return string[]
   */
  public function getTables()
  {
    return $this->tables;
  }
  /**
   * Optional. The number of threads to use for parallel export.
   *
   * @param int $threads
   */
  public function setThreads($threads)
  {
    $this->threads = $threads;
  }
  /**
   * @return int
   */
  public function getThreads()
  {
    return $this->threads;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExportContextSqlExportOptions::class, 'Google_Service_SQLAdmin_ExportContextSqlExportOptions');
