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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsRequestSheetsDestination extends \Google\Model
{
  /**
   * Optional. The title of the new sheet to write the feedback labels to.
   *
   * @var string
   */
  public $sheetTitle;
  /**
   * Required. The Google Sheets document to write the feedback labels to.
   * Retrieved from Google Sheets URI. E.g.
   * `https://docs.google.com/spreadsheets/d/1234567890` The spreadsheet must be
   * shared with the Insights P4SA. The spreadsheet ID written to will be
   * returned as `file_names` in the BulkDownloadFeedbackLabelsMetadata.
   *
   * @var string
   */
  public $spreadsheetUri;

  /**
   * Optional. The title of the new sheet to write the feedback labels to.
   *
   * @param string $sheetTitle
   */
  public function setSheetTitle($sheetTitle)
  {
    $this->sheetTitle = $sheetTitle;
  }
  /**
   * @return string
   */
  public function getSheetTitle()
  {
    return $this->sheetTitle;
  }
  /**
   * Required. The Google Sheets document to write the feedback labels to.
   * Retrieved from Google Sheets URI. E.g.
   * `https://docs.google.com/spreadsheets/d/1234567890` The spreadsheet must be
   * shared with the Insights P4SA. The spreadsheet ID written to will be
   * returned as `file_names` in the BulkDownloadFeedbackLabelsMetadata.
   *
   * @param string $spreadsheetUri
   */
  public function setSpreadsheetUri($spreadsheetUri)
  {
    $this->spreadsheetUri = $spreadsheetUri;
  }
  /**
   * @return string
   */
  public function getSpreadsheetUri()
  {
    return $this->spreadsheetUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsRequestSheetsDestination::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1BulkDownloadFeedbackLabelsRequestSheetsDestination');
