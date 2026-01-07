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

class GoogleAppsCardV1Chip extends \Google\Model
{
  /**
   * The alternative text that's used for accessibility. Set descriptive text
   * that lets users know what the chip does. For example, if a chip opens a
   * hyperlink, write: "Opens a new browser tab and navigates to the Google Chat
   * developer documentation at https://developers.google.com/workspace/chat".
   *
   * @var string
   */
  public $altText;
  /**
   * Whether the chip is in an inactive state and ignores user actions. Defaults
   * to `false`.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Whether the chip is in an active state and responds to user actions.
   * Defaults to `true`. Deprecated. Use `disabled` instead.
   *
   * @deprecated
   * @var bool
   */
  public $enabled;
  protected $iconType = GoogleAppsCardV1Icon::class;
  protected $iconDataType = '';
  /**
   * The text displayed inside the chip.
   *
   * @var string
   */
  public $label;
  protected $onClickType = GoogleAppsCardV1OnClick::class;
  protected $onClickDataType = '';

  /**
   * The alternative text that's used for accessibility. Set descriptive text
   * that lets users know what the chip does. For example, if a chip opens a
   * hyperlink, write: "Opens a new browser tab and navigates to the Google Chat
   * developer documentation at https://developers.google.com/workspace/chat".
   *
   * @param string $altText
   */
  public function setAltText($altText)
  {
    $this->altText = $altText;
  }
  /**
   * @return string
   */
  public function getAltText()
  {
    return $this->altText;
  }
  /**
   * Whether the chip is in an inactive state and ignores user actions. Defaults
   * to `false`.
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
   * Whether the chip is in an active state and responds to user actions.
   * Defaults to `true`. Deprecated. Use `disabled` instead.
   *
   * @deprecated
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * The icon image. If both `icon` and `text` are set, then the icon appears
   * before the text.
   *
   * @param GoogleAppsCardV1Icon $icon
   */
  public function setIcon(GoogleAppsCardV1Icon $icon)
  {
    $this->icon = $icon;
  }
  /**
   * @return GoogleAppsCardV1Icon
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * The text displayed inside the chip.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Optional. The action to perform when a user clicks the chip, such as
   * opening a hyperlink or running a custom function.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Chip::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Chip');
