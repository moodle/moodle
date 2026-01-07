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

class CreateSheetsChartRequest extends \Google\Model
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
  /**
   * The ID of the specific chart in the Google Sheets spreadsheet.
   *
   * @var int
   */
  public $chartId;
  protected $elementPropertiesType = PageElementProperties::class;
  protected $elementPropertiesDataType = '';
  /**
   * The mode with which the chart is linked to the source spreadsheet. When not
   * specified, the chart will be an image that is not linked.
   *
   * @var string
   */
  public $linkingMode;
  /**
   * A user-supplied object ID. If specified, the ID must be unique among all
   * pages and page elements in the presentation. The ID should start with a
   * word character [a-zA-Z0-9_] and then followed by any number of the
   * following characters [a-zA-Z0-9_-:]. The length of the ID should not be
   * less than 5 or greater than 50. If empty, a unique identifier will be
   * generated.
   *
   * @var string
   */
  public $objectId;
  /**
   * The ID of the Google Sheets spreadsheet that contains the chart. You might
   * need to add a resource key to the HTTP header for a subset of old files.
   * For more information, see [Access link-shared files using resource
   * keys](https://developers.google.com/drive/api/v3/resource-keys).
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
   * The element properties for the chart. When the aspect ratio of the provided
   * size does not match the chart aspect ratio, the chart is scaled and
   * centered with respect to the size in order to maintain aspect ratio. The
   * provided transform is applied after this operation.
   *
   * @param PageElementProperties $elementProperties
   */
  public function setElementProperties(PageElementProperties $elementProperties)
  {
    $this->elementProperties = $elementProperties;
  }
  /**
   * @return PageElementProperties
   */
  public function getElementProperties()
  {
    return $this->elementProperties;
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
   * A user-supplied object ID. If specified, the ID must be unique among all
   * pages and page elements in the presentation. The ID should start with a
   * word character [a-zA-Z0-9_] and then followed by any number of the
   * following characters [a-zA-Z0-9_-:]. The length of the ID should not be
   * less than 5 or greater than 50. If empty, a unique identifier will be
   * generated.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The ID of the Google Sheets spreadsheet that contains the chart. You might
   * need to add a resource key to the HTTP header for a subset of old files.
   * For more information, see [Access link-shared files using resource
   * keys](https://developers.google.com/drive/api/v3/resource-keys).
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
class_alias(CreateSheetsChartRequest::class, 'Google_Service_Slides_CreateSheetsChartRequest');
