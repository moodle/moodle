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

class ConstraintEntity extends \Google\Collection
{
  protected $collection_key = 'tableColumns';
  /**
   * Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  /**
   * The name of the table constraint.
   *
   * @var string
   */
  public $name;
  /**
   * Reference columns which may be associated with the constraint. For example,
   * if the constraint is a FOREIGN_KEY, this represents the list of full names
   * of referenced columns by the foreign key.
   *
   * @var string[]
   */
  public $referenceColumns;
  /**
   * Reference table which may be associated with the constraint. For example,
   * if the constraint is a FOREIGN_KEY, this represents the list of full name
   * of the referenced table by the foreign key.
   *
   * @var string
   */
  public $referenceTable;
  /**
   * Table columns used as part of the Constraint, for example primary key
   * constraint should list the columns which constitutes the key.
   *
   * @var string[]
   */
  public $tableColumns;
  /**
   * Table which is associated with the constraint. In case the constraint is
   * defined on a table, this field is left empty as this information is stored
   * in parent_name. However, if constraint is defined on a view, this field
   * stores the table name on which the view is defined.
   *
   * @var string
   */
  public $tableName;
  /**
   * Type of constraint, for example unique, primary key, foreign key (currently
   * only primary key is supported).
   *
   * @var string
   */
  public $type;

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
   * The name of the table constraint.
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
   * Reference columns which may be associated with the constraint. For example,
   * if the constraint is a FOREIGN_KEY, this represents the list of full names
   * of referenced columns by the foreign key.
   *
   * @param string[] $referenceColumns
   */
  public function setReferenceColumns($referenceColumns)
  {
    $this->referenceColumns = $referenceColumns;
  }
  /**
   * @return string[]
   */
  public function getReferenceColumns()
  {
    return $this->referenceColumns;
  }
  /**
   * Reference table which may be associated with the constraint. For example,
   * if the constraint is a FOREIGN_KEY, this represents the list of full name
   * of the referenced table by the foreign key.
   *
   * @param string $referenceTable
   */
  public function setReferenceTable($referenceTable)
  {
    $this->referenceTable = $referenceTable;
  }
  /**
   * @return string
   */
  public function getReferenceTable()
  {
    return $this->referenceTable;
  }
  /**
   * Table columns used as part of the Constraint, for example primary key
   * constraint should list the columns which constitutes the key.
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
   * Table which is associated with the constraint. In case the constraint is
   * defined on a table, this field is left empty as this information is stored
   * in parent_name. However, if constraint is defined on a view, this field
   * stores the table name on which the view is defined.
   *
   * @param string $tableName
   */
  public function setTableName($tableName)
  {
    $this->tableName = $tableName;
  }
  /**
   * @return string
   */
  public function getTableName()
  {
    return $this->tableName;
  }
  /**
   * Type of constraint, for example unique, primary key, foreign key (currently
   * only primary key is supported).
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConstraintEntity::class, 'Google_Service_DatabaseMigrationService_ConstraintEntity');
