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

class TableStats extends \Google\Model
{
  /**
   * How many cells are present per column (column family, column qualifier)
   * combinations, averaged over all columns in all rows in the table. e.g. A
   * table with 2 rows: * A row with 3 cells in "family:col" and 1 cell in
   * "other:col" (4 cells / 2 columns) * A row with 1 cell in "family:col", 7
   * cells in "family:other_col", and 7 cells in "other:data" (15 cells / 3
   * columns) would report (4 + 15)/(2 + 3) = 3.8 in this field.
   *
   * @var 
   */
  public $averageCellsPerColumn;
  /**
   * How many (column family, column qualifier) combinations are present per row
   * in the table, averaged over all rows in the table. e.g. A table with 2
   * rows: * A row with cells in "family:col" and "other:col" (2 distinct
   * columns) * A row with cells in "family:col", "family:other_col", and
   * "other:data" (3 distinct columns) would report (2 + 3)/2 = 2.5 in this
   * field.
   *
   * @var 
   */
  public $averageColumnsPerRow;
  /**
   * This is roughly how many bytes would be needed to read the entire table
   * (e.g. by streaming all contents out).
   *
   * @var string
   */
  public $logicalDataBytes;
  /**
   * How many rows are in the table.
   *
   * @var string
   */
  public $rowCount;

  public function setAverageCellsPerColumn($averageCellsPerColumn)
  {
    $this->averageCellsPerColumn = $averageCellsPerColumn;
  }
  public function getAverageCellsPerColumn()
  {
    return $this->averageCellsPerColumn;
  }
  public function setAverageColumnsPerRow($averageColumnsPerRow)
  {
    $this->averageColumnsPerRow = $averageColumnsPerRow;
  }
  public function getAverageColumnsPerRow()
  {
    return $this->averageColumnsPerRow;
  }
  /**
   * This is roughly how many bytes would be needed to read the entire table
   * (e.g. by streaming all contents out).
   *
   * @param string $logicalDataBytes
   */
  public function setLogicalDataBytes($logicalDataBytes)
  {
    $this->logicalDataBytes = $logicalDataBytes;
  }
  /**
   * @return string
   */
  public function getLogicalDataBytes()
  {
    return $this->logicalDataBytes;
  }
  /**
   * How many rows are in the table.
   *
   * @param string $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return string
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableStats::class, 'Google_Service_BigtableAdmin_TableStats');
