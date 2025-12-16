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

namespace Google\Service\ShoppingContent;

class TextWithTooltip extends \Google\Model
{
  /**
   * Default value. Will never be provided by the API.
   */
  public const TOOLTIP_ICON_STYLE_TOOLTIP_ICON_STYLE_UNSPECIFIED = 'TOOLTIP_ICON_STYLE_UNSPECIFIED';
  /**
   * Used when the tooltip adds additional information to the context, the 'i'
   * can be used as an icon.
   */
  public const TOOLTIP_ICON_STYLE_INFO = 'INFO';
  /**
   * Used when the tooltip shows helpful information, the '?' can be used as an
   * icon.
   */
  public const TOOLTIP_ICON_STYLE_QUESTION = 'QUESTION';
  /**
   * Value of the tooltip as a simple text.
   *
   * @var string
   */
  public $simpleTooltipValue;
  /**
   * Value of the message as a simple text.
   *
   * @var string
   */
  public $simpleValue;
  /**
   * The suggested type of an icon for tooltip, if a tooltip is present.
   *
   * @var string
   */
  public $tooltipIconStyle;

  /**
   * Value of the tooltip as a simple text.
   *
   * @param string $simpleTooltipValue
   */
  public function setSimpleTooltipValue($simpleTooltipValue)
  {
    $this->simpleTooltipValue = $simpleTooltipValue;
  }
  /**
   * @return string
   */
  public function getSimpleTooltipValue()
  {
    return $this->simpleTooltipValue;
  }
  /**
   * Value of the message as a simple text.
   *
   * @param string $simpleValue
   */
  public function setSimpleValue($simpleValue)
  {
    $this->simpleValue = $simpleValue;
  }
  /**
   * @return string
   */
  public function getSimpleValue()
  {
    return $this->simpleValue;
  }
  /**
   * The suggested type of an icon for tooltip, if a tooltip is present.
   *
   * Accepted values: TOOLTIP_ICON_STYLE_UNSPECIFIED, INFO, QUESTION
   *
   * @param self::TOOLTIP_ICON_STYLE_* $tooltipIconStyle
   */
  public function setTooltipIconStyle($tooltipIconStyle)
  {
    $this->tooltipIconStyle = $tooltipIconStyle;
  }
  /**
   * @return self::TOOLTIP_ICON_STYLE_*
   */
  public function getTooltipIconStyle()
  {
    return $this->tooltipIconStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextWithTooltip::class, 'Google_Service_ShoppingContent_TextWithTooltip');
