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

class ExportContextSqlExportOptionsPostgresExportOptions extends \Google\Model
{
  /**
   * Optional. Use this option to include DROP  SQL statements. Use these
   * statements to delete database objects before running the import operation.
   *
   * @var bool
   */
  public $clean;
  /**
   * Optional. Option to include an IF EXISTS SQL statement with each DROP
   * statement produced by clean.
   *
   * @var bool
   */
  public $ifExists;

  /**
   * Optional. Use this option to include DROP  SQL statements. Use these
   * statements to delete database objects before running the import operation.
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
   * Optional. Option to include an IF EXISTS SQL statement with each DROP
   * statement produced by clean.
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
class_alias(ExportContextSqlExportOptionsPostgresExportOptions::class, 'Google_Service_SQLAdmin_ExportContextSqlExportOptionsPostgresExportOptions');
