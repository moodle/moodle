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

namespace Google\Service\Bigquery;

class TableConstraints extends \Google\Collection
{
  protected $collection_key = 'foreignKeys';
  protected $foreignKeysType = TableConstraintsForeignKeys::class;
  protected $foreignKeysDataType = 'array';
  protected $primaryKeyType = TableConstraintsPrimaryKey::class;
  protected $primaryKeyDataType = '';

  /**
   * Optional. Present only if the table has a foreign key. The foreign key is
   * not enforced.
   *
   * @param TableConstraintsForeignKeys[] $foreignKeys
   */
  public function setForeignKeys($foreignKeys)
  {
    $this->foreignKeys = $foreignKeys;
  }
  /**
   * @return TableConstraintsForeignKeys[]
   */
  public function getForeignKeys()
  {
    return $this->foreignKeys;
  }
  /**
   * Represents the primary key constraint on a table's columns.
   *
   * @param TableConstraintsPrimaryKey $primaryKey
   */
  public function setPrimaryKey(TableConstraintsPrimaryKey $primaryKey)
  {
    $this->primaryKey = $primaryKey;
  }
  /**
   * @return TableConstraintsPrimaryKey
   */
  public function getPrimaryKey()
  {
    return $this->primaryKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableConstraints::class, 'Google_Service_Bigquery_TableConstraints');
