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

class GoogleAppsCardV1DecoratedText extends \Google\Model
{
  /**
   * Unspecified type. Do not use.
   */
  public const START_ICON_VERTICAL_ALIGNMENT_VERTICAL_ALIGNMENT_UNSPECIFIED = 'VERTICAL_ALIGNMENT_UNSPECIFIED';
  /**
   * Alignment to the top position.
   */
  public const START_ICON_VERTICAL_ALIGNMENT_TOP = 'TOP';
  /**
   * Alignment to the middle position.
   */
  public const START_ICON_VERTICAL_ALIGNMENT_MIDDLE = 'MIDDLE';
  /**
   * Alignment to the bottom position.
   */
  public const START_ICON_VERTICAL_ALIGNMENT_BOTTOM = 'BOTTOM';
  /**
   * The text that appears below `text`. Always wraps.
   *
   * @var string
   */
  public $bottomLabel;
  protected $bottomLabelTextType = GoogleAppsCardV1TextParagraph::class;
  protected $bottomLabelTextDataType = '';
  protected $buttonType = GoogleAppsCardV1Button::class;
  protected $buttonDataType = '';
  protected $contentTextType = GoogleAppsCardV1TextParagraph::class;
  protected $contentTextDataType = '';
  protected $endIconType = GoogleAppsCardV1Icon::class;
  protected $endIconDataType = '';
  protected $iconType = GoogleAppsCardV1Icon::class;
  protected $iconDataType = '';
  protected $onClickType = GoogleAppsCardV1OnClick::class;
  protected $onClickDataType = '';
  protected $startIconType = GoogleAppsCardV1Icon::class;
  protected $startIconDataType = '';
  /**
   * Optional. Vertical alignment of the start icon. If not set, the icon will
   * be vertically centered. [Google Chat
   * apps](https://developers.google.com/workspace/chat):
   *
   * @var string
   */
  public $startIconVerticalAlignment;
  protected $switchControlType = GoogleAppsCardV1SwitchControl::class;
  protected $switchControlDataType = '';
  /**
   * Required. The primary text. Supports simple formatting. For more
   * information about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
   *
   * @var string
   */
  public $text;
  /**
   * The text that appears above `text`. Always truncates.
   *
   * @var string
   */
  public $topLabel;
  protected $topLabelTextType = GoogleAppsCardV1TextParagraph::class;
  protected $topLabelTextDataType = '';
  /**
   * The wrap text setting. If `true`, the text wraps and displays on multiple
   * lines. Otherwise, the text is truncated. Only applies to `text`, not
   * `topLabel` and `bottomLabel`.
   *
   * @var bool
   */
  public $wrapText;

