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

namespace Google\Service\Docs;

class NamedStyleSuggestionState extends \Google\Model
{
  /**
   * The type of named style is unspecified.
   */
  public const NAMED_STYLE_TYPE_NAMED_STYLE_TYPE_UNSPECIFIED = 'NAMED_STYLE_TYPE_UNSPECIFIED';
  /**
   * Normal text.
   */
  public const NAMED_STYLE_TYPE_NORMAL_TEXT = 'NORMAL_TEXT';
  /**
   * Title.
   */
  public const NAMED_STYLE_TYPE_TITLE = 'TITLE';
  /**
   * Subtitle.
   */
  public const NAMED_STYLE_TYPE_SUBTITLE = 'SUBTITLE';
  /**
   * Heading 1.
   */
  public const NAMED_STYLE_TYPE_HEADING_1 = 'HEADING_1';
  /**
   * Heading 2.
   */
  public const NAMED_STYLE_TYPE_HEADING_2 = 'HEADING_2';
  /**
   * Heading 3.
   */
  public const NAMED_STYLE_TYPE_HEADING_3 = 'HEADING_3';
  /**
   * Heading 4.
   */
  public const NAMED_STYLE_TYPE_HEADING_4 = 'HEADING_4';
  /**
   * Heading 5.
   */
  public const NAMED_STYLE_TYPE_HEADING_5 = 'HEADING_5';
  /**
   * Heading 6.
   */
  public const NAMED_STYLE_TYPE_HEADING_6 = 'HEADING_6';
  /**
   * The named style type that this suggestion state corresponds to. This field
   * is provided as a convenience for matching the NamedStyleSuggestionState
   * with its corresponding NamedStyle.
   *
   * @var string
   */
  public $namedStyleType;
  protected $paragraphStyleSuggestionStateType = ParagraphStyleSuggestionState::class;
  protected $paragraphStyleSuggestionStateDataType = '';
  protected $textStyleSuggestionStateType = TextStyleSuggestionState::class;
  protected $textStyleSuggestionStateDataType = '';

  /**
   * The named style type that this suggestion state corresponds to. This field
   * is provided as a convenience for matching the NamedStyleSuggestionState
   * with its corresponding NamedStyle.
   *
   * Accepted values: NAMED_STYLE_TYPE_UNSPECIFIED, NORMAL_TEXT, TITLE,
   * SUBTITLE, HEADING_1, HEADING_2, HEADING_3, HEADING_4, HEADING_5, HEADING_6
   *
   * @param self::NAMED_STYLE_TYPE_* $namedStyleType
   */
  public function setNamedStyleType($namedStyleType)
  {
    $this->namedStyleType = $namedStyleType;
  }
  /**
   * @return self::NAMED_STYLE_TYPE_*
   */
  public function getNamedStyleType()
  {
    return $this->namedStyleType;
  }
  /**
   * A mask that indicates which of the fields in paragraph style have been
   * changed in this suggestion.
   *
   * @param ParagraphStyleSuggestionState $paragraphStyleSuggestionState
   */
  public function setParagraphStyleSuggestionState(ParagraphStyleSuggestionState $paragraphStyleSuggestionState)
  {
    $this->paragraphStyleSuggestionState = $paragraphStyleSuggestionState;
  }
  /**
   * @return ParagraphStyleSuggestionState
   */
  public function getParagraphStyleSuggestionState()
  {
    return $this->paragraphStyleSuggestionState;
  }
  /**
   * A mask that indicates which of the fields in text style have been changed
   * in this suggestion.
   *
   * @param TextStyleSuggestionState $textStyleSuggestionState
   */
  public function setTextStyleSuggestionState(TextStyleSuggestionState $textStyleSuggestionState)
  {
    $this->textStyleSuggestionState = $textStyleSuggestionState;
  }
  /**
   * @return TextStyleSuggestionState
   */
  public function getTextStyleSuggestionState()
  {
    return $this->textStyleSuggestionState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NamedStyleSuggestionState::class, 'Google_Service_Docs_NamedStyleSuggestionState');
