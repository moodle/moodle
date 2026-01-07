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

namespace Google\Service\Slides;

class CreateParagraphBulletsRequest extends \Google\Model
{
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
   * A numbered list with `DIGIT`, `ALPHA` and `ROMAN` numeric glyphs for the
   * first 3 list nesting levels, followed by periods.
   */
  public const BULLET_PRESET_NUMBERED_DIGIT_ALPHA_ROMAN = 'NUMBERED_DIGIT_ALPHA_ROMAN';
  /**
   * A numbered list with `DIGIT`, `ALPHA` and `ROMAN` numeric glyphs for the
   * first 3 list nesting levels, followed by parenthesis.
   */
  public const BULLET_PRESET_NUMBERED_DIGIT_ALPHA_ROMAN_PARENS = 'NUMBERED_DIGIT_ALPHA_ROMAN_PARENS';
  /**
   * A numbered list with `DIGIT` numeric glyphs separated by periods, where
   * each nesting level uses the previous nesting level's glyph as a prefix. For
   * example: '1.', '1.1.', '2.', '2.2.'.
   */
  public const BULLET_PRESET_NUMBERED_DIGIT_NESTED = 'NUMBERED_DIGIT_NESTED';
  /**
   * A numbered list with `UPPERALPHA`, `ALPHA` and `ROMAN` numeric glyphs for
   * the first 3 list nesting levels, followed by periods.
   */
  public const BULLET_PRESET_NUMBERED_UPPERALPHA_ALPHA_ROMAN = 'NUMBERED_UPPERALPHA_ALPHA_ROMAN';
  /**
   * A numbered list with `UPPERROMAN`, `UPPERALPHA` and `DIGIT` numeric glyphs
   * for the first 3 list nesting levels, followed by periods.
   */
  public const BULLET_PRESET_NUMBERED_UPPERROMAN_UPPERALPHA_DIGIT = 'NUMBERED_UPPERROMAN_UPPERALPHA_DIGIT';
  /**
   * A numbered list with `ZERODIGIT`, `ALPHA` and `ROMAN` numeric glyphs for
   * the first 3 list nesting levels, followed by periods.
   */
  public const BULLET_PRESET_NUMBERED_ZERODIGIT_ALPHA_ROMAN = 'NUMBERED_ZERODIGIT_ALPHA_ROMAN';
  /**
   * The kinds of bullet glyphs to be used. Defaults to the
   * `BULLET_DISC_CIRCLE_SQUARE` preset.
   *
   * @var string
   */
  public $bulletPreset;
  protected $cellLocationType = TableCellLocation::class;
  protected $cellLocationDataType = '';
  /**
   * The object ID of the shape or table containing the text to add bullets to.
   *
   * @var string
   */
  public $objectId;
  protected $textRangeType = Range::class;
  protected $textRangeDataType = '';

  /**
   * The kinds of bullet glyphs to be used. Defaults to the
   * `BULLET_DISC_CIRCLE_SQUARE` preset.
   *
   * Accepted values: BULLET_DISC_CIRCLE_SQUARE, BULLET_DIAMONDX_ARROW3D_SQUARE,
   * BULLET_CHECKBOX, BULLET_ARROW_DIAMOND_DISC, BULLET_STAR_CIRCLE_SQUARE,
   * BULLET_ARROW3D_CIRCLE_SQUARE, BULLET_LEFTTRIANGLE_DIAMOND_DISC,
   * BULLET_DIAMONDX_HOLLOWDIAMOND_SQUARE, BULLET_DIAMOND_CIRCLE_SQUARE,
   * NUMBERED_DIGIT_ALPHA_ROMAN, NUMBERED_DIGIT_ALPHA_ROMAN_PARENS,
   * NUMBERED_DIGIT_NESTED, NUMBERED_UPPERALPHA_ALPHA_ROMAN,
   * NUMBERED_UPPERROMAN_UPPERALPHA_DIGIT, NUMBERED_ZERODIGIT_ALPHA_ROMAN
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
   * The optional table cell location if the text to be modified is in a table
   * cell. If present, the object_id must refer to a table.
   *
   * @param TableCellLocation $cellLocation
   */
  public function setCellLocation(TableCellLocation $cellLocation)
  {
    $this->cellLocation = $cellLocation;
  }
  /**
   * @return TableCellLocation
   */
  public function getCellLocation()
  {
    return $this->cellLocation;
  }
  /**
   * The object ID of the shape or table containing the text to add bullets to.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The range of text to apply the bullet presets to, based on TextElement
   * indexes.
   *
   * @param Range $textRange
   */
  public function setTextRange(Range $textRange)
  {
    $this->textRange = $textRange;
  }
  /**
   * @return Range
   */
  public function getTextRange()
  {
    return $this->textRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateParagraphBulletsRequest::class, 'Google_Service_Slides_CreateParagraphBulletsRequest');
