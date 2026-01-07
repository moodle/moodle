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

class GridProperties extends \Google\Model
{
  /**
   * The number of columns in the grid.
   *
   * @var int
   */
  public $columnCount;
  /**
   * True if the column grouping control toggle is shown after the group.
   *
   * @var bool
   */
  public $columnGroupControlAfter;
  /**
   * The number of columns that are frozen in the grid.
   *
   * @var int
   */
  public $frozenColumnCount;
  /**
   * The number of rows that are frozen in the grid.
   *
   * @var int
   */
  public $frozenRowCount;
  /**
   * True if the grid isn't showing gridlines in the UI.
   *
   * @var bool
   */
  public $hideGridlines;
  /**
   * The number of rows in the grid.
   *
   * @var int
   */
  public $rowCount;
  /**
   * True if the row grouping control toggle is shown after the group.
   *
   * @var bool
   */
  public $rowGroupControlAfter;

  /**
   * The number of columns in the grid.
   *
   * @param int $columnCount
   */
  public function setColumnCount($columnCount)
  {
    $this->columnCount = $columnCount;
  }
  /**
   * @return int
   */
  public function getColumnCount()
  {
    return $this->columnCount;
  }
  /**
   * True if the column grouping control toggle is shown after the group.
   *
   * @param bool $columnGroupControlAfter
   */
  public function setColumnGroupControlAfter($columnGroupControlAfter)
  {
    $this->columnGroupControlAfter = $columnGroupControlAfter;
  }
  /**
   * @return bool
   */
  public function getColumnGroupControlAfter()
  {
    return $this->columnGroupControlAfter;
  }
  /**
   * The number of columns that are frozen in the grid.
   *
   * @param int $frozenColumnCount
   */
  public function setFrozenColumnCount($frozenColumnCount)
  {
    $this->frozenColumnCount = $frozenColumnCount;
  }
  /**
   * @return int
   */
  public function getFrozenColumnCount()
  {
    return $this->frozenColumnCount;
  }
  /**
   * The number of rows that are frozen in the grid.
   *
   * @param int $frozenRowCount
   */
  public function setFrozenRowCount($frozenRowCount)
  {
    $this->frozenRowCount = $frozenRowCount;
  }
  /**
   * @return int
   */
  public function getFrozenRowCount()
  {
    return $this->frozenRowCount;
  }
  /**
   * True if the grid isn't showing gridlines in the UI.
   *
   * @param bool $hideGridlines
   */
  public function setHideGridlines($hideGridlines)
  {
    $this->hideGridlines = $hideGridlines;
  }
  /**
   * @return bool
   */
  public function getHideGridlines()
  {
    return $this->hideGridlines;
  }
  /**
   * The number of rows in the grid.
   *
   * @param int $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return int
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
  /**
   * True if the row grouping control toggle is shown after the group.
   *
   * @param bool $rowGroupControlAfter
   */
  public function setRowGroupControlAfter($rowGroupControlAfter)
  {
    $this->rowGroupControlAfter = $rowGroupControlAfter;
  }
  /**
   * @return bool
   */
  public function getRowGroupControlAfter()
  {
    return $this->rowGroupControlAfter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GridProperties::class, 'Google_Service_Sheets_GridProperties');
