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

class GoogleAppsCardV1SelectionItem extends \Google\Model
{
  /**
   * For multiselect menus, a text description or label that's displayed below
   * the item's `text` field.
   *
   * @var string
   */
  public $bottomText;
  /**
   * Whether the item is selected by default. If the selection input only
   * accepts one value (such as for radio buttons or a dropdown menu), only set
   * this field for one item.
   *
   * @var bool
   */
  public $selected;
  /**
   * @var string
   */
  public $startIconUri;
  /**
   * The text that identifies or describes the item to users.
   *
   * @var string
   */
  public $text;
  /**
   * The value associated with this item. The client should use this as a form
   * input value. For details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @var string
   */
  public $value;

  /**
   * For multiselect menus, a text description or label that's displayed below
   * the item's `text` field.
   *
   * @param string $bottomText
   */
  public function setBottomText($bottomText)
  {
    $this->bottomText = $bottomText;
  }
  /**
   * @return string
   */
  public function getBottomText()
  {
    return $this->bottomText;
  }
  /**
   * Whether the item is selected by default. If the selection input only
   * accepts one value (such as for radio buttons or a dropdown menu), only set
   * this field for one item.
   *
   * @param bool $selected
   */
  public function setSelected($selected)
  {
    $this->selected = $selected;
  }
  /**
   * @return bool
   */
  public function getSelected()
  {
    return $this->selected;
  }
  /**
   * @param string $startIconUri
   */
  public function setStartIconUri($startIconUri)
  {
    $this->startIconUri = $startIconUri;
  }
  /**
   * @return string
   */
  public function getStartIconUri()
  {
    return $this->startIconUri;
  }
  /**
   * The text that identifies or describes the item to users.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * The value associated with this item. The client should use this as a form
   * input value. For details about working with form inputs, see [Receive form
   * data](https://developers.google.com/workspace/chat/read-form-data).
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1SelectionItem::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1SelectionItem');
