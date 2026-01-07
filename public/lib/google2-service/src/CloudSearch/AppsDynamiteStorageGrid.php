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

class AppsDynamiteStorageGrid extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $borderStyleType = AppsDynamiteStorageBorderStyle::class;
  protected $borderStyleDataType = '';
  /**
   * @var int
   */
  public $columnCount;
  protected $itemsType = AppsDynamiteStorageGridGridItem::class;
  protected $itemsDataType = 'array';
  protected $onClickType = AppsDynamiteStorageOnClick::class;
  protected $onClickDataType = '';
  /**
   * @var string
   */
  public $title;

  /**
   * @param AppsDynamiteStorageBorderStyle
   */
  public function setBorderStyle(AppsDynamiteStorageBorderStyle $borderStyle)
  {
    $this->borderStyle = $borderStyle;
  }
  /**
   * @return AppsDynamiteStorageBorderStyle
   */
  public function getBorderStyle()
  {
    return $this->borderStyle;
  }
  /**
   * @param int
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
   * @param AppsDynamiteStorageGridGridItem[]
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return AppsDynamiteStorageGridGridItem[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * @param AppsDynamiteStorageOnClick
   */
  public function setOnClick(AppsDynamiteStorageOnClick $onClick)
  {
    $this->onClick = $onClick;
  }
  /**
   * @return AppsDynamiteStorageOnClick
   */
  public function getOnClick()
  {
    return $this->onClick;
  }
  /**
   * @param string
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
class_alias(AppsDynamiteStorageGrid::class, 'Google_Service_CloudSearch_AppsDynamiteStorageGrid');
