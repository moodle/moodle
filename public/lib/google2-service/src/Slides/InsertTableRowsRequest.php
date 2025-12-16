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

class InsertTableRowsRequest extends \Google\Model
{
  protected $cellLocationType = TableCellLocation::class;
  protected $cellLocationDataType = '';
  /**
   * Whether to insert new rows below the reference cell location. - `True`:
   * insert below the cell. - `False`: insert above the cell.
   *
   * @var bool
   */
  public $insertBelow;
  /**
   * The number of rows to be inserted. Maximum 20 per request.
   *
   * @var int
   */
  public $number;
  /**
   * The table to insert rows into.
   *
   * @var string
   */
  public $tableObjectId;

  /**
   * The reference table cell location from which rows will be inserted. A new
   * row will be inserted above (or below) the row where the reference cell is.
   * If the reference cell is a merged cell, a new row will be inserted above
   * (or below) the merged cell.
   *
   * @param TableCellLocation $cellLocation
   */
  public function setCellLocation(TableCellLocation $cellLocation)
  {
    $this->cellLocation = $cellLocation;
  }
  /**
   * @return TableCellLocation
   */
  public function getCellLocation()
  {
    return $this->cellLocation;
  }
  /**
   * Whether to insert new rows below the reference cell location. - `True`:
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
   * The number of rows to be inserted. Maximum 20 per request.
   *
   * @param int $number
   */
  public function setNumber($number)
  {
    $this->number = $number;
  }
  /**
   * @return int
   */
  public function getNumber()
  {
    return $this->number;
  }
  /**
   * The table to insert rows into.
   *
   * @param string $tableObjectId
   */
  public function setTableObjectId($tableObjectId)
  {
    $this->tableObjectId = $tableObjectId;
  }
  /**
   * @return string
   */
  public function getTableObjectId()
  {
    return $this->tableObjectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertTableRowsRequest::class, 'Google_Service_Slides_InsertTableRowsRequest');
