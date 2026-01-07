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

class BatchUpdateSpreadsheetResponse extends \Google\Collection
{
  protected $collection_key = 'replies';
  protected $repliesType = Response::class;
  protected $repliesDataType = 'array';
  /**
   * The spreadsheet the updates were applied to.
   *
   * @var string
   */
  public $spreadsheetId;
  protected $updatedSpreadsheetType = Spreadsheet::class;
  protected $updatedSpreadsheetDataType = '';

  /**
   * The reply of the updates. This maps 1:1 with the updates, although replies
   * to some requests may be empty.
   *
   * @param Response[] $replies
   */
  public function setReplies($replies)
  {
    $this->replies = $replies;
  }
  /**
   * @return Response[]
   */
  public function getReplies()
  {
    return $this->replies;
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
  /**
   * The spreadsheet after updates were applied. This is only set if
   * BatchUpdateSpreadsheetRequest.include_spreadsheet_in_response is `true`.
   *
   * @param Spreadsheet $updatedSpreadsheet
   */
  public function setUpdatedSpreadsheet(Spreadsheet $updatedSpreadsheet)
  {
    $this->updatedSpreadsheet = $updatedSpreadsheet;
  }
  /**
   * @return Spreadsheet
   */
  public function getUpdatedSpreadsheet()
  {
    return $this->updatedSpreadsheet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchUpdateSpreadsheetResponse::class, 'Google_Service_Sheets_BatchUpdateSpreadsheetResponse');
