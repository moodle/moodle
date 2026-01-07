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

class DuplicateSheetRequest extends \Google\Model
{
  /**
   * The zero-based index where the new sheet should be inserted. The index of
   * all sheets after this are incremented.
   *
   * @var int
   */
  public $insertSheetIndex;
  /**
   * If set, the ID of the new sheet. If not set, an ID is chosen. If set, the
   * ID must not conflict with any existing sheet ID. If set, it must be non-
   * negative.
   *
   * @var int
   */
  public $newSheetId;
  /**
   * The name of the new sheet. If empty, a new name is chosen for you.
   *
   * @var string
   */
  public $newSheetName;
  /**
   * The sheet to duplicate. If the source sheet is of DATA_SOURCE type, its
   * backing DataSource is also duplicated and associated with the new copy of
   * the sheet. No data execution is triggered, the grid data of this sheet is
   * also copied over but only available after the batch request completes.
   *
   * @var int
   */
  public $sourceSheetId;

  /**
   * The zero-based index where the new sheet should be inserted. The index of
   * all sheets after this are incremented.
   *
   * @param int $insertSheetIndex
   */
  public function setInsertSheetIndex($insertSheetIndex)
  {
    $this->insertSheetIndex = $insertSheetIndex;
  }
  /**
   * @return int
   */
  public function getInsertSheetIndex()
  {
    return $this->insertSheetIndex;
  }
  /**
   * If set, the ID of the new sheet. If not set, an ID is chosen. If set, the
   * ID must not conflict with any existing sheet ID. If set, it must be non-
   * negative.
   *
   * @param int $newSheetId
   */
  public function setNewSheetId($newSheetId)
  {
    $this->newSheetId = $newSheetId;
  }
  /**
   * @return int
   */
  public function getNewSheetId()
  {
    return $this->newSheetId;
  }
  /**
   * The name of the new sheet. If empty, a new name is chosen for you.
   *
   * @param string $newSheetName
   */
  public function setNewSheetName($newSheetName)
  {
    $this->newSheetName = $newSheetName;
  }
  /**
   * @return string
   */
  public function getNewSheetName()
  {
    return $this->newSheetName;
  }
  /**
   * The sheet to duplicate. If the source sheet is of DATA_SOURCE type, its
   * backing DataSource is also duplicated and associated with the new copy of
   * the sheet. No data execution is triggered, the grid data of this sheet is
   * also copied over but only available after the batch request completes.
   *
   * @param int $sourceSheetId
   */
  public function setSourceSheetId($sourceSheetId)
  {
    $this->sourceSheetId = $sourceSheetId;
  }
  /**
   * @return int
   */
  public function getSourceSheetId()
  {
    return $this->sourceSheetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DuplicateSheetRequest::class, 'Google_Service_Sheets_DuplicateSheetRequest');
