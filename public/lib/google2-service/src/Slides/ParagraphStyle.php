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

class ParagraphStyle extends \Google\Model
{
  /**
   * The paragraph alignment is inherited from the parent.
   */
  public const ALIGNMENT_ALIGNMENT_UNSPECIFIED = 'ALIGNMENT_UNSPECIFIED';
  /**
   * The paragraph is aligned to the start of the line. Left-aligned for LTR
   * text, right-aligned otherwise.
   */
  public const ALIGNMENT_START = 'START';
  /**
   * The paragraph is centered.
   */
  public const ALIGNMENT_CENTER = 'CENTER';
  /**
   * The paragraph is aligned to the end of the line. Right-aligned for LTR
   * text, left-aligned otherwise.
   */
  public const ALIGNMENT_END = 'END';
  /**
   * The paragraph is justified.
   */
  public const ALIGNMENT_JUSTIFIED = 'JUSTIFIED';
  /**
   * The text direction is inherited from the parent.
   */
  public const DIRECTION_TEXT_DIRECTION_UNSPECIFIED = 'TEXT_DIRECTION_UNSPECIFIED';
  /**
   * The text goes from left to right.
   */
  public const DIRECTION_LEFT_TO_RIGHT = 'LEFT_TO_RIGHT';
  /**
   * The text goes from right to left.
   */
  public const DIRECTION_RIGHT_TO_LEFT = 'RIGHT_TO_LEFT';
  /**
   * The spacing mode is inherited from the parent.
   */
  public const SPACING_MODE_SPACING_MODE_UNSPECIFIED = 'SPACING_MODE_UNSPECIFIED';
  /**
   * Paragraph spacing is always rendered.
   */
  public const SPACING_MODE_NEVER_COLLAPSE = 'NEVER_COLLAPSE';
  /**
   * Paragraph spacing is skipped between list elements.
   */
  public const SPACING_MODE_COLLAPSE_LISTS = 'COLLAPSE_LISTS';
  /**
   * The text alignment for this paragraph.
   *
   * @var string
   */
  public $alignment;
  /**
   * The text direction of this paragraph. If unset, the value defaults to
   * LEFT_TO_RIGHT since text direction is not inherited.
   *
   * @var string
   */
  public $direction;
  protected $indentEndType = Dimension::class;
  protected $indentEndDataType = '';
  protected $indentFirstLineType = Dimension::class;
  protected $indentFirstLineDataType = '';
  protected $indentStartType = Dimension::class;
  protected $indentStartDataType = '';
  /**
   * The amount of space between lines, as a percentage of normal, where normal
   * is represented as 100.0. If unset, the value is inherited from the parent.
   *
   * @var float
   */
  public $lineSpacing;
  protected $spaceAboveType = Dimension::class;
  protected $spaceAboveDataType = '';
  protected $spaceBelowType = Dimension::class;
  protected $spaceBelowDataType = '';
  /**
   * The spacing mode for the paragraph.
   *
   * @var string
   */
  public $spacingMode;

  /**
   * The text alignment for this paragraph.
   *
   * Accepted values: ALIGNMENT_UNSPECIFIED, START, CENTER, END, JUSTIFIED
   *
   * @param self::ALIGNMENT_* $alignment
   */
  public function setAlignment($alignment)
  {
    $this->alignment = $alignment;
  }
  /**
   * @return self::ALIGNMENT_*
   */
  public function getAlignment()
  {
    return $this->alignment;
  }
  /**
   * The text direction of this paragraph. If unset, the value defaults to
   * LEFT_TO_RIGHT since text direction is not inherited.
   *
   * Accepted values: TEXT_DIRECTION_UNSPECIFIED, LEFT_TO_RIGHT, RIGHT_TO_LEFT
   *
   * @param self::DIRECTION_* $direction
   */
  public function setDirection($direction)
  {
    $this->direction = $direction;
  }
  /**
   * @return self::DIRECTION_*
   */
  public function getDirection()
  {
    return $this->direction;
  }
  /**
   * The amount indentation for the paragraph on the side that corresponds to
   * the end of the text, based on the current text direction. If unset, the
   * value is inherited from the parent.
   *
   * @param Dimension $indentEnd
   */
  public function setIndentEnd(Dimension $indentEnd)
  {
    $this->indentEnd = $indentEnd;
  }
  /**
   * @return Dimension
   */
  public function getIndentEnd()
  {
    return $this->indentEnd;
  }
  /**
   * The amount of indentation for the start of the first line of the paragraph.
   * If unset, the value is inherited from the parent.
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
   * The amount indentation for the paragraph on the side that corresponds to
   * the start of the text, based on the current text direction. If unset, the
   * value is inherited from the parent.
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
   * The amount of space between lines, as a percentage of normal, where normal
   * is represented as 100.0. If unset, the value is inherited from the parent.
   *
   * @param float $lineSpacing
   */
  public function setLineSpacing($lineSpacing)
  {
    $this->lineSpacing = $lineSpacing;
  }
  /**
   * @return float
   */
  public function getLineSpacing()
  {
    return $this->lineSpacing;
  }
  /**
   * The amount of extra space above the paragraph. If unset, the value is
   * inherited from the parent.
   *
   * @param Dimension $spaceAbove
   */
  public function setSpaceAbove(Dimension $spaceAbove)
  {
    $this->spaceAbove = $spaceAbove;
  }
  /**
   * @return Dimension
   */
  public function getSpaceAbove()
  {
    return $this->spaceAbove;
  }
  /**
   * The amount of extra space below the paragraph. If unset, the value is
   * inherited from the parent.
   *
   * @param Dimension $spaceBelow
   */
  public function setSpaceBelow(Dimension $spaceBelow)
  {
    $this->spaceBelow = $spaceBelow;
  }
  /**
   * @return Dimension
   */
  public function getSpaceBelow()
  {
    return $this->spaceBelow;
  }
  /**
   * The spacing mode for the paragraph.
   *
   * Accepted values: SPACING_MODE_UNSPECIFIED, NEVER_COLLAPSE, COLLAPSE_LISTS
   *
   * @param self::SPACING_MODE_* $spacingMode
   */
  public function setSpacingMode($spacingMode)
  {
    $this->spacingMode = $spacingMode;
  }
  /**
   * @return self::SPACING_MODE_*
   */
  public function getSpacingMode()
  {
    return $this->spacingMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParagraphStyle::class, 'Google_Service_Slides_ParagraphStyle');
