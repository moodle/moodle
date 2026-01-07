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

class InsertTableColumnsRequest extends \Google\Model
{
  protected $cellLocationType = TableCellLocation::class;
  protected $cellLocationDataType = '';
  /**
   * Whether to insert new columns to the right of the reference cell location.
   * - `True`: insert to the right. - `False`: insert to the left.
   *
   * @var bool
   */
  public $insertRight;
  /**
   * The number of columns to be inserted. Maximum 20 per request.
   *
   * @var int
   */
  public $number;
  /**
   * The table to insert columns into.
   *
   * @var string
   */
  public $tableObjectId;

  /**
   * The reference table cell location from which columns will be inserted. A
   * new column will be inserted to the left (or right) of the column where the
   * reference cell is. If the reference cell is a merged cell, a new column
   * will be inserted to the left (or right) of the merged cell.
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
   * Whether to insert new columns to the right of the reference cell location.
   * - `True`: insert to the right. - `False`: insert to the left.
   *
   * @param bool $insertRight
   */
  public function setInsertRight($insertRight)
  {
    $this->insertRight = $insertRight;
  }
  /**
   * @return bool
   */
  public function getInsertRight()
  {
    return $this->insertRight;
  }
  /**
   * The number of columns to be inserted. Maximum 20 per request.
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
   * The table to insert columns into.
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
class_alias(InsertTableColumnsRequest::class, 'Google_Service_Slides_InsertTableColumnsRequest');
