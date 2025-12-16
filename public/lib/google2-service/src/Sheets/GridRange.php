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

class GridRange extends \Google\Model
{
  /**
   * The end column (exclusive) of the range, or not set if unbounded.
   *
   * @var int
   */
  public $endColumnIndex;
  /**
   * The end row (exclusive) of the range, or not set if unbounded.
   *
   * @var int
   */
  public $endRowIndex;
  /**
   * The sheet this range is on.
   *
   * @var int
   */
  public $sheetId;
  /**
   * The start column (inclusive) of the range, or not set if unbounded.
   *
   * @var int
   */
  public $startColumnIndex;
  /**
   * The start row (inclusive) of the range, or not set if unbounded.
   *
   * @var int
   */
  public $startRowIndex;

  /**
   * The end column (exclusive) of the range, or not set if unbounded.
   *
   * @param int $endColumnIndex
   */
  public function setEndColumnIndex($endColumnIndex)
  {
    $this->endColumnIndex = $endColumnIndex;
  }
  /**
   * @return int
   */
  public function getEndColumnIndex()
  {
    return $this->endColumnIndex;
  }
  /**
   * The end row (exclusive) of the range, or not set if unbounded.
   *
   * @param int $endRowIndex
   */
  public function setEndRowIndex($endRowIndex)
  {
    $this->endRowIndex = $endRowIndex;
  }
  /**
   * @return int
   */
  public function getEndRowIndex()
  {
    return $this->endRowIndex;
  }
  /**
   * The sheet this range is on.
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
  /**
   * The start column (inclusive) of the range, or not set if unbounded.
   *
   * @param int $startColumnIndex
   */
  public function setStartColumnIndex($startColumnIndex)
  {
    $this->startColumnIndex = $startColumnIndex;
  }
  /**
   * @return int
   */
  public function getStartColumnIndex()
  {
    return $this->startColumnIndex;
  }
  /**
   * The start row (inclusive) of the range, or not set if unbounded.
   *
   * @param int $startRowIndex
   */
  public function setStartRowIndex($startRowIndex)
  {
    $this->startRowIndex = $startRowIndex;
  }
  /**
   * @return int
   */
  public function getStartRowIndex()
  {
    return $this->startRowIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GridRange::class, 'Google_Service_Sheets_GridRange');
