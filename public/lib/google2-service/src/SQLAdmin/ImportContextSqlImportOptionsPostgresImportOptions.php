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

class ImportContextSqlImportOptionsPostgresImportOptions extends \Google\Model
{
  /**
   * Optional. The --clean flag for the pg_restore utility. This flag applies
   * only if you enabled Cloud SQL to import files in parallel.
   *
   * @var bool
   */
  public $clean;
  /**
   * Optional. The --if-exists flag for the pg_restore utility. This flag
   * applies only if you enabled Cloud SQL to import files in parallel.
   *
   * @var bool
   */
  public $ifExists;

  /**
   * Optional. The --clean flag for the pg_restore utility. This flag applies
   * only if you enabled Cloud SQL to import files in parallel.
   *
   * @param bool $clean
   */
  public function setClean($clean)
  {
    $this->clean = $clean;
  }
  /**
   * @return bool
   */
  public function getClean()
  {
    return $this->clean;
  }
  /**
   * Optional. The --if-exists flag for the pg_restore utility. This flag
   * applies only if you enabled Cloud SQL to import files in parallel.
   *
   * @param bool $ifExists
   */
  public function setIfExists($ifExists)
  {
    $this->ifExists = $ifExists;
  }
  /**
   * @return bool
   */
  public function getIfExists()
  {
    return $this->ifExists;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImportContextSqlImportOptionsPostgresImportOptions::class, 'Google_Service_SQLAdmin_ImportContextSqlImportOptionsPostgresImportOptions');
