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

class ColumnFamilyStats extends \Google\Model
{
  /**
   * How many cells are present per column qualifier in this column family,
   * averaged over all rows containing any column in the column family. e.g. For
   * column family "family" in a table with 3 rows: * A row with 3 cells in
   * "family:col" and 1 cell in "other:col" (3 cells / 1 column in "family") * A
   * row with 1 cell in "family:col", 7 cells in "family:other_col", and 7 cells
   * in "other:data" (8 cells / 2 columns in "family") * A row with 3 cells in
   * "other:col" (0 columns in "family", "family" not present) would report (3 +
   * 8 + 0)/(1 + 2 + 0) = 3.66 in this field.
   *
   * @var 
   */
  public $averageCellsPerColumn;
  /**
   * How many column qualifiers are present in this column family, averaged over
   * all rows in the table. e.g. For column family "family" in a table with 3
   * rows: * A row with cells in "family:col" and "other:col" (1 column in
   * "family") * A row with cells in "family:col", "family:other_col", and
   * "other:data" (2 columns in "family") * A row with cells in "other:col" (0
   * columns in "family", "family" not present) would report (1 + 2 + 0)/3 = 1.5
   * in this field.
   *
   * @var 
   */
  public $averageColumnsPerRow;
  /**
   * How much space the data in the column family occupies. This is roughly how
   * many bytes would be needed to read the contents of the entire column family
   * (e.g. by streaming all contents out).
   *
   * @var string
   */
  public $logicalDataBytes;

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
   * How much space the data in the column family occupies. This is roughly how
   * many bytes would be needed to read the contents of the entire column family
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ColumnFamilyStats::class, 'Google_Service_BigtableAdmin_ColumnFamilyStats');
