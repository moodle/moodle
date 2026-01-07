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

class GridCoordinate extends \Google\Model
{
  /**
   * The column index of the coordinate.
   *
   * @var int
   */
  public $columnIndex;
  /**
   * The row index of the coordinate.
   *
   * @var int
   */
  public $rowIndex;
  /**
   * The sheet this coordinate is on.
   *
   * @var int
   */
  public $sheetId;

  /**
   * The column index of the coordinate.
   *
   * @param int $columnIndex
   */
  public function setColumnIndex($columnIndex)
  {
    $this->columnIndex = $columnIndex;
  }
  /**
   * @return int
   */
  public function getColumnIndex()
  {
    return $this->columnIndex;
  }
  /**
   * The row index of the coordinate.
   *
   * @param int $rowIndex
   */
  public function setRowIndex($rowIndex)
  {
    $this->rowIndex = $rowIndex;
  }
  /**
   * @return int
   */
  public function getRowIndex()
  {
    return $this->rowIndex;
  }
  /**
   * The sheet this coordinate is on.
   *
   * @param int $sheetId
   */
  public function setSheetId($sheetId)
  {
    $this->sheetId = $sheetId;
  }
  /**
   * @return int
   */
  public function getSheetId()
  {
    return $this->sheetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GridCoordinate::class, 'Google_Service_Sheets_GridCoordinate');
