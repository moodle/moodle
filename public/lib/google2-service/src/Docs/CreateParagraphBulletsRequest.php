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

class CreateParagraphBulletsRequest extends \Google\Model
{
  /**
   * The bullet glyph preset is unspecified.
   */
  public const BULLET_PRESET_BULLET_GLYPH_PRESET_UNSPECIFIED = 'BULLET_GLYPH_PRESET_UNSPECIFIED';
  /**
   * A bulleted list with a `DISC`, `CIRCLE` and `SQUARE` bullet glyph for the
   * first 3 list nesting levels.
   */
  public const BULLET_PRESET_BULLET_DISC_CIRCLE_SQUARE = 'BULLET_DISC_CIRCLE_SQUARE';
  /**
   * A bulleted list with a `DIAMONDX`, `ARROW3D` and `SQUARE` bullet glyph for
   * the first 3 list nesting levels.
   */
  public const BULLET_PRESET_BULLET_DIAMONDX_ARROW3D_SQUARE = 'BULLET_DIAMONDX_ARROW3D_SQUARE';
  /**
   * A bulleted list with `CHECKBOX` bullet glyphs for all list nesting levels.
   */
  public const BULLET_PRESET_BULLET_CHECKBOX = 'BULLET_CHECKBOX';
  /**
   * A bulleted list with a `ARROW`, `DIAMOND` and `DISC` bullet glyph for the
   * first 3 list nesting levels.
   */
  public const BULLET_PRESET_BULLET_ARROW_DIAMOND_DISC = 'BULLET_ARROW_DIAMOND_DISC';
  /**
   * A bulleted list with a `STAR`, `CIRCLE` and `SQUARE` bullet glyph for the
   * first 3 list nesting levels.
   */
  public const BULLET_PRESET_BULLET_STAR_CIRCLE_SQUARE = 'BULLET_STAR_CIRCLE_SQUARE';
  /**
   * A bulleted list with a `ARROW3D`, `CIRCLE` and `SQUARE` bullet glyph for
   * the first 3 list nesting levels.
   */
  public const BULLET_PRESET_BULLET_ARROW3D_CIRCLE_SQUARE = 'BULLET_ARROW3D_CIRCLE_SQUARE';
  /**
   * A bulleted list with a `LEFTTRIANGLE`, `DIAMOND` and `DISC` bullet glyph
   * for the first 3 list nesting levels.
   */
  public const BULLET_PRESET_BULLET_LEFTTRIANGLE_DIAMOND_DISC = 'BULLET_LEFTTRIANGLE_DIAMOND_DISC';
  /**
   * A bulleted list with a `DIAMONDX`, `HOLLOWDIAMOND` and `SQUARE` bullet
   * glyph for the first 3 list nesting levels.
   */
  public const BULLET_PRESET_BULLET_DIAMONDX_HOLLOWDIAMOND_SQUARE = 'BULLET_DIAMONDX_HOLLOWDIAMOND_SQUARE';
  /**
   * A bulleted list with a `DIAMOND`, `CIRCLE` and `SQUARE` bullet glyph for
   * the first 3 list nesting levels.
   */
  public const BULLET_PRESET_BULLET_DIAMOND_CIRCLE_SQUARE = 'BULLET_DIAMOND_CIRCLE_SQUARE';
  /**
   * A numbered list with `DECIMAL`, `ALPHA` and `ROMAN` numeric glyphs for the
   * first 3 list nesting levels, followed by periods.
   */
  public const BULLET_PRESET_NUMBERED_DECIMAL_ALPHA_ROMAN = 'NUMBERED_DECIMAL_ALPHA_ROMAN';
  /**
   * A numbered list with `DECIMAL`, `ALPHA` and `ROMAN` numeric glyphs for the
   * first 3 list nesting levels, followed by parenthesis.
   */
  public const BULLET_PRESET_NUMBERED_DECIMAL_ALPHA_ROMAN_PARENS = 'NUMBERED_DECIMAL_ALPHA_ROMAN_PARENS';
  /**
   * A numbered list with `DECIMAL` numeric glyphs separated by periods, where
   * each nesting level uses the previous nesting level's glyph as a prefix. For
   * example: '1.', '1.1.', '2.', '2.2.'.
   */
  public const BULLET_PRESET_NUMBERED_DECIMAL_NESTED = 'NUMBERED_DECIMAL_NESTED';
  /**
   * A numbered list with `UPPERALPHA`, `ALPHA` and `ROMAN` numeric glyphs for
   * the first 3 list nesting levels, followed by periods.
   */
  public const BULLET_PRESET_NUMBERED_UPPERALPHA_ALPHA_ROMAN = 'NUMBERED_UPPERALPHA_ALPHA_ROMAN';
  /**
   * A numbered list with `UPPERROMAN`, `UPPERALPHA` and `DECIMAL` numeric
   * glyphs for the first 3 list nesting levels, followed by periods.
   */
  public const BULLET_PRESET_NUMBERED_UPPERROMAN_UPPERALPHA_DECIMAL = 'NUMBERED_UPPERROMAN_UPPERALPHA_DECIMAL';
  /**
   * A numbered list with `ZERODECIMAL`, `ALPHA` and `ROMAN` numeric glyphs for
   * the first 3 list nesting levels, followed by periods.
   */
  public const BULLET_PRESET_NUMBERED_ZERODECIMAL_ALPHA_ROMAN = 'NUMBERED_ZERODECIMAL_ALPHA_ROMAN';
  /**
   * The kinds of bullet glyphs to be used.
   *
   * @var string
   */
  public $bulletPreset;
  protected $rangeType = Range::class;
  protected $rangeDataType = '';

  /**
   * The kinds of bullet glyphs to be used.
   *
   * Accepted values: BULLET_GLYPH_PRESET_UNSPECIFIED,
   * BULLET_DISC_CIRCLE_SQUARE, BULLET_DIAMONDX_ARROW3D_SQUARE, BULLET_CHECKBOX,
   * BULLET_ARROW_DIAMOND_DISC, BULLET_STAR_CIRCLE_SQUARE,
   * BULLET_ARROW3D_CIRCLE_SQUARE, BULLET_LEFTTRIANGLE_DIAMOND_DISC,
   * BULLET_DIAMONDX_HOLLOWDIAMOND_SQUARE, BULLET_DIAMOND_CIRCLE_SQUARE,
   * NUMBERED_DECIMAL_ALPHA_ROMAN, NUMBERED_DECIMAL_ALPHA_ROMAN_PARENS,
   * NUMBERED_DECIMAL_NESTED, NUMBERED_UPPERALPHA_ALPHA_ROMAN,
   * NUMBERED_UPPERROMAN_UPPERALPHA_DECIMAL, NUMBERED_ZERODECIMAL_ALPHA_ROMAN
   *
   * @param self::BULLET_PRESET_* $bulletPreset
   */
  public function setBulletPreset($bulletPreset)
  {
    $this->bulletPreset = $bulletPreset;
  }
  /**
   * @return self::BULLET_PRESET_*
   */
  public function getBulletPreset()
  {
    return $this->bulletPreset;
  }
  /**
   * The range to apply the bullet preset to.
   *
   * @param Range $range
   */
  public function setRange(Range $range)
  {
    $this->range = $range;
  }
  /**
   * @return Range
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateParagraphBulletsRequest::class, 'Google_Service_Docs_CreateParagraphBulletsRequest');