  /**
   * The text that appears below `text`. Always wraps.
   *
   * @param string $bottomLabel
   */
  public function setBottomLabel($bottomLabel)
  {
    $this->bottomLabel = $bottomLabel;
  }
  /**
   * @return string
   */
  public function getBottomLabel()
  {
    return $this->bottomLabel;
  }
  /**
   * `TextParagraph` equivalent of `bottom_label`. Always wraps. Allows for more
   * complex formatting than `bottom_label`. [Google Chat
   * apps](https://developers.google.com/workspace/chat):
   *
   * @param GoogleAppsCardV1TextParagraph $bottomLabelText
   */
  public function setBottomLabelText(GoogleAppsCardV1TextParagraph $bottomLabelText)
  {
    $this->bottomLabelText = $bottomLabelText;
  }
  /**
   * @return GoogleAppsCardV1TextParagraph
   */
  public function getBottomLabelText()
  {
    return $this->bottomLabelText;
  }
  /**
   * A button that a user can click to trigger an action.
   *
   * @param GoogleAppsCardV1Button $button
   */
  public function setButton(GoogleAppsCardV1Button $button)
  {
    $this->button = $button;
  }
  /**
   * @return GoogleAppsCardV1Button
   */
  public function getButton()
  {
    return $this->button;
  }
  /**
   * `TextParagraph` equivalent of `text`. Allows for more complex formatting
   * than `text`. [Google Chat
   * apps](https://developers.google.com/workspace/chat):
   *
   * @param GoogleAppsCardV1TextParagraph $contentText
   */
  public function setContentText(GoogleAppsCardV1TextParagraph $contentText)
  {
    $this->contentText = $contentText;
  }
  /**
   * @return GoogleAppsCardV1TextParagraph
   */
  public function getContentText()
  {
    return $this->contentText;
  }
  /**
   * An icon displayed after the text. Supports [built-
   * in](https://developers.google.com/workspace/chat/format-
   * messages#builtinicons) and
   * [custom](https://developers.google.com/workspace/chat/format-
   * messages#customicons) icons.
   *
   * @param GoogleAppsCardV1Icon $endIcon
   */
  public function setEndIcon(GoogleAppsCardV1Icon $endIcon)
  {
    $this->endIcon = $endIcon;
  }
  /**
   * @return GoogleAppsCardV1Icon
   */
  public function getEndIcon()
  {
    return $this->endIcon;
  }
  /**
   * Deprecated in favor of `startIcon`.
   *
   * @deprecated
   * @param GoogleAppsCardV1Icon $icon
   */
  public function setIcon(GoogleAppsCardV1Icon $icon)
  {
    $this->icon = $icon;
  }
  /**
   * @deprecated
   * @return GoogleAppsCardV1Icon
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * This action is triggered when users click `topLabel` or `bottomLabel`.
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
   * Optional. Vertical alignment of the start icon. If not set, the icon will
   * be vertically centered. [Google Chat
   * apps](https://developers.google.com/workspace/chat):
   *
   * Accepted values: VERTICAL_ALIGNMENT_UNSPECIFIED, TOP, MIDDLE, BOTTOM
   *
   * @param self::START_ICON_VERTICAL_ALIGNMENT_* $startIconVerticalAlignment
   */
  public function setStartIconVerticalAlignment($startIconVerticalAlignment)
  {
    $this->startIconVerticalAlignment = $startIconVerticalAlignment;
  }
  /**
   * @return self::START_ICON_VERTICAL_ALIGNMENT_*
   */
  public function getStartIconVerticalAlignment()
  {
    return $this->startIconVerticalAlignment;
  }
  /**
   * A switch widget that a user can click to change its state and trigger an
   * action.
   *
   * @param GoogleAppsCardV1SwitchControl $switchControl
   */
  public function setSwitchControl(GoogleAppsCardV1SwitchControl $switchControl)
  {
    $this->switchControl = $switchControl;
  }
  /**
   * @return GoogleAppsCardV1SwitchControl
   */
  public function getSwitchControl()
  {
    return $this->switchControl;
  }
  /**
   * Required. The primary text. Supports simple formatting. For more
   * information about formatting text, see [Formatting text in Google Chat
   * apps](https://developers.google.com/workspace/chat/format-messages#card-
   * formatting) and [Formatting text in Google Workspace add-
   * ons](https://developers.google.com/apps-script/add-
   * ons/concepts/widgets#text_formatting).
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
   * The text that appears above `text`. Always truncates.
   *
   * @param string $topLabel
   */
  public function setTopLabel($topLabel)
  {
    $this->topLabel = $topLabel;
  }
  /**
   * @return string
   */
  public function getTopLabel()
  {
    return $this->topLabel;
  }
  /**
   * `TextParagraph` equivalent of `top_label`. Always truncates. Allows for
   * more complex formatting than `top_label`. [Google Chat
   * apps](https://developers.google.com/workspace/chat):
   *
   * @param GoogleAppsCardV1TextParagraph $topLabelText
   */
  public function setTopLabelText(GoogleAppsCardV1TextParagraph $topLabelText)
  {
    $this->topLabelText = $topLabelText;
  }
  /**
   * @return GoogleAppsCardV1TextParagraph
   */
  public function getTopLabelText()
  {
    return $this->topLabelText;
  }
  /**
   * The wrap text setting. If `true`, the text wraps and displays on multiple
   * lines. Otherwise, the text is truncated. Only applies to `text`, not
   * `topLabel` and `bottomLabel`.
   *
   * @param bool $wrapText
   */
  public function setWrapText($wrapText)
  {
    $this->wrapText = $wrapText;
  }
  /**
   * @return bool
   */
  public function getWrapText()
  {
    return $this->wrapText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1DecoratedText::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1DecoratedText');
