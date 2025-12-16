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

class IndexEntity extends \Google\Collection
{
  protected $collection_key = 'tableColumnsDescending';
  /**
   * Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  /**
   * The name of the index.
   *
   * @var string
   */
  public $name;
  /**
   * Table columns used as part of the Index, for example B-TREE index should
   * list the columns which constitutes the index.
   *
   * @var string[]
   */
  public $tableColumns;
  /**
   * For each table_column, mark whether it's sorting order is ascending (false)
   * or descending (true). If no value is defined, assume all columns are sorted
   * in ascending order. Otherwise, the number of items must match that of
   * table_columns with each value specifying the direction of the matched
   * column by its index.
   *
   * @var bool[]
   */
  public $tableColumnsDescending;
  /**
   * Type of index, for example B-TREE.
   *
   * @var string
   */
  public $type;
  /**
   * Boolean value indicating whether the index is unique.
   *
   * @var bool
   */
  public $unique;

  /**
   * Custom engine specific features.
   *
   * @param array[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return array[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * The name of the index.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Table columns used as part of the Index, for example B-TREE index should
   * list the columns which constitutes the index.
   *
   * @param string[] $tableColumns
   */
  public function setTableColumns($tableColumns)
  {
    $this->tableColumns = $tableColumns;
  }
  /**
   * @return string[]
   */
  public function getTableColumns()
  {
    return $this->tableColumns;
  }
  /**
   * For each table_column, mark whether it's sorting order is ascending (false)
   * or descending (true). If no value is defined, assume all columns are sorted
   * in ascending order. Otherwise, the number of items must match that of
   * table_columns with each value specifying the direction of the matched
   * column by its index.
   *
   * @param bool[] $tableColumnsDescending
   */
  public function setTableColumnsDescending($tableColumnsDescending)
  {
    $this->tableColumnsDescending = $tableColumnsDescending;
  }
  /**
   * @return bool[]
   */
  public function getTableColumnsDescending()
  {
    return $this->tableColumnsDescending;
  }
  /**
   * Type of index, for example B-TREE.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Boolean value indicating whether the index is unique.
   *
   * @param bool $unique
   */
  public function setUnique($unique)
  {
    $this->unique = $unique;
  }
  /**
   * @return bool
   */
  public function getUnique()
  {
    return $this->unique;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IndexEntity::class, 'Google_Service_DatabaseMigrationService_IndexEntity');
