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

class GoogleAppsCardV1OverflowMenuItem extends \Google\Model
{
  /**
   * Whether the menu option is disabled. Defaults to false.
   *
   * @var bool
   */
  public $disabled;
  protected $onClickType = GoogleAppsCardV1OnClick::class;
  protected $onClickDataType = '';
  protected $startIconType = GoogleAppsCardV1Icon::class;
  protected $startIconDataType = '';
  /**
   * Required. The text that identifies or describes the item to users.
   *
   * @var string
   */
  public $text;

  /**
   * Whether the menu option is disabled. Defaults to false.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Required. The action invoked when a menu option is selected. This `OnClick`
   * cannot contain an `OverflowMenu`, any specified `OverflowMenu` is dropped
   * and the menu item disabled.
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
   * The icon displayed in front of the text.
   *
   * @param GoogleAppsCardV1Icon $startIcon
   */
  public function setStartIcon(GoogleAppsCardV1Icon $startIcon)
  {
    $this->startIcon = $startIcon;
  }
  /**
   * @return GoogleAppsCardV1Icon
   */
  public function getStartIcon()
  {
    return $this->startIcon;
  }
  /**
   * Required. The text that identifies or describes the item to users.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1OverflowMenuItem::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1OverflowMenuItem');
