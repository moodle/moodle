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

class NestingLevel extends \Google\Model
{
  /**
   * The bullet alignment is unspecified.
   */
  public const BULLET_ALIGNMENT_BULLET_ALIGNMENT_UNSPECIFIED = 'BULLET_ALIGNMENT_UNSPECIFIED';
  /**
   * The bullet is aligned to the start of the space allotted for rendering the
   * bullet. Left-aligned for LTR text, right-aligned otherwise.
   */
  public const BULLET_ALIGNMENT_START = 'START';
  /**
   * The bullet is aligned to the center of the space allotted for rendering the
   * bullet.
   */
  public const BULLET_ALIGNMENT_CENTER = 'CENTER';
  /**
   * The bullet is aligned to the end of the space allotted for rendering the
   * bullet. Right-aligned for LTR text, left-aligned otherwise.
   */
  public const BULLET_ALIGNMENT_END = 'END';
  /**
   * The glyph type is unspecified or unsupported.
   */
  public const GLYPH_TYPE_GLYPH_TYPE_UNSPECIFIED = 'GLYPH_TYPE_UNSPECIFIED';
  /**
   * An empty string.
   */
  public const GLYPH_TYPE_NONE = 'NONE';
  /**
   * A number, like `1`, `2`, or `3`.
   */
  public const GLYPH_TYPE_DECIMAL = 'DECIMAL';
  /**
   * A number where single digit numbers are prefixed with a zero, like `01`,
   * `02`, or `03`. Numbers with more than one digit are not prefixed with a
   * zero.
   */
  public const GLYPH_TYPE_ZERO_DECIMAL = 'ZERO_DECIMAL';
  /**
   * An uppercase letter, like `A`, `B`, or `C`.
   */
  public const GLYPH_TYPE_UPPER_ALPHA = 'UPPER_ALPHA';
  /**
   * A lowercase letter, like `a`, `b`, or `c`.
   */
  public const GLYPH_TYPE_ALPHA = 'ALPHA';
  /**
   * An uppercase Roman numeral, like `I`, `II`, or `III`.
   */
  public const GLYPH_TYPE_UPPER_ROMAN = 'UPPER_ROMAN';
  /**
   * A lowercase Roman numeral, like `i`, `ii`, or `iii`.
   */
  public const GLYPH_TYPE_ROMAN = 'ROMAN';
  /**
   * The alignment of the bullet within the space allotted for rendering the
   * bullet.
   *
   * @var string
   */
  public $bulletAlignment;
  /**
   * The format string used by bullets at this level of nesting. The glyph
   * format contains one or more placeholders, and these placeholders are
   * replaced with the appropriate values depending on the glyph_type or
   * glyph_symbol. The placeholders follow the pattern `%[nesting_level]`.
   * Furthermore, placeholders can have prefixes and suffixes. Thus, the glyph
   * format follows the pattern `%[nesting_level]`. Note that the prefix and
   * suffix are optional and can be arbitrary strings. For example, the glyph
   * format `%0.` indicates that the rendered glyph will replace the placeholder
   * with the corresponding glyph for nesting level 0 followed by a period as
   * the suffix. So a list with a glyph type of UPPER_ALPHA and glyph format
   * `%0.` at nesting level 0 will result in a list with rendered glyphs `A.`
   * `B.` `C.` The glyph format can contain placeholders for the current nesting
   * level as well as placeholders for parent nesting levels. For example, a
   * list can have a glyph format of `%0.` at nesting level 0 and a glyph format
   * of `%0.%1.` at nesting level 1. Assuming both nesting levels have DECIMAL
   * glyph types, this would result in a list with rendered glyphs `1.` `2.` `
   * 2.1.` ` 2.2.` `3.` For nesting levels that are ordered, the string that
   * replaces a placeholder in the glyph format for a particular paragraph
   * depends on the paragraph's order within the list.
   *
   * @var string
   */
  public $glyphFormat;
  /**
   * A custom glyph symbol used by bullets when paragraphs at this level of
   * nesting is unordered. The glyph symbol replaces placeholders within the
   * glyph_format. For example, if the glyph_symbol is the solid circle
   * corresponding to Unicode U+25cf code point and the glyph_format is `%0`,
   * the rendered glyph would be the solid circle.
   *
   * @var string
   */
  public $glyphSymbol;
  /**
   * The type of glyph used by bullets when paragraphs at this level of nesting
   * is ordered. The glyph type determines the type of glyph used to replace
   * placeholders within the glyph_format when paragraphs at this level of
   * nesting are ordered. For example, if the nesting level is 0, the
   * glyph_format is `%0.` and the glyph type is DECIMAL, then the rendered
   * glyph would replace the placeholder `%0` in the glyph format with a number
   * corresponding to the list item's order within the list.
   *
   * @var string
   */
  public $glyphType;
  protected $indentFirstLineType = Dimension::class;
  protected $indentFirstLineDataType = '';
  protected $indentStartType = Dimension::class;
  protected $indentStartDataType = '';
  /**
   * The number of the first list item at this nesting level. A value of 0 is
   * treated as a value of 1 for lettered lists and Roman numeral lists. For
   * values of both 0 and 1, lettered and Roman numeral lists will begin at `a`
   * and `i` respectively. This value is ignored for nesting levels with
   * unordered glyphs.
   *
   * @var int
   */
  public $startNumber;
  protected $textStyleType = TextStyle::class;
  protected $textStyleDataType = '';

