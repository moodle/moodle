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

class PartitionReadRequest extends \Google\Collection
{
  protected $collection_key = 'columns';
  /**
   * The columns of table to be returned for each row matching this request.
   *
   * @var string[]
   */
  public $columns;
  /**
   * If non-empty, the name of an index on table. This index is used instead of
   * the table primary key when interpreting key_set and sorting result rows.
   * See key_set for further information.
   *
   * @var string
   */
  public $index;
  protected $keySetType = KeySet::class;
  protected $keySetDataType = '';
  protected $partitionOptionsType = PartitionOptions::class;
  protected $partitionOptionsDataType = '';
  /**
   * Required. The name of the table in the database to be read.
   *
   * @var string
   */
  public $table;
  protected $transactionType = TransactionSelector::class;
  protected $transactionDataType = '';

  /**
   * The columns of table to be returned for each row matching this request.
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
   * If non-empty, the name of an index on table. This index is used instead of
   * the table primary key when interpreting key_set and sorting result rows.
   * See key_set for further information.
   *
   * @param string $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Required. `key_set` identifies the rows to be yielded. `key_set` names the
   * primary keys of the rows in table to be yielded, unless index is present.
   * If index is present, then key_set instead names index keys in index. It
   * isn't an error for the `key_set` to name rows that don't exist in the
   * database. Read yields nothing for nonexistent rows.
   *
   * @param KeySet $keySet
   */
  public function setKeySet(KeySet $keySet)
  {
    $this->keySet = $keySet;
  }
  /**
   * @return KeySet
   */
  public function getKeySet()
  {
    return $this->keySet;
  }
  /**
   * Additional options that affect how many partitions are created.
   *
   * @param PartitionOptions $partitionOptions
   */
  public function setPartitionOptions(PartitionOptions $partitionOptions)
  {
    $this->partitionOptions = $partitionOptions;
  }
  /**
   * @return PartitionOptions
   */
  public function getPartitionOptions()
  {
    return $this->partitionOptions;
  }
  /**
   * Required. The name of the table in the database to be read.
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
   * Read only snapshot transactions are supported, read/write and single use
   * transactions are not.
   *
   * @param TransactionSelector $transaction
   */
  public function setTransaction(TransactionSelector $transaction)
  {
    $this->transaction = $transaction;
  }
  /**
   * @return TransactionSelector
   */
  public function getTransaction()
  {
    return $this->transaction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionReadRequest::class, 'Google_Service_Spanner_PartitionReadRequest');
