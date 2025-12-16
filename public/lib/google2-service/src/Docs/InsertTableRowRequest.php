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

class InsertTableRowRequest extends \Google\Model
{
  /**
   * Whether to insert new row below the reference cell location. - `True`:
   * insert below the cell. - `False`: insert above the cell.
   *
   * @var bool
   */
  public $insertBelow;
  protected $tableCellLocationType = TableCellLocation::class;
  protected $tableCellLocationDataType = '';

  /**
   * Whether to insert new row below the reference cell location. - `True`:
   * insert below the cell. - `False`: insert above the cell.
   *
   * @param bool $insertBelow
   */
  public function setInsertBelow($insertBelow)
  {
    $this->insertBelow = $insertBelow;
  }
  /**
   * @return bool
   */
  public function getInsertBelow()
  {
    return $this->insertBelow;
  }
  /**
   * The reference table cell location from which rows will be inserted. A new
   * row will be inserted above (or below) the row where the reference cell is.
   * If the reference cell is a merged cell, a new row will be inserted above
   * (or below) the merged cell.
   *
   * @param TableCellLocation $tableCellLocation
   */
  public function setTableCellLocation(TableCellLocation $tableCellLocation)
  {
    $this->tableCellLocation = $tableCellLocation;
  }
  /**
   * @return TableCellLocation
   */
  public function getTableCellLocation()
  {
    return $this->tableCellLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertTableRowRequest::class, 'Google_Service_Docs_InsertTableRowRequest');