  /**
   * The alignment of the bullet within the space allotted for rendering the
   * bullet.
   *
   * Accepted values: BULLET_ALIGNMENT_UNSPECIFIED, START, CENTER, END
   *
   * @param self::BULLET_ALIGNMENT_* $bulletAlignment
   */
  public function setBulletAlignment($bulletAlignment)
  {
    $this->bulletAlignment = $bulletAlignment;
  }
  /**
   * @return self::BULLET_ALIGNMENT_*
   */
  public function getBulletAlignment()
  {
    return $this->bulletAlignment;
  }
  /**
   * The format string used by bullets at this level of nesting. The glyph
   * format contains one or more placeholders, and these placeholders are
   * replaced with the appropriate values depending on the glyph_type or
   * glyph_symbol. The placeholders follow the pattern `%[nesting_level]`.
   * Furthermore, placeholders can have prefixes and suffixes. Thus, the glyph
   * format follows the pattern `%[nesting_level]`. Note that the prefix and
   * suffix are optional and can be arbitrary strings. For example, the glyph
   * format `%0.` indicates that the rendered glyph will replace the placeholder
   * with the corresponding glyph for nesting level 0 followed by a period as
   * the suffix. So a list with a glyph type of UPPER_ALPHA and glyph format
   * `%0.` at nesting level 0 will result in a list with rendered glyphs `A.`
   * `B.` `C.` The glyph format can contain placeholders for the current nesting
   * level as well as placeholders for parent nesting levels. For example, a
   * list can have a glyph format of `%0.` at nesting level 0 and a glyph format
   * of `%0.%1.` at nesting level 1. Assuming both nesting levels have DECIMAL
   * glyph types, this would result in a list with rendered glyphs `1.` `2.` `
   * 2.1.` ` 2.2.` `3.` For nesting levels that are ordered, the string that
   * replaces a placeholder in the glyph format for a particular paragraph
   * depends on the paragraph's order within the list.
   *
   * @param string $glyphFormat
   */
  public function setGlyphFormat($glyphFormat)
  {
    $this->glyphFormat = $glyphFormat;
  }
  /**
   * @return string
   */
  public function getGlyphFormat()
  {
    return $this->glyphFormat;
  }
  /**
   * A custom glyph symbol used by bullets when paragraphs at this level of
   * nesting is unordered. The glyph symbol replaces placeholders within the
   * glyph_format. For example, if the glyph_symbol is the solid circle
   * corresponding to Unicode U+25cf code point and the glyph_format is `%0`,
   * the rendered glyph would be the solid circle.
   *
   * @param string $glyphSymbol
   */
  public function setGlyphSymbol($glyphSymbol)
  {
    $this->glyphSymbol = $glyphSymbol;
  }
  /**
   * @return string
   */
  public function getGlyphSymbol()
  {
    return $this->glyphSymbol;
  }
  /**
   * The type of glyph used by bullets when paragraphs at this level of nesting
   * is ordered. The glyph type determines the type of glyph used to replace
   * placeholders within the glyph_format when paragraphs at this level of
   * nesting are ordered. For example, if the nesting level is 0, the
   * glyph_format is `%0.` and the glyph type is DECIMAL, then the rendered
   * glyph would replace the placeholder `%0` in the glyph format with a number
   * corresponding to the list item's order within the list.
   *
   * Accepted values: GLYPH_TYPE_UNSPECIFIED, NONE, DECIMAL, ZERO_DECIMAL,
   * UPPER_ALPHA, ALPHA, UPPER_ROMAN, ROMAN
   *
   * @param self::GLYPH_TYPE_* $glyphType
   */
  public function setGlyphType($glyphType)
  {
    $this->glyphType = $glyphType;
  }
  /**
   * @return self::GLYPH_TYPE_*
   */
  public function getGlyphType()
  {
    return $this->glyphType;
  }
  /**
   * The amount of indentation for the first line of paragraphs at this level of
   * nesting.
   *
   * @param Dimension $indentFirstLine
   */
  public function setIndentFirstLine(Dimension $indentFirstLine)
  {
    $this->indentFirstLine = $indentFirstLine;
  }
  /**
   * @return Dimension
   */
  public function getIndentFirstLine()
  {
    return $this->indentFirstLine;
  }
  /**
   * The amount of indentation for paragraphs at this level of nesting. Applied
   * to the side that corresponds to the start of the text, based on the
   * paragraph's content direction.
   *
   * @param Dimension $indentStart
   */
  public function setIndentStart(Dimension $indentStart)
  {
    $this->indentStart = $indentStart;
  }
  /**
   * @return Dimension
   */
  public function getIndentStart()
  {
    return $this->indentStart;
  }
  /**
   * The number of the first list item at this nesting level. A value of 0 is
   * treated as a value of 1 for lettered lists and Roman numeral lists. For
   * values of both 0 and 1, lettered and Roman numeral lists will begin at `a`
   * and `i` respectively. This value is ignored for nesting levels with
   * unordered glyphs.
   *
   * @param int $startNumber
   */
  public function setStartNumber($startNumber)
  {
    $this->startNumber = $startNumber;
  }
  /**
   * @return int
   */
  public function getStartNumber()
  {
    return $this->startNumber;
  }
  /**
   * The text style of bullets at this level of nesting.
   *
   * @param TextStyle $textStyle
   */
  public function setTextStyle(TextStyle $textStyle)
  {
    $this->textStyle = $textStyle;
  }
  /**
   * @return TextStyle
   */
  public function getTextStyle()
  {
    return $this->textStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NestingLevel::class, 'Google_Service_Docs_NestingLevel');
