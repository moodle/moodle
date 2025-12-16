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

class Mutation extends \Google\Model
{
  protected $deleteType = Delete::class;
  protected $deleteDataType = '';
  protected $insertType = Write::class;
  protected $insertDataType = '';
  protected $insertOrUpdateType = Write::class;
  protected $insertOrUpdateDataType = '';
  protected $replaceType = Write::class;
  protected $replaceDataType = '';
  protected $updateType = Write::class;
  protected $updateDataType = '';

  /**
   * Delete rows from a table. Succeeds whether or not the named rows were
   * present.
   *
   * @param Delete $delete
   */
  public function setDelete(Delete $delete)
  {
    $this->delete = $delete;
  }
  /**
   * @return Delete
   */
  public function getDelete()
  {
    return $this->delete;
  }
  /**
   * Insert new rows in a table. If any of the rows already exist, the write or
   * transaction fails with error `ALREADY_EXISTS`.
   *
   * @param Write $insert
   */
  public function setInsert(Write $insert)
  {
    $this->insert = $insert;
  }
  /**
   * @return Write
   */
  public function getInsert()
  {
    return $this->insert;
  }
  /**
   * Like insert, except that if the row already exists, then its column values
   * are overwritten with the ones provided. Any column values not explicitly
   * written are preserved. When using insert_or_update, just as when using
   * insert, all `NOT NULL` columns in the table must be given a value. This
   * holds true even when the row already exists and will therefore actually be
   * updated.
   *
   * @param Write $insertOrUpdate
   */
  public function setInsertOrUpdate(Write $insertOrUpdate)
  {
    $this->insertOrUpdate = $insertOrUpdate;
  }
  /**
   * @return Write
   */
  public function getInsertOrUpdate()
  {
    return $this->insertOrUpdate;
  }
  /**
   * Like insert, except that if the row already exists, it is deleted, and the
   * column values provided are inserted instead. Unlike insert_or_update, this
   * means any values not explicitly written become `NULL`. In an interleaved
   * table, if you create the child table with the `ON DELETE CASCADE`
   * annotation, then replacing a parent row also deletes the child rows.
   * Otherwise, you must delete the child rows before you replace the parent
   * row.
   *
   * @param Write $replace
   */
  public function setReplace(Write $replace)
  {
    $this->replace = $replace;
  }
  /**
   * @return Write
   */
  public function getReplace()
  {
    return $this->replace;
  }
  /**
   * Update existing rows in a table. If any of the rows does not already exist,
   * the transaction fails with error `NOT_FOUND`.
   *
   * @param Write $update
   */
  public function setUpdate(Write $update)
  {
    $this->update = $update;
  }
  /**
   * @return Write
   */
  public function getUpdate()
  {
    return $this->update;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Mutation::class, 'Google_Service_Spanner_Mutation');
