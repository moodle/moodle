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

class GoogleAppsCardV1Grid extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $borderStyleType = GoogleAppsCardV1BorderStyle::class;
  protected $borderStyleDataType = '';
  /**
   * The number of columns to display in the grid. A default value is used if
   * this field isn't specified, and that default value is different depending
   * on where the grid is shown (dialog versus companion).
   *
   * @var int
   */
  public $columnCount;
  protected $itemsType = GoogleAppsCardV1GridItem::class;
  protected $itemsDataType = 'array';
  protected $onClickType = GoogleAppsCardV1OnClick::class;
  protected $onClickDataType = '';
  /**
   * The text that displays in the grid header.
   *
   * @var string
   */
  public $title;

  /**
   * The border style to apply to each grid item.
   *
   * @param GoogleAppsCardV1BorderStyle $borderStyle
   */
  public function setBorderStyle(GoogleAppsCardV1BorderStyle $borderStyle)
  {
    $this->borderStyle = $borderStyle;
  }
  /**
   * @return GoogleAppsCardV1BorderStyle
   */
  public function getBorderStyle()
  {
    return $this->borderStyle;
  }
  /**
   * The number of columns to display in the grid. A default value is used if
   * this field isn't specified, and that default value is different depending
   * on where the grid is shown (dialog versus companion).
   *
   * @param int $columnCount
   */
  public function setColumnCount($columnCount)
  {
    $this->columnCount = $columnCount;
  }
  /**
   * @return int
   */
  public function getColumnCount()
  {
    return $this->columnCount;
  }
  /**
   * The items to display in the grid.
   *
   * @param GoogleAppsCardV1GridItem[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return GoogleAppsCardV1GridItem[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * This callback is reused by each individual grid item, but with the item's
   * identifier and index in the items list added to the callback's parameters.
   *
   * @param GoogleAppsCardV1OnClick $onClick
   */
  public function setOnClick(GoogleAppsCardV1OnClick $onClick)
  {
    $this->onClick = $onClick;
  }
  /**
   * @return GoogleAppsCardV1OnClick
   */
  public function getOnClick()
  {
    return $this->onClick;
  }
  /**
   * The text that displays in the grid header.
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
class_alias(GoogleAppsCardV1Grid::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Grid');
