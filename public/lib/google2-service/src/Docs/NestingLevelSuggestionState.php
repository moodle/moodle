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

class NestingLevelSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to bullet_alignment.
   *
   * @var bool
   */
  public $bulletAlignmentSuggested;
  /**
   * Indicates if there was a suggested change to glyph_format.
   *
   * @var bool
   */
  public $glyphFormatSuggested;
  /**
   * Indicates if there was a suggested change to glyph_symbol.
   *
   * @var bool
   */
  public $glyphSymbolSuggested;
  /**
   * Indicates if there was a suggested change to glyph_type.
   *
   * @var bool
   */
  public $glyphTypeSuggested;
  /**
   * Indicates if there was a suggested change to indent_first_line.
   *
   * @var bool
   */
  public $indentFirstLineSuggested;
  /**
   * Indicates if there was a suggested change to indent_start.
   *
   * @var bool
   */
  public $indentStartSuggested;
  /**
   * Indicates if there was a suggested change to start_number.
   *
   * @var bool
   */
  public $startNumberSuggested;
  protected $textStyleSuggestionStateType = TextStyleSuggestionState::class;
  protected $textStyleSuggestionStateDataType = '';

  /**
   * Indicates if there was a suggested change to bullet_alignment.
   *
   * @param bool $bulletAlignmentSuggested
   */
  public function setBulletAlignmentSuggested($bulletAlignmentSuggested)
  {
    $this->bulletAlignmentSuggested = $bulletAlignmentSuggested;
  }
  /**
   * @return bool
   */
  public function getBulletAlignmentSuggested()
  {
    return $this->bulletAlignmentSuggested;
  }
  /**
   * Indicates if there was a suggested change to glyph_format.
   *
   * @param bool $glyphFormatSuggested
   */
  public function setGlyphFormatSuggested($glyphFormatSuggested)
  {
    $this->glyphFormatSuggested = $glyphFormatSuggested;
  }
  /**
   * @return bool
   */
  public function getGlyphFormatSuggested()
  {
    return $this->glyphFormatSuggested;
  }
  /**
   * Indicates if there was a suggested change to glyph_symbol.
   *
   * @param bool $glyphSymbolSuggested
   */
  public function setGlyphSymbolSuggested($glyphSymbolSuggested)
  {
    $this->glyphSymbolSuggested = $glyphSymbolSuggested;
  }
  /**
   * @return bool
   */
  public function getGlyphSymbolSuggested()
  {
    return $this->glyphSymbolSuggested;
  }
  /**
   * Indicates if there was a suggested change to glyph_type.
   *
   * @param bool $glyphTypeSuggested
   */
  public function setGlyphTypeSuggested($glyphTypeSuggested)
  {
    $this->glyphTypeSuggested = $glyphTypeSuggested;
  }
  /**
   * @return bool
   */
  public function getGlyphTypeSuggested()
  {
    return $this->glyphTypeSuggested;
  }
  /**
   * Indicates if there was a suggested change to indent_first_line.
   *
   * @param bool $indentFirstLineSuggested
   */
  public function setIndentFirstLineSuggested($indentFirstLineSuggested)
  {
    $this->indentFirstLineSuggested = $indentFirstLineSuggested;
  }
  /**
   * @return bool
   */
  public function getIndentFirstLineSuggested()
  {
    return $this->indentFirstLineSuggested;
  }
  /**
   * Indicates if there was a suggested change to indent_start.
   *
   * @param bool $indentStartSuggested
   */
  public function setIndentStartSuggested($indentStartSuggested)
  {
    $this->indentStartSuggested = $indentStartSuggested;
  }
  /**
   * @return bool
   */
  public function getIndentStartSuggested()
  {
    return $this->indentStartSuggested;
  }
  /**
   * Indicates if there was a suggested change to start_number.
   *
   * @param bool $startNumberSuggested
   */
  public function setStartNumberSuggested($startNumberSuggested)
  {
    $this->startNumberSuggested = $startNumberSuggested;
  }
  /**
   * @return bool
   */
  public function getStartNumberSuggested()
  {
    return $this->startNumberSuggested;
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
class_alias(NestingLevelSuggestionState::class, 'Google_Service_Docs_NestingLevelSuggestionState');
