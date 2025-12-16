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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1IntentMessageTableCard extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $buttonsType = GoogleCloudDialogflowV2beta1IntentMessageBasicCardButton::class;
  protected $buttonsDataType = 'array';
  protected $columnPropertiesType = GoogleCloudDialogflowV2beta1IntentMessageColumnProperties::class;
  protected $columnPropertiesDataType = 'array';
  protected $imageType = GoogleCloudDialogflowV2beta1IntentMessageImage::class;
  protected $imageDataType = '';
  protected $rowsType = GoogleCloudDialogflowV2beta1IntentMessageTableCardRow::class;
  protected $rowsDataType = 'array';
  /**
   * Optional. Subtitle to the title.
   *
   * @var string
   */
  public $subtitle;
  /**
   * Required. Title of the card.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. List of buttons for the card.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageBasicCardButton[] $buttons
   */
  public function setButtons($buttons)
  {
    $this->buttons = $buttons;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageBasicCardButton[]
   */
  public function getButtons()
  {
    return $this->buttons;
  }
  /**
   * Optional. Display properties for the columns in this table.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageColumnProperties[] $columnProperties
   */
  public function setColumnProperties($columnProperties)
  {
    $this->columnProperties = $columnProperties;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageColumnProperties[]
   */
  public function getColumnProperties()
  {
    return $this->columnProperties;
  }
  /**
   * Optional. Image which should be displayed on the card.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageImage $image
   */
  public function setImage(GoogleCloudDialogflowV2beta1IntentMessageImage $image)
  {
    $this->image = $image;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageImage
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Optional. Rows in this table of data.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageTableCardRow[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageTableCardRow[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * Optional. Subtitle to the title.
   *
   * @param string $subtitle
   */
  public function setSubtitle($subtitle)
  {
    $this->subtitle = $subtitle;
  }
  /**
   * @return string
   */
  public function getSubtitle()
  {
    return $this->subtitle;
  }
  /**
   * Required. Title of the card.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessageTableCard::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageTableCard');
