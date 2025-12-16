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

class SetTablePrimaryKey extends \Google\Collection
{
  protected $collection_key = 'primaryKeyColumns';
  /**
   * Optional. Name for the primary key
   *
   * @var string
   */
  public $primaryKey;
  /**
   * Required. List of column names for the primary key
   *
   * @var string[]
   */
  public $primaryKeyColumns;

  /**
   * Optional. Name for the primary key
   *
   * @param string $primaryKey
   */
  public function setPrimaryKey($primaryKey)
  {
    $this->primaryKey = $primaryKey;
  }
  /**
   * @return string
   */
  public function getPrimaryKey()
  {
    return $this->primaryKey;
  }
  /**
   * Required. List of column names for the primary key
   *
   * @param string[] $primaryKeyColumns
   */
  public function setPrimaryKeyColumns($primaryKeyColumns)
  {
    $this->primaryKeyColumns = $primaryKeyColumns;
  }
  /**
   * @return string[]
   */
  public function getPrimaryKeyColumns()
  {
    return $this->primaryKeyColumns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetTablePrimaryKey::class, 'Google_Service_DatabaseMigrationService_SetTablePrimaryKey');
