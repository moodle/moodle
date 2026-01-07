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

class SheetsChartReferenceSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to chart_id.
   *
   * @var bool
   */
  public $chartIdSuggested;
  /**
   * Indicates if there was a suggested change to spreadsheet_id.
   *
   * @var bool
   */
  public $spreadsheetIdSuggested;

  /**
   * Indicates if there was a suggested change to chart_id.
   *
   * @param bool $chartIdSuggested
   */
  public function setChartIdSuggested($chartIdSuggested)
  {
    $this->chartIdSuggested = $chartIdSuggested;
  }
  /**
   * @return bool
   */
  public function getChartIdSuggested()
  {
    return $this->chartIdSuggested;
  }
  /**
   * Indicates if there was a suggested change to spreadsheet_id.
   *
   * @param bool $spreadsheetIdSuggested
   */
  public function setSpreadsheetIdSuggested($spreadsheetIdSuggested)
  {
    $this->spreadsheetIdSuggested = $spreadsheetIdSuggested;
  }
  /**
   * @return bool
   */
  public function getSpreadsheetIdSuggested()
  {
    return $this->spreadsheetIdSuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SheetsChartReferenceSuggestionState::class, 'Google_Service_Docs_SheetsChartReferenceSuggestionState');
