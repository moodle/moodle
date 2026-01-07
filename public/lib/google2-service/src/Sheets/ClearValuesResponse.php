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

class ClearValuesResponse extends \Google\Model
{
  /**
   * The range (in A1 notation) that was cleared. (If the request was for an
   * unbounded range or a ranger larger than the bounds of the sheet, this will
   * be the actual range that was cleared, bounded to the sheet's limits.)
   *
   * @var string
   */
  public $clearedRange;
  /**
   * The spreadsheet the updates were applied to.
   *
   * @var string
   */
  public $spreadsheetId;

  /**
   * The range (in A1 notation) that was cleared. (If the request was for an
   * unbounded range or a ranger larger than the bounds of the sheet, this will
   * be the actual range that was cleared, bounded to the sheet's limits.)
   *
   * @param string $clearedRange
   */
  public function setClearedRange($clearedRange)
  {
    $this->clearedRange = $clearedRange;
  }
  /**
   * @return string
   */
  public function getClearedRange()
  {
    return $this->clearedRange;
  }
  /**
   * The spreadsheet the updates were applied to.
   *
   * @param string $spreadsheetId
   */
  public function setSpreadsheetId($spreadsheetId)
  {
    $this->spreadsheetId = $spreadsheetId;
  }
  /**
   * @return string
   */
  public function getSpreadsheetId()
  {
    return $this->spreadsheetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClearValuesResponse::class, 'Google_Service_Sheets_ClearValuesResponse');
