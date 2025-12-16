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

namespace Google\Service\BigtableAdmin;

class CreateTableRequest extends \Google\Collection
{
  protected $collection_key = 'initialSplits';
  protected $initialSplitsType = Split::class;
  protected $initialSplitsDataType = 'array';
  protected $tableType = Table::class;
  protected $tableDataType = '';
  /**
   * Required. The name by which the new table should be referred to within the
   * parent instance, e.g., `foobar` rather than `{parent}/tables/foobar`.
   * Maximum 50 characters.
   *
   * @var string
   */
  public $tableId;

  /**
   * The optional list of row keys that will be used to initially split the
   * table into several tablets (tablets are similar to HBase regions). Given
   * two split keys, `s1` and `s2`, three tablets will be created, spanning the
   * key ranges: `[, s1), [s1, s2), [s2, )`. Example: * Row keys := `["a",
   * "apple", "custom", "customer_1", "customer_2",` `"other", "zz"]` *
   * initial_split_keys := `["apple", "customer_1", "customer_2", "other"]` *
   * Key assignment: - Tablet 1 `[, apple) => {"a"}.` - Tablet 2 `[apple,
   * customer_1) => {"apple", "custom"}.` - Tablet 3 `[customer_1, customer_2)
   * => {"customer_1"}.` - Tablet 4 `[customer_2, other) => {"customer_2"}.` -
   * Tablet 5 `[other, ) => {"other", "zz"}.`
   *
   * @param Split[] $initialSplits
   */
  public function setInitialSplits($initialSplits)
  {
    $this->initialSplits = $initialSplits;
  }
  /**
   * @return Split[]
   */
  public function getInitialSplits()
  {
    return $this->initialSplits;
  }
  /**
   * Required. The Table to create.
   *
   * @param Table $table
   */
  public function setTable(Table $table)
  {
    $this->table = $table;
  }
  /**
   * @return Table
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * Required. The name by which the new table should be referred to within the
   * parent instance, e.g., `foobar` rather than `{parent}/tables/foobar`.
   * Maximum 50 characters.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateTableRequest::class, 'Google_Service_BigtableAdmin_CreateTableRequest');
