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

namespace Google\Service\Sheets;

class GridData extends \Google\Collection
{
  protected $collection_key = 'rowMetadata';
  protected $columnMetadataType = DimensionProperties::class;
  protected $columnMetadataDataType = 'array';
  protected $rowDataType = RowData::class;
  protected $rowDataDataType = 'array';
  protected $rowMetadataType = DimensionProperties::class;
  protected $rowMetadataDataType = 'array';
  /**
   * The first column this GridData refers to, zero-based.
   *
   * @var int
   */
  public $startColumn;
  /**
   * The first row this GridData refers to, zero-based.
   *
   * @var int
   */
  public $startRow;

  /**
   * Metadata about the requested columns in the grid, starting with the column
   * in start_column.
   *
   * @param DimensionProperties[] $columnMetadata
   */
  public function setColumnMetadata($columnMetadata)
  {
    $this->columnMetadata = $columnMetadata;
  }
  /**
   * @return DimensionProperties[]
   */
  public function getColumnMetadata()
  {
    return $this->columnMetadata;
  }
  /**
   * The data in the grid, one entry per row, starting with the row in startRow.
   * The values in RowData will correspond to columns starting at start_column.
   *
   * @param RowData[] $rowData
   */
  public function setRowData($rowData)
  {
    $this->rowData = $rowData;
  }
  /**
   * @return RowData[]
   */
  public function getRowData()
  {
    return $this->rowData;
  }
  /**
   * Metadata about the requested rows in the grid, starting with the row in
   * start_row.
   *
   * @param DimensionProperties[] $rowMetadata
   */
  public function setRowMetadata($rowMetadata)
  {
    $this->rowMetadata = $rowMetadata;
  }
  /**
   * @return DimensionProperties[]
   */
  public function getRowMetadata()
  {
    return $this->rowMetadata;
  }
  /**
   * The first column this GridData refers to, zero-based.
   *
   * @param int $startColumn
   */
  public function setStartColumn($startColumn)
  {
    $this->startColumn = $startColumn;
  }
  /**
   * @return int
   */
  public function getStartColumn()
  {
    return $this->startColumn;
  }
  /**
   * The first row this GridData refers to, zero-based.
   *
   * @param int $startRow
   */
  public function setStartRow($startRow)
  {
    $this->startRow = $startRow;
  }
  /**
   * @return int
   */
  public function getStartRow()
  {
    return $this->startRow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GridData::class, 'Google_Service_Sheets_GridData');
