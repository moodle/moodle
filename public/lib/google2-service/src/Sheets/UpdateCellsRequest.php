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

class UpdateCellsRequest extends \Google\Collection
{
  protected $collection_key = 'rows';
  /**
   * The fields of CellData that should be updated. At least one field must be
   * specified. The root is the CellData; 'row.values.' should not be specified.
   * A single `"*"` can be used as short-hand for listing every field.
   *
   * @var string
   */
  public $fields;
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';
  protected $rowsType = RowData::class;
  protected $rowsDataType = 'array';
  protected $startType = GridCoordinate::class;
  protected $startDataType = '';

  /**
   * The fields of CellData that should be updated. At least one field must be
   * specified. The root is the CellData; 'row.values.' should not be specified.
   * A single `"*"` can be used as short-hand for listing every field.
   *
   * @param string $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return string
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * The range to write data to. If the data in rows does not cover the entire
   * requested range, the fields matching those set in fields will be cleared.
   *
   * @param GridRange $range
   */
  public function setRange(GridRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return GridRange
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * The data to write.
   *
   * @param RowData[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return RowData[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * The coordinate to start writing data at. Any number of rows and columns
   * (including a different number of columns per row) may be written.
   *
   * @param GridCoordinate $start
   */
  public function setStart(GridCoordinate $start)
  {
    $this->start = $start;
  }
  /**
   * @return GridCoordinate
   */
  public function getStart()
  {
    return $this->start;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateCellsRequest::class, 'Google_Service_Sheets_UpdateCellsRequest');
