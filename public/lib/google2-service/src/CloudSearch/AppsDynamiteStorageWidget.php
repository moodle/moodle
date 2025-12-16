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

namespace Google\Service\CloudSearch;

class AppsDynamiteStorageWidget extends \Google\Model
{
  protected $buttonListType = AppsDynamiteStorageButtonList::class;
  protected $buttonListDataType = '';
  protected $columnsType = AppsDynamiteStorageColumns::class;
  protected $columnsDataType = '';
  protected $dateTimePickerType = AppsDynamiteStorageDateTimePicker::class;
  protected $dateTimePickerDataType = '';
  protected $decoratedTextType = AppsDynamiteStorageDecoratedText::class;
  protected $decoratedTextDataType = '';
  protected $dividerType = AppsDynamiteStorageDivider::class;
  protected $dividerDataType = '';
  protected $gridType = AppsDynamiteStorageGrid::class;
  protected $gridDataType = '';
  /**
   * @var string
   */
  public $horizontalAlignment;
  protected $imageType = AppsDynamiteStorageImage::class;
  protected $imageDataType = '';
  protected $selectionInputType = AppsDynamiteStorageSelectionInput::class;
  protected $selectionInputDataType = '';
  protected $textInputType = AppsDynamiteStorageTextInput::class;
  protected $textInputDataType = '';
  protected $textParagraphType = AppsDynamiteStorageTextParagraph::class;
  protected $textParagraphDataType = '';

  /**
   * @param AppsDynamiteStorageButtonList
   */
  public function setButtonList(AppsDynamiteStorageButtonList $buttonList)
  {
    $this->buttonList = $buttonList;
  }
  /**
   * @return AppsDynamiteStorageButtonList
   */
  public function getButtonList()
  {
    return $this->buttonList;
  }
  /**
   * @param AppsDynamiteStorageColumns
   */
  public function setColumns(AppsDynamiteStorageColumns $columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return AppsDynamiteStorageColumns
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * @param AppsDynamiteStorageDateTimePicker
   */
  public function setDateTimePicker(AppsDynamiteStorageDateTimePicker $dateTimePicker)
  {
    $this->dateTimePicker = $dateTimePicker;
  }
  /**
   * @return AppsDynamiteStorageDateTimePicker
   */
  public function getDateTimePicker()
  {
    return $this->dateTimePicker;
  }
  /**
   * @param AppsDynamiteStorageDecoratedText
   */
  public function setDecoratedText(AppsDynamiteStorageDecoratedText $decoratedText)
  {
    $this->decoratedText = $decoratedText;
  }
  /**
   * @return AppsDynamiteStorageDecoratedText
   */
  public function getDecoratedText()
  {
    return $this->decoratedText;
  }
  /**
   * @param AppsDynamiteStorageDivider
   */
  public function setDivider(AppsDynamiteStorageDivider $divider)
  {
    $this->divider = $divider;
  }
  /**
   * @return AppsDynamiteStorageDivider
   */
  public function getDivider()
  {
    return $this->divider;
  }
  /**
   * @param AppsDynamiteStorageGrid
   */
  public function setGrid(AppsDynamiteStorageGrid $grid)
  {
    $this->grid = $grid;
  }
  /**
   * @return AppsDynamiteStorageGrid
   */
  public function getGrid()
  {
    return $this->grid;
  }
  /**
   * @param string
   */
  public function setHorizontalAlignment($horizontalAlignment)
  {
    $this->horizontalAlignment = $horizontalAlignment;
  }
  /**
   * @return string
   */
  public function getHorizontalAlignment()
  {
    return $this->horizontalAlignment;
  }
  /**
   * @param AppsDynamiteStorageImage
   */
  public function setImage(AppsDynamiteStorageImage $image)
  {
    $this->image = $image;
  }
  /**
   * @return AppsDynamiteStorageImage
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * @param AppsDynamiteStorageSelectionInput
   */
  public function setSelectionInput(AppsDynamiteStorageSelectionInput $selectionInput)
  {
    $this->selectionInput = $selectionInput;
  }
  /**
   * @return AppsDynamiteStorageSelectionInput
   */
  public function getSelectionInput()
  {
    return $this->selectionInput;
  }
  /**
   * @param AppsDynamiteStorageTextInput
   */
  public function setTextInput(AppsDynamiteStorageTextInput $textInput)
  {
    $this->textInput = $textInput;
  }
  /**
   * @return AppsDynamiteStorageTextInput
   */
  public function getTextInput()
  {
    return $this->textInput;
  }
  /**
   * @param AppsDynamiteStorageTextParagraph
   */
  public function setTextParagraph(AppsDynamiteStorageTextParagraph $textParagraph)
  {
    $this->textParagraph = $textParagraph;
  }
  /**
   * @return AppsDynamiteStorageTextParagraph
   */
  public function getTextParagraph()
  {
    return $this->textParagraph;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsDynamiteStorageWidget::class, 'Google_Service_CloudSearch_AppsDynamiteStorageWidget');
