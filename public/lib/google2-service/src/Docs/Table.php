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

namespace Google\Service\Docs;

class Table extends \Google\Collection
{
  protected $collection_key = 'tableRows';
  /**
   * Number of columns in the table. It's possible for a table to be non-
   * rectangular, so some rows may have a different number of cells.
   *
   * @var int
   */
  public $columns;
  /**
   * Number of rows in the table.
   *
   * @var int
   */
  public $rows;
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @var string[]
   */
  public $suggestedDeletionIds;
  /**
   * The suggested insertion IDs. A Table may have multiple insertion IDs if
   * it's a nested suggested change. If empty, then this is not a suggested
   * insertion.
   *
   * @var string[]
   */
  public $suggestedInsertionIds;
  protected $tableRowsType = TableRow::class;
  protected $tableRowsDataType = 'array';
  protected $tableStyleType = TableStyle::class;
  protected $tableStyleDataType = '';

  /**
   * Number of columns in the table. It's possible for a table to be non-
   * rectangular, so some rows may have a different number of cells.
   *
   * @param int $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return int
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Number of rows in the table.
   *
   * @param int $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return int
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @param string[] $suggestedDeletionIds
   */
  public function setSuggestedDeletionIds($suggestedDeletionIds)
  {
    $this->suggestedDeletionIds = $suggestedDeletionIds;
  }
  /**
   * @return string[]
   */
  public function getSuggestedDeletionIds()
  {
    return $this->suggestedDeletionIds;
  }
  /**
   * The suggested insertion IDs. A Table may have multiple insertion IDs if
   * it's a nested suggested change. If empty, then this is not a suggested
   * insertion.
   *
   * @param string[] $suggestedInsertionIds
   */
  public function setSuggestedInsertionIds($suggestedInsertionIds)
  {
    $this->suggestedInsertionIds = $suggestedInsertionIds;
  }
  /**
   * @return string[]
   */
  public function getSuggestedInsertionIds()
  {
    return $this->suggestedInsertionIds;
  }
  /**
   * The contents and style of each row.
   *
   * @param TableRow[] $tableRows
   */
  public function setTableRows($tableRows)
  {
    $this->tableRows = $tableRows;
  }
  /**
   * @return TableRow[]
   */
  public function getTableRows()
  {
    return $this->tableRows;
  }
  /**
   * The style of the table.
   *
   * @param TableStyle $tableStyle
   */
  public function setTableStyle(TableStyle $tableStyle)
  {
    $this->tableStyle = $tableStyle;
  }
  /**
   * @return TableStyle
   */
  public function getTableStyle()
  {
    return $this->tableStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Table::class, 'Google_Service_Docs_Table');
