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

class GoogleAppsCardV1Button extends \Google\Model
{
  /**
   * Don't use. Unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Outlined buttons are medium-emphasis buttons. They usually contain actions
   * that are important, but aren’t the primary action in a Chat app or an add-
   * on.
   */
  public const TYPE_OUTLINED = 'OUTLINED';
  /**
   * A filled button has a container with a solid color. It has the most visual
   * impact and is recommended for the important and primary action in a Chat
   * app or an add-on.
   */
  public const TYPE_FILLED = 'FILLED';
  /**
   * A filled tonal button is an alternative middle ground between filled and
   * outlined buttons. They’re useful in contexts where a lower-priority button
   * requires slightly more emphasis than an outline button would give.
   */
  public const TYPE_FILLED_TONAL = 'FILLED_TONAL';
  /**
   * A button does not have an invisible container in its default state. It is
   * often used for the lowest priority actions, especially when presenting
   * multiple options.
   */
  public const TYPE_BORDERLESS = 'BORDERLESS';
  /**
   * The alternative text that's used for accessibility. Set descriptive text
   * that lets users know what the button does. For example, if a button opens a
   * hyperlink, you might write: "Opens a new browser tab and navigates to the
   * Google Chat developer documentation at
   * https://developers.google.com/workspace/chat".
   *
   * @var string
   */
  public $altText;
  protected $colorType = Color::class;
  protected $colorDataType = '';
  /**
   * If `true`, the button is displayed in an inactive state and doesn't respond
   * to user actions.
   *
   * @var bool
   */
  public $disabled;
  protected $iconType = GoogleAppsCardV1Icon::class;
  protected $iconDataType = '';
  protected $onClickType = GoogleAppsCardV1OnClick::class;
  protected $onClickDataType = '';
  /**
   * The text displayed inside the button.
   *
   * @var string
   */
  public $text;
  /**
   * Optional. The type of a button. If unset, button type defaults to
   * `OUTLINED`. If the `color` field is set, the button type is forced to
   * `FILLED` and any value set for this field is ignored.
   *
   * @var string
   */
  public $type;

  /**
   * The alternative text that's used for accessibility. Set descriptive text
   * that lets users know what the button does. For example, if a button opens a
   * hyperlink, you might write: "Opens a new browser tab and navigates to the
   * Google Chat developer documentation at
   * https://developers.google.com/workspace/chat".
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
   * Optional. The color of the button. If set, the button `type` is set to
   * `FILLED` and the color of `text` and `icon` fields are set to a contrasting
   * color for readability. For example, if the button color is set to blue, any
   * text or icons in the button are set to white. To set the button color,
   * specify a value for the `red`, `green`, and `blue` fields. The value must
   * be a float number between 0 and 1 based on the RGB color value, where `0`
   * (0/255) represents the absence of color and `1` (255/255) represents the
   * maximum intensity of the color. For example, the following sets the color
   * to red at its maximum intensity: ``` "color": { "red": 1, "green": 0,
   * "blue": 0, } ``` The `alpha` field is unavailable for button color. If
   * specified, this field is ignored.
   *
   * @param Color $color
   */
  public function setColor(Color $color)
  {
    $this->color = $color;
  }
  /**
   * @return Color
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * If `true`, the button is displayed in an inactive state and doesn't respond
   * to user actions.
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
   * An icon displayed inside the button. If both `icon` and `text` are set,
   * then the icon appears before the text.
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
   * Required. The action to perform when a user clicks the button, such as
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
  /**
   * The text displayed inside the button.
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
   * Optional. The type of a button. If unset, button type defaults to
   * `OUTLINED`. If the `color` field is set, the button type is forced to
   * `FILLED` and any value set for this field is ignored.
   *
   * Accepted values: TYPE_UNSPECIFIED, OUTLINED, FILLED, FILLED_TONAL,
   * BORDERLESS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Button::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Button');
