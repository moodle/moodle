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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1Widget extends \Google\Collection
{
  /**
   * Don't use. Unspecified.
   */
  public const HORIZONTAL_ALIGNMENT_HORIZONTAL_ALIGNMENT_UNSPECIFIED = 'HORIZONTAL_ALIGNMENT_UNSPECIFIED';
  /**
   * Default value. Aligns widgets to the start position of the column. For
   * left-to-right layouts, aligns to the left. For right-to-left layouts,
   * aligns to the right.
   */
  public const HORIZONTAL_ALIGNMENT_START = 'START';
  /**
   * Aligns widgets to the center of the column.
   */
  public const HORIZONTAL_ALIGNMENT_CENTER = 'CENTER';
  /**
   * Aligns widgets to the end position of the column. For left-to-right
   * layouts, aligns widgets to the right. For right-to-left layouts, aligns
   * widgets to the left.
   */
  public const HORIZONTAL_ALIGNMENT_END = 'END';
  /**
   * Unspecified visibility. Do not use.
   */
  public const VISIBILITY_VISIBILITY_UNSPECIFIED = 'VISIBILITY_UNSPECIFIED';
  /**
   * The UI element is visible.
   */
  public const VISIBILITY_VISIBLE = 'VISIBLE';
  /**
   * The UI element is hidden.
   */
  public const VISIBILITY_HIDDEN = 'HIDDEN';
  protected $collection_key = 'eventActions';
  protected $buttonListType = GoogleAppsCardV1ButtonList::class;
  protected $buttonListDataType = '';
  protected $carouselType = GoogleAppsCardV1Carousel::class;
  protected $carouselDataType = '';
  protected $chipListType = GoogleAppsCardV1ChipList::class;
  protected $chipListDataType = '';
  protected $columnsType = GoogleAppsCardV1Columns::class;
  protected $columnsDataType = '';
  protected $dateTimePickerType = GoogleAppsCardV1DateTimePicker::class;
  protected $dateTimePickerDataType = '';
  protected $decoratedTextType = GoogleAppsCardV1DecoratedText::class;
  protected $decoratedTextDataType = '';
  protected $dividerType = GoogleAppsCardV1Divider::class;
  protected $dividerDataType = '';
  protected $eventActionsType = GoogleAppsCardV1EventAction::class;
  protected $eventActionsDataType = 'array';
  protected $gridType = GoogleAppsCardV1Grid::class;
  protected $gridDataType = '';
  /**
   * Specifies whether widgets align to the left, right, or center of a column.
   *
   * @var string
   */
  public $horizontalAlignment;
  /**
   * A unique ID assigned to the widget that's used to identify the widget to be
   * mutated. The ID has a character limit of 64 characters and should be in the
   * format of `[a-zA-Z0-9-]+`. Available for Google Workspace add-ons that
   * extend Google Workspace Studio. Unavailable for Google Chat apps.
   *
   * @var string
   */
  public $id;
  protected $imageType = GoogleAppsCardV1Image::class;
  protected $imageDataType = '';
  protected $selectionInputType = GoogleAppsCardV1SelectionInput::class;
  protected $selectionInputDataType = '';
  protected $textInputType = GoogleAppsCardV1TextInput::class;
  protected $textInputDataType = '';
  protected $textParagraphType = GoogleAppsCardV1TextParagraph::class;
  protected $textParagraphDataType = '';
  /**
   * Specifies whether the widget is visible or hidden. The default value is
   * `VISIBLE`. Available for Google Workspace add-ons that extend Google
   * Workspace Studio. Unavailable for Google Chat apps.
   *
   * @var string
   */
  public $visibility;

  /**
   * A list of buttons. For example, the following JSON creates two buttons. The
   * first is a blue text button and the second is an image button that opens a
   * link: ``` "buttonList": { "buttons": [ { "text": "Edit", "color": { "red":
   * 0, "green": 0, "blue": 1, }, "disabled": true, }, { "icon": { "knownIcon":
   * "INVITE", "altText": "check calendar" }, "onClick": { "openLink": { "url":
   * "https://example.com/calendar" } } } ] } ```
   *
   * @param GoogleAppsCardV1ButtonList $buttonList
   */
  public function setButtonList(GoogleAppsCardV1ButtonList $buttonList)
  {
    $this->buttonList = $buttonList;
  }
  /**
   * @return GoogleAppsCardV1ButtonList
   */
  public function getButtonList()
  {
    return $this->buttonList;
  }
  /**
   * A carousel contains a collection of nested widgets. For example, this is a
   * JSON representation of a carousel that contains two text paragraphs. ``` {
   * "widgets": [ { "textParagraph": { "text": "First text paragraph in the
   * carousel." } }, { "textParagraph": { "text": "Second text paragraph in the
   * carousel." } } ] } ```
   *
   * @param GoogleAppsCardV1Carousel $carousel
   */
  public function setCarousel(GoogleAppsCardV1Carousel $carousel)
  {
    $this->carousel = $carousel;
  }
  /**
   * @return GoogleAppsCardV1Carousel
   */
  public function getCarousel()
  {
    return $this->carousel;
  }
  /**
   * A list of chips. For example, the following JSON creates two chips. The
   * first is a text chip and the second is an icon chip that opens a link: ```
   * "chipList": { "chips": [ { "text": "Edit", "disabled": true, }, { "icon": {
   * "knownIcon": "INVITE", "altText": "check calendar" }, "onClick": {
   * "openLink": { "url": "https://example.com/calendar" } } } ] } ```
   *
   * @param GoogleAppsCardV1ChipList $chipList
   */
  public function setChipList(GoogleAppsCardV1ChipList $chipList)
  {
    $this->chipList = $chipList;
  }
  /**
   * @return GoogleAppsCardV1ChipList
   */
  public function getChipList()
  {
    return $this->chipList;
  }
  /**
   * Displays up to 2 columns. To include more than 2 columns, or to use rows,
   * use the `Grid` widget. For example, the following JSON creates 2 columns
   * that each contain text paragraphs: ``` "columns": { "columnItems": [ {
   * "horizontalSizeStyle": "FILL_AVAILABLE_SPACE", "horizontalAlignment":
   * "CENTER", "verticalAlignment": "CENTER", "widgets": [ { "textParagraph": {
   * "text": "First column text paragraph" } } ] }, { "horizontalSizeStyle":
   * "FILL_AVAILABLE_SPACE", "horizontalAlignment": "CENTER",
   * "verticalAlignment": "CENTER", "widgets": [ { "textParagraph": { "text":
   * "Second column text paragraph" } } ] } ] } ```
   *
   * @param GoogleAppsCardV1Columns $columns
   */
  public function setColumns(GoogleAppsCardV1Columns $columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return GoogleAppsCardV1Columns
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Displays a widget that lets users input a date, time, or date and time. For
   * example, the following JSON creates a date time picker to schedule an
   * appointment: ``` "dateTimePicker": { "name": "appointment_time", "label":
   * "Book your appointment at:", "type": "DATE_AND_TIME", "valueMsEpoch":
   * 796435200000 } ```
   *
   * @param GoogleAppsCardV1DateTimePicker $dateTimePicker
   */
  public function setDateTimePicker(GoogleAppsCardV1DateTimePicker $dateTimePicker)
  {
    $this->dateTimePicker = $dateTimePicker;
  }
  /**
   * @return GoogleAppsCardV1DateTimePicker
   */
  public function getDateTimePicker()
  {
    return $this->dateTimePicker;
  }
  /**
   * Displays a decorated text item. For example, the following JSON creates a
   * decorated text widget showing email address: ``` "decoratedText": { "icon":
   * { "knownIcon": "EMAIL" }, "topLabel": "Email Address", "text":
   * "sasha@example.com", "bottomLabel": "This is a new Email address!",
   * "switchControl": { "name": "has_send_welcome_email_to_sasha", "selected":
   * false, "controlType": "CHECKBOX" } } ```
   *
   * @param GoogleAppsCardV1DecoratedText $decoratedText
   */
  public function setDecoratedText(GoogleAppsCardV1DecoratedText $decoratedText)
  {
    $this->decoratedText = $decoratedText;
  }
  /**
   * @return GoogleAppsCardV1DecoratedText
   */
  public function getDecoratedText()
  {
    return $this->decoratedText;
  }
  /**
   * Displays a horizontal line divider between widgets. For example, the
   * following JSON creates a divider: ``` "divider": { } ```
   *
   * @param GoogleAppsCardV1Divider $divider
   */
  public function setDivider(GoogleAppsCardV1Divider $divider)
  {
    $this->divider = $divider;
  }
  /**
   * @return GoogleAppsCardV1Divider
   */
  public function getDivider()
  {
    return $this->divider;
  }
  /**
   * Specifies the event actions that can be performed on the widget. Available
   * for Google Workspace add-ons that extend Google Workspace Studio.
   * Unavailable for Google Chat apps.
   *
   * @param GoogleAppsCardV1EventAction[] $eventActions
   */
  public function setEventActions($eventActions)
  {
    $this->eventActions = $eventActions;
  }
  /**
   * @return GoogleAppsCardV1EventAction[]
   */
  public function getEventActions()
  {
    return $this->eventActions;
  }
  /**
   * Displays a grid with a collection of items. A grid supports any number of
   * columns and items. The number of rows is determined by the upper bounds of
   * the number items divided by the number of columns. A grid with 10 items and
   * 2 columns has 5 rows. A grid with 11 items and 2 columns has 6 rows.
   * [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend): For example, the
   * following JSON creates a 2 column grid with a single item: ``` "grid": {
   * "title": "A fine collection of items", "columnCount": 2, "borderStyle": {
   * "type": "STROKE", "cornerRadius": 4 }, "items": [ { "image": { "imageUri":
   * "https://www.example.com/image.png", "cropStyle": { "type": "SQUARE" },
   * "borderStyle": { "type": "STROKE" } }, "title": "An item", "textAlignment":
   * "CENTER" } ], "onClick": { "openLink": { "url": "https://www.example.com" }
   * } } ```
   *
   * @param GoogleAppsCardV1Grid $grid
   */
  public function setGrid(GoogleAppsCardV1Grid $grid)
  {
    $this->grid = $grid;
  }
  /**
   * @return GoogleAppsCardV1Grid
   */
  public function getGrid()
  {
    return $this->grid;
  }
  /**
   * Specifies whether widgets align to the left, right, or center of a column.
   *
   * Accepted values: HORIZONTAL_ALIGNMENT_UNSPECIFIED, START, CENTER, END
   *
   * @param self::HORIZONTAL_ALIGNMENT_* $horizontalAlignment
   */
  public function setHorizontalAlignment($horizontalAlignment)
  {
    $this->horizontalAlignment = $horizontalAlignment;
  }
  /**
   * @return self::HORIZONTAL_ALIGNMENT_*
   */
  public function getHorizontalAlignment()
  {
    return $this->horizontalAlignment;
  }
  /**
   * A unique ID assigned to the widget that's used to identify the widget to be
   * mutated. The ID has a character limit of 64 characters and should be in the
   * format of `[a-zA-Z0-9-]+`. Available for Google Workspace add-ons that
   * extend Google Workspace Studio. Unavailable for Google Chat apps.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Displays an image. For example, the following JSON creates an image with
   * alternative text: ``` "image": { "imageUrl":
   * "https://developers.google.com/workspace/chat/images/quickstart-app-
   * avatar.png", "altText": "Chat app avatar" } ```
   *
   * @param GoogleAppsCardV1Image $image
   */
  public function setImage(GoogleAppsCardV1Image $image)
  {
    $this->image = $image;
  }
  /**
   * @return GoogleAppsCardV1Image
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Displays a selection control that lets users select items. Selection
   * controls can be checkboxes, radio buttons, switches, or dropdown menus. For
   * example, the following JSON creates a dropdown menu that lets users choose
   * a size: ``` "selectionInput": { "name": "size", "label": "Size" "type":
   * "DROPDOWN", "items": [ { "text": "S", "value": "small", "selected": false
   * }, { "text": "M", "value": "medium", "selected": true }, { "text": "L",
   * "value": "large", "selected": false }, { "text": "XL", "value":
   * "extra_large", "selected": false } ] } ```
   *
   * @param GoogleAppsCardV1SelectionInput $selectionInput
   */
  public function setSelectionInput(GoogleAppsCardV1SelectionInput $selectionInput)
  {
    $this->selectionInput = $selectionInput;
  }
  /**
   * @return GoogleAppsCardV1SelectionInput
   */
  public function getSelectionInput()
  {
    return $this->selectionInput;
  }
  /**
   * Displays a text box that users can type into. For example, the following
   * JSON creates a text input for an email address: ``` "textInput": { "name":
   * "mailing_address", "label": "Mailing Address" } ``` As another example, the
   * following JSON creates a text input for a programming language with static
   * suggestions: ``` "textInput": { "name": "preferred_programing_language",
   * "label": "Preferred Language", "initialSuggestions": { "items": [ { "text":
   * "C++" }, { "text": "Java" }, { "text": "JavaScript" }, { "text": "Python" }
   * ] } } ```
   *
   * @param GoogleAppsCardV1TextInput $textInput
   */
  public function setTextInput(GoogleAppsCardV1TextInput $textInput)
  {
    $this->textInput = $textInput;
  }
  /**
   * @return GoogleAppsCardV1TextInput
   */
  public function getTextInput()
  {
    return $this->textInput;
  }
  /**
   * Displays a text paragraph. Supports simple HTML formatted text. For more
   * information about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting). For example, the following JSON
   * creates a bolded text: ``` "textParagraph": { "text": " *bold text*" } ```
   *
   * @param GoogleAppsCardV1TextParagraph $textParagraph
   */
  public function setTextParagraph(GoogleAppsCardV1TextParagraph $textParagraph)
  {
    $this->textParagraph = $textParagraph;
  }
  /**
   * @return GoogleAppsCardV1TextParagraph
   */
  public function getTextParagraph()
  {
    return $this->textParagraph;
  }
  /**
   * Specifies whether the widget is visible or hidden. The default value is
   * `VISIBLE`. Available for Google Workspace add-ons that extend Google
   * Workspace Studio. Unavailable for Google Chat apps.
   *
   * Accepted values: VISIBILITY_UNSPECIFIED, VISIBLE, HIDDEN
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Widget::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Widget');
