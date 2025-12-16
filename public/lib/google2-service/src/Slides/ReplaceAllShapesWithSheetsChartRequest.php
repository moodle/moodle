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

class ReplaceAllShapesWithSheetsChartRequest extends \Google\Collection
{
  /**
   * The chart is not associated with the source spreadsheet and cannot be
   * updated. A chart that is not linked will be inserted as an image.
   */
  public const LINKING_MODE_NOT_LINKED_IMAGE = 'NOT_LINKED_IMAGE';
  /**
   * Linking the chart allows it to be updated, and other collaborators will see
   * a link to the spreadsheet.
   */
  public const LINKING_MODE_LINKED = 'LINKED';
  protected $collection_key = 'pageObjectIds';
  /**
   * The ID of the specific chart in the Google Sheets spreadsheet.
   *
   * @var int
   */
  public $chartId;
  protected $containsTextType = SubstringMatchCriteria::class;
  protected $containsTextDataType = '';
  /**
   * The mode with which the chart is linked to the source spreadsheet. When not
   * specified, the chart will be an image that is not linked.
   *
   * @var string
   */
  public $linkingMode;
  /**
   * If non-empty, limits the matches to page elements only on the given pages.
   * Returns a 400 bad request error if given the page object ID of a notes page
   * or a notes master, or if a page with that object ID doesn't exist in the
   * presentation.
   *
   * @var string[]
   */
  public $pageObjectIds;
  /**
   * The ID of the Google Sheets spreadsheet that contains the chart.
   *
   * @var string
   */
  public $spreadsheetId;

  /**
   * The ID of the specific chart in the Google Sheets spreadsheet.
   *
   * @param int $chartId
   */
  public function setChartId($chartId)
  {
    $this->chartId = $chartId;
  }
  /**
   * @return int
   */
  public function getChartId()
  {
    return $this->chartId;
  }
  /**
   * The criteria that the shapes must match in order to be replaced. The
   * request will replace all of the shapes that contain the given text.
   *
   * @param SubstringMatchCriteria $containsText
   */
  public function setContainsText(SubstringMatchCriteria $containsText)
  {
    $this->containsText = $containsText;
  }
  /**
   * @return SubstringMatchCriteria
   */
  public function getContainsText()
  {
    return $this->containsText;
  }
  /**
   * The mode with which the chart is linked to the source spreadsheet. When not
   * specified, the chart will be an image that is not linked.
   *
   * Accepted values: NOT_LINKED_IMAGE, LINKED
   *
   * @param self::LINKING_MODE_* $linkingMode
   */
  public function setLinkingMode($linkingMode)
  {
    $this->linkingMode = $linkingMode;
  }
  /**
   * @return self::LINKING_MODE_*
   */
  public function getLinkingMode()
  {
    return $this->linkingMode;
  }
  /**
   * If non-empty, limits the matches to page elements only on the given pages.
   * Returns a 400 bad request error if given the page object ID of a notes page
   * or a notes master, or if a page with that object ID doesn't exist in the
   * presentation.
   *
   * @param string[] $pageObjectIds
   */
  public function setPageObjectIds($pageObjectIds)
  {
    $this->pageObjectIds = $pageObjectIds;
  }
  /**
   * @return string[]
   */
  public function getPageObjectIds()
  {
    return $this->pageObjectIds;
  }
  /**
   * The ID of the Google Sheets spreadsheet that contains the chart.
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
class_alias(ReplaceAllShapesWithSheetsChartRequest::class, 'Google_Service_Slides_ReplaceAllShapesWithSheetsChartRequest');
