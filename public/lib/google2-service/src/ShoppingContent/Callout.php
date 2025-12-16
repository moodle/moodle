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

class Callout extends \Google\Model
{
  /**
   * Default value. Will never be provided by the API.
   */
  public const STYLE_HINT_CALLOUT_STYLE_HINT_UNSPECIFIED = 'CALLOUT_STYLE_HINT_UNSPECIFIED';
  /**
   * The most important type of information highlighting problems, like an
   * unsuccessful outcome of previously requested actions.
   */
  public const STYLE_HINT_ERROR = 'ERROR';
  /**
   * Information warning about pending problems, risks or deadlines.
   */
  public const STYLE_HINT_WARNING = 'WARNING';
  /**
   * Default severity for important information like pending status of
   * previously requested action or cooldown for re-review.
   */
  public const STYLE_HINT_INFO = 'INFO';
  protected $fullMessageType = TextWithTooltip::class;
  protected $fullMessageDataType = '';
  /**
   * Can be used to render messages with different severity in different styles.
   * Snippets off all types contain important information that should be
   * displayed to merchants.
   *
   * @var string
   */
  public $styleHint;

  /**
   * A full message that needs to be shown to the merchant.
   *
   * @param TextWithTooltip $fullMessage
   */
  public function setFullMessage(TextWithTooltip $fullMessage)
  {
    $this->fullMessage = $fullMessage;
  }
  /**
   * @return TextWithTooltip
   */
  public function getFullMessage()
  {
    return $this->fullMessage;
  }
  /**
   * Can be used to render messages with different severity in different styles.
   * Snippets off all types contain important information that should be
   * displayed to merchants.
   *
   * Accepted values: CALLOUT_STYLE_HINT_UNSPECIFIED, ERROR, WARNING, INFO
   *
   * @param self::STYLE_HINT_* $styleHint
   */
  public function setStyleHint($styleHint)
  {
    $this->styleHint = $styleHint;
  }
  /**
   * @return self::STYLE_HINT_*
   */
  public function getStyleHint()
  {
    return $this->styleHint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Callout::class, 'Google_Service_ShoppingContent_Callout');
