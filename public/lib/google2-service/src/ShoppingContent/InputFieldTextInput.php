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

class InputFieldTextInput extends \Google\Model
{
  /**
   * Default value. Will never be provided by the API.
   */
  public const TYPE_TEXT_INPUT_TYPE_UNSPECIFIED = 'TEXT_INPUT_TYPE_UNSPECIFIED';
  /**
   * Used when a short text is expected. The field can be rendered as a [text
   * field](https://www.w3.org/TR/2012/WD-html-
   * markup-20121025/input.text.html#input.text).
   */
  public const TYPE_GENERIC_SHORT_TEXT = 'GENERIC_SHORT_TEXT';
  /**
   * Used when a longer text is expected. The field should be rendered as a
   * [textarea](https://www.w3.org/TR/2012/WD-html-
   * markup-20121025/textarea.html#textarea).
   */
  public const TYPE_GENERIC_LONG_TEXT = 'GENERIC_LONG_TEXT';
  protected $additionalInfoType = TextWithTooltip::class;
  protected $additionalInfoDataType = '';
  /**
   * Text to be used as the [aria-
   * label](https://www.w3.org/TR/WCAG20-TECHS/ARIA14.html) for the input.
   *
   * @var string
   */
  public $ariaLabel;
  /**
   * Information about the required format. If present, it should be shown close
   * to the input field to help merchants to provide a correct value. For
   * example: "VAT numbers should be in a format similar to SK9999999999"
   *
   * @var string
   */
  public $formatInfo;
  /**
   * Type of the text input
   *
   * @var string
   */
  public $type;

  /**
   * Additional info regarding the field to be displayed to merchant. For
   * example, warning to not include personal identifiable information. There
   * may be more information to be shown in a tooltip.
   *
   * @param TextWithTooltip $additionalInfo
   */
  public function setAdditionalInfo(TextWithTooltip $additionalInfo)
  {
    $this->additionalInfo = $additionalInfo;
  }
  /**
   * @return TextWithTooltip
   */
  public function getAdditionalInfo()
  {
    return $this->additionalInfo;
  }
  /**
   * Text to be used as the [aria-
   * label](https://www.w3.org/TR/WCAG20-TECHS/ARIA14.html) for the input.
   *
   * @param string $ariaLabel
   */
  public function setAriaLabel($ariaLabel)
  {
    $this->ariaLabel = $ariaLabel;
  }
  /**
   * @return string
   */
  public function getAriaLabel()
  {
    return $this->ariaLabel;
  }
  /**
   * Information about the required format. If present, it should be shown close
   * to the input field to help merchants to provide a correct value. For
   * example: "VAT numbers should be in a format similar to SK9999999999"
   *
   * @param string $formatInfo
   */
  public function setFormatInfo($formatInfo)
  {
    $this->formatInfo = $formatInfo;
  }
  /**
   * @return string
   */
  public function getFormatInfo()
  {
    return $this->formatInfo;
  }
  /**
   * Type of the text input
   *
   * Accepted values: TEXT_INPUT_TYPE_UNSPECIFIED, GENERIC_SHORT_TEXT,
   * GENERIC_LONG_TEXT
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
class_alias(InputFieldTextInput::class, 'Google_Service_ShoppingContent_InputFieldTextInput');
