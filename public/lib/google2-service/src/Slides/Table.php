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

namespace Google\Service\Slides;

class Table extends \Google\Collection
{
  protected $collection_key = 'verticalBorderRows';
  /**
   * Number of columns in the table.
   *
   * @var int
   */
  public $columns;
  protected $horizontalBorderRowsType = TableBorderRow::class;
  protected $horizontalBorderRowsDataType = 'array';
  /**
   * Number of rows in the table.
   *
   * @var int
   */
  public $rows;
  protected $tableColumnsType = TableColumnProperties::class;
  protected $tableColumnsDataType = 'array';
  protected $tableRowsType = TableRow::class;
  protected $tableRowsDataType = 'array';
  protected $verticalBorderRowsType = TableBorderRow::class;
  protected $verticalBorderRowsDataType = 'array';

  /**
   * Number of columns in the table.
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
   * Properties of horizontal cell borders. A table's horizontal cell borders
   * are represented as a grid. The grid has one more row than the number of
   * rows in the table and the same number of columns as the table. For example,
   * if the table is 3 x 3, its horizontal borders will be represented as a grid
   * with 4 rows and 3 columns.
   *
   * @param TableBorderRow[] $horizontalBorderRows
   */
  public function setHorizontalBorderRows($horizontalBorderRows)
  {
    $this->horizontalBorderRows = $horizontalBorderRows;
  }
  /**
   * @return TableBorderRow[]
   */
  public function getHorizontalBorderRows()
  {
    return $this->horizontalBorderRows;
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
   * Properties of each column.
   *
   * @param TableColumnProperties[] $tableColumns
   */
  public function setTableColumns($tableColumns)
  {
    $this->tableColumns = $tableColumns;
  }
  /**
   * @return TableColumnProperties[]
   */
  public function getTableColumns()
  {
    return $this->tableColumns;
  }
  /**
   * Properties and contents of each row. Cells that span multiple rows are
   * contained in only one of these rows and have a row_span greater than 1.
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
   * Properties of vertical cell borders. A table's vertical cell borders are
   * represented as a grid. The grid has the same number of rows as the table
   * and one more column than the number of columns in the table. For example,
   * if the table is 3 x 3, its vertical borders will be represented as a grid
   * with 3 rows and 4 columns.
   *
   * @param TableBorderRow[] $verticalBorderRows
   */
  public function setVerticalBorderRows($verticalBorderRows)
  {
    $this->verticalBorderRows = $verticalBorderRows;
  }
  /**
   * @return TableBorderRow[]
   */
  public function getVerticalBorderRows()
  {
    return $this->verticalBorderRows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Table::class, 'Google_Service_Slides_Table');
