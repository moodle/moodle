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

namespace Google\Service\DatabaseMigrationService;

class FilterTableColumns extends \Google\Collection
{
  protected $collection_key = 'includeColumns';
  /**
   * Optional. List of columns to be excluded for a particular table.
   *
   * @var string[]
   */
  public $excludeColumns;
  /**
   * Optional. List of columns to be included for a particular table.
   *
   * @var string[]
   */
  public $includeColumns;

  /**
   * Optional. List of columns to be excluded for a particular table.
   *
   * @param string[] $excludeColumns
   */
  public function setExcludeColumns($excludeColumns)
  {
    $this->excludeColumns = $excludeColumns;
  }
  /**
   * @return string[]
   */
  public function getExcludeColumns()
  {
    return $this->excludeColumns;
  }
  /**
   * Optional. List of columns to be included for a particular table.
   *
   * @param string[] $includeColumns
   */
  public function setIncludeColumns($includeColumns)
  {
    $this->includeColumns = $includeColumns;
  }
  /**
   * @return string[]
   */
  public function getIncludeColumns()
  {
    return $this->includeColumns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilterTableColumns::class, 'Google_Service_DatabaseMigrationService_FilterTableColumns');
