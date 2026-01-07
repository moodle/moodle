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

class TableConstraintsForeignKeysColumnReferences extends \Google\Model
{
  /**
   * Required. The column in the primary key that are referenced by the
   * referencing_column.
   *
   * @var string
   */
  public $referencedColumn;
  /**
   * Required. The column that composes the foreign key.
   *
   * @var string
   */
  public $referencingColumn;

  /**
   * Required. The column in the primary key that are referenced by the
   * referencing_column.
   *
   * @param string $referencedColumn
   */
  public function setReferencedColumn($referencedColumn)
  {
    $this->referencedColumn = $referencedColumn;
  }
  /**
   * @return string
   */
  public function getReferencedColumn()
  {
    return $this->referencedColumn;
  }
  /**
   * Required. The column that composes the foreign key.
   *
   * @param string $referencingColumn
   */
  public function setReferencingColumn($referencingColumn)
  {
    $this->referencingColumn = $referencingColumn;
  }
  /**
   * @return string
   */
  public function getReferencingColumn()
  {
    return $this->referencingColumn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableConstraintsForeignKeysColumnReferences::class, 'Google_Service_Bigquery_TableConstraintsForeignKeysColumnReferences');
