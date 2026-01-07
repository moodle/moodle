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

class TableConstraintsForeignKeys extends \Google\Collection
{
  protected $collection_key = 'columnReferences';
  protected $columnReferencesType = TableConstraintsForeignKeysColumnReferences::class;
  protected $columnReferencesDataType = 'array';
  /**
   * Optional. Set only if the foreign key constraint is named.
   *
   * @var string
   */
  public $name;
  protected $referencedTableType = TableConstraintsForeignKeysReferencedTable::class;
  protected $referencedTableDataType = '';

  /**
   * Required. The columns that compose the foreign key.
   *
   * @param TableConstraintsForeignKeysColumnReferences[] $columnReferences
   */
  public function setColumnReferences($columnReferences)
  {
    $this->columnReferences = $columnReferences;
  }
  /**
   * @return TableConstraintsForeignKeysColumnReferences[]
   */
  public function getColumnReferences()
  {
    return $this->columnReferences;
  }
  /**
   * Optional. Set only if the foreign key constraint is named.
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
   * @param TableConstraintsForeignKeysReferencedTable $referencedTable
   */
  public function setReferencedTable(TableConstraintsForeignKeysReferencedTable $referencedTable)
  {
    $this->referencedTable = $referencedTable;
  }
  /**
   * @return TableConstraintsForeignKeysReferencedTable
   */
  public function getReferencedTable()
  {
    return $this->referencedTable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableConstraintsForeignKeys::class, 'Google_Service_Bigquery_TableConstraintsForeignKeys');
