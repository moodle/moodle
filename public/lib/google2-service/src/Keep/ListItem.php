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

namespace Google\Service\Keep;

class ListItem extends \Google\Collection
{
  protected $collection_key = 'childListItems';
  /**
   * Whether this item has been checked off or not.
   *
   * @var bool
   */
  public $checked;
  protected $childListItemsType = ListItem::class;
  protected $childListItemsDataType = 'array';
  protected $textType = TextContent::class;
  protected $textDataType = '';

  /**
   * Whether this item has been checked off or not.
   *
   * @param bool $checked
   */
  public function setChecked($checked)
  {
    $this->checked = $checked;
  }
  /**
   * @return bool
   */
  public function getChecked()
  {
    return $this->checked;
  }
  /**
   * If set, list of list items nested under this list item. Only one level of
   * nesting is allowed.
   *
   * @param ListItem[] $childListItems
   */
  public function setChildListItems($childListItems)
  {
    $this->childListItems = $childListItems;
  }
  /**
   * @return ListItem[]
   */
  public function getChildListItems()
  {
    return $this->childListItems;
  }
  /**
   * The text of this item. Length must be less than 1,000 characters.
   *
   * @param TextContent $text
   */
  public function setText(TextContent $text)
  {
    $this->text = $text;
  }
  /**
   * @return TextContent
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListItem::class, 'Google_Service_Keep_ListItem');
