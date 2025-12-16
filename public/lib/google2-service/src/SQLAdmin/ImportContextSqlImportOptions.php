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

class ImportContextSqlImportOptions extends \Google\Model
{
  /**
   * Optional. Whether or not the import should be parallel.
   *
   * @var bool
   */
  public $parallel;
  protected $postgresImportOptionsType = ImportContextSqlImportOptionsPostgresImportOptions::class;
  protected $postgresImportOptionsDataType = '';
  /**
   * Optional. The number of threads to use for parallel import.
   *
   * @var int
   */
  public $threads;

  /**
   * Optional. Whether or not the import should be parallel.
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
   * Optional. Options for importing from a Cloud SQL for PostgreSQL instance.
   *
   * @param ImportContextSqlImportOptionsPostgresImportOptions $postgresImportOptions
   */
  public function setPostgresImportOptions(ImportContextSqlImportOptionsPostgresImportOptions $postgresImportOptions)
  {
    $this->postgresImportOptions = $postgresImportOptions;
  }
  /**
   * @return ImportContextSqlImportOptionsPostgresImportOptions
   */
  public function getPostgresImportOptions()
  {
    return $this->postgresImportOptions;
  }
  /**
   * Optional. The number of threads to use for parallel import.
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
class_alias(ImportContextSqlImportOptions::class, 'Google_Service_SQLAdmin_ImportContextSqlImportOptions');
