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

namespace Google\Service\Spanner;

class Write extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * The names of the columns in table to be written. The list of columns must
   * contain enough columns to allow Cloud Spanner to derive values for all
   * primary key columns in the row(s) to be modified.
   *
   * @var string[]
   */
  public $columns;
  /**
   * Required. The table whose rows will be written.
   *
   * @var string
   */
  public $table;
  /**
   * The values to be written. `values` can contain more than one list of
   * values. If it does, then multiple rows are written, one for each entry in
   * `values`. Each list in `values` must have exactly as many entries as there
   * are entries in columns above. Sending multiple lists is equivalent to
   * sending multiple `Mutation`s, each containing one `values` entry and
   * repeating table and columns. Individual values in each list are encoded as
   * described here.
   *
   * @var array[]
   */
  public $values;

  /**
   * The names of the columns in table to be written. The list of columns must
   * contain enough columns to allow Cloud Spanner to derive values for all
   * primary key columns in the row(s) to be modified.
   *
   * @param string[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return string[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Required. The table whose rows will be written.
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * The values to be written. `values` can contain more than one list of
   * values. If it does, then multiple rows are written, one for each entry in
   * `values`. Each list in `values` must have exactly as many entries as there
   * are entries in columns above. Sending multiple lists is equivalent to
   * sending multiple `Mutation`s, each containing one `values` entry and
   * repeating table and columns. Individual values in each list are encoded as
   * described here.
   *
   * @param array[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return array[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Write::class, 'Google_Service_Spanner_Write');
