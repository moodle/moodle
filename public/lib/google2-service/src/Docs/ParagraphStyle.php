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

class ParagraphStyle extends \Google\Collection
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
   * The content direction is unspecified.
   */
  public const DIRECTION_CONTENT_DIRECTION_UNSPECIFIED = 'CONTENT_DIRECTION_UNSPECIFIED';
  /**
   * The content goes from left to right.
   */
  public const DIRECTION_LEFT_TO_RIGHT = 'LEFT_TO_RIGHT';
  /**
   * The content goes from right to left.
   */
  public const DIRECTION_RIGHT_TO_LEFT = 'RIGHT_TO_LEFT';
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
  protected $collection_key = 'tabStops';
  /**
   * The text alignment for this paragraph.
   *
   * @var string
   */
  public $alignment;
  /**
   * Whether to avoid widows and orphans for the paragraph. If unset, the value
   * is inherited from the parent.
   *
   * @var bool
   */
  public $avoidWidowAndOrphan;
  protected $borderBetweenType = ParagraphBorder::class;
  protected $borderBetweenDataType = '';
  protected $borderBottomType = ParagraphBorder::class;
  protected $borderBottomDataType = '';
  protected $borderLeftType = ParagraphBorder::class;
  protected $borderLeftDataType = '';
  protected $borderRightType = ParagraphBorder::class;
  protected $borderRightDataType = '';
  protected $borderTopType = ParagraphBorder::class;
  protected $borderTopDataType = '';
  /**
   * The text direction of this paragraph. If unset, the value defaults to
   * LEFT_TO_RIGHT since paragraph direction is not inherited.
   *
   * @var string
   */
  public $direction;
  /**
   * The heading ID of the paragraph. If empty, then this paragraph is not a
   * heading. This property is read-only.
   *
   * @var string
   */
  public $headingId;
  protected $indentEndType = Dimension::class;
  protected $indentEndDataType = '';
  protected $indentFirstLineType = Dimension::class;
  protected $indentFirstLineDataType = '';
  protected $indentStartType = Dimension::class;
  protected $indentStartDataType = '';
  /**
   * Whether all lines of the paragraph should be laid out on the same page or
   * column if possible. If unset, the value is inherited from the parent.
   *
   * @var bool
   */
  public $keepLinesTogether;
  /**
   * Whether at least a part of this paragraph should be laid out on the same
   * page or column as the next paragraph if possible. If unset, the value is
   * inherited from the parent.
   *
   * @var bool
   */
  public $keepWithNext;
  /**
   * The amount of space between lines, as a percentage of normal, where normal
   * is represented as 100.0. If unset, the value is inherited from the parent.
   *
   * @var float
   */
  public $lineSpacing;
  /**
   * The named style type of the paragraph. Since updating the named style type
   * affects other properties within ParagraphStyle, the named style type is
   * applied before the other properties are updated.
   *
   * @var string
   */
  public $namedStyleType;
  /**
   * Whether the current paragraph should always start at the beginning of a
   * page. If unset, the value is inherited from the parent. Attempting to
   * update page_break_before for paragraphs in unsupported regions, including
   * Table, Header, Footer and Footnote, can result in an invalid document state
   * that returns a 400 bad request error.
   *
   * @var bool
   */
  public $pageBreakBefore;
  protected $shadingType = Shading::class;
  protected $shadingDataType = '';
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
  protected $tabStopsType = TabStop::class;
  protected $tabStopsDataType = 'array';

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
   * Whether to avoid widows and orphans for the paragraph. If unset, the value
   * is inherited from the parent.
   *
   * @param bool $avoidWidowAndOrphan
   */
  public function setAvoidWidowAndOrphan($avoidWidowAndOrphan)
  {
    $this->avoidWidowAndOrphan = $avoidWidowAndOrphan;
  }
  /**
   * @return bool
   */
  public function getAvoidWidowAndOrphan()
  {
    return $this->avoidWidowAndOrphan;
  }
  /**
   * The border between this paragraph and the next and previous paragraphs. If
   * unset, the value is inherited from the parent. The between border is
   * rendered when the adjacent paragraph has the same border and indent
   * properties. Paragraph borders cannot be partially updated. When changing a
   * paragraph border, the new border must be specified in its entirety.
   *
   * @param ParagraphBorder $borderBetween
   */
  public function setBorderBetween(ParagraphBorder $borderBetween)
  {
    $this->borderBetween = $borderBetween;
  }
  /**
   * @return ParagraphBorder
   */
  public function getBorderBetween()
  {
    return $this->borderBetween;
  }
  /**
   * The border at the bottom of this paragraph. If unset, the value is
   * inherited from the parent. The bottom border is rendered when the paragraph
   * below has different border and indent properties. Paragraph borders cannot
   * be partially updated. When changing a paragraph border, the new border must
   * be specified in its entirety.
   *
   * @param ParagraphBorder $borderBottom
   */
  public function setBorderBottom(ParagraphBorder $borderBottom)
  {
    $this->borderBottom = $borderBottom;
  }
  /**
   * @return ParagraphBorder
   */
  public function getBorderBottom()
  {
    return $this->borderBottom;
  }
  /**
   * The border to the left of this paragraph. If unset, the value is inherited
   * from the parent. Paragraph borders cannot be partially updated. When
   * changing a paragraph border, the new border must be specified in its
   * entirety.
   *
   * @param ParagraphBorder $borderLeft
   */
  public function setBorderLeft(ParagraphBorder $borderLeft)
  {
    $this->borderLeft = $borderLeft;
  }
  /**
   * @return ParagraphBorder
   */
  public function getBorderLeft()
  {
    return $this->borderLeft;
  }
  /**
   * The border to the right of this paragraph. If unset, the value is inherited
   * from the parent. Paragraph borders cannot be partially updated. When
   * changing a paragraph border, the new border must be specified in its
   * entirety.
   *
   * @param ParagraphBorder $borderRight
   */
  public function setBorderRight(ParagraphBorder $borderRight)
  {
    $this->borderRight = $borderRight;
  }
  /**
   * @return ParagraphBorder
   */
  public function getBorderRight()
  {
    return $this->borderRight;
  }
  /**
   * The border at the top of this paragraph. If unset, the value is inherited
   * from the parent. The top border is rendered when the paragraph above has
   * different border and indent properties. Paragraph borders cannot be
   * partially updated. When changing a paragraph border, the new border must be
   * specified in its entirety.
   *
   * @param ParagraphBorder $borderTop
   */
  public function setBorderTop(ParagraphBorder $borderTop)
  {
    $this->borderTop = $borderTop;
  }
  /**
   * @return ParagraphBorder
   */
  public function getBorderTop()
  {
    return $this->borderTop;
  }
  /**
   * The text direction of this paragraph. If unset, the value defaults to
   * LEFT_TO_RIGHT since paragraph direction is not inherited.
   *
   * Accepted values: CONTENT_DIRECTION_UNSPECIFIED, LEFT_TO_RIGHT,
   * RIGHT_TO_LEFT
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
   * The heading ID of the paragraph. If empty, then this paragraph is not a
   * heading. This property is read-only.
   *
   * @param string $headingId
   */
  public function setHeadingId($headingId)
  {
    $this->headingId = $headingId;
  }
  /**
   * @return string
   */
  public function getHeadingId()
  {
    return $this->headingId;
  }
  /**
   * The amount of indentation for the paragraph on the side that corresponds to
   * the end of the text, based on the current paragraph direction. If unset,
   * the value is inherited from the parent.
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
   * The amount of indentation for the first line of the paragraph. If unset,
   * the value is inherited from the parent.
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
   * The amount of indentation for the paragraph on the side that corresponds to
   * the start of the text, based on the current paragraph direction. If unset,
   * the value is inherited from the parent.
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
   * Whether all lines of the paragraph should be laid out on the same page or
   * column if possible. If unset, the value is inherited from the parent.
   *
   * @param bool $keepLinesTogether
   */
  public function setKeepLinesTogether($keepLinesTogether)
  {
    $this->keepLinesTogether = $keepLinesTogether;
  }
  /**
   * @return bool
   */
  public function getKeepLinesTogether()
  {
    return $this->keepLinesTogether;
  }
  /**
   * Whether at least a part of this paragraph should be laid out on the same
   * page or column as the next paragraph if possible. If unset, the value is
   * inherited from the parent.
   *
   * @param bool $keepWithNext
   */
  public function setKeepWithNext($keepWithNext)
  {
    $this->keepWithNext = $keepWithNext;
  }
  /**
   * @return bool
   */
  public function getKeepWithNext()
  {
    return $this->keepWithNext;
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
   * The named style type of the paragraph. Since updating the named style type
   * affects other properties within ParagraphStyle, the named style type is
   * applied before the other properties are updated.
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
   * Whether the current paragraph should always start at the beginning of a
   * page. If unset, the value is inherited from the parent. Attempting to
   * update page_break_before for paragraphs in unsupported regions, including
   * Table, Header, Footer and Footnote, can result in an invalid document state
   * that returns a 400 bad request error.
   *
   * @param bool $pageBreakBefore
   */
  public function setPageBreakBefore($pageBreakBefore)
  {
    $this->pageBreakBefore = $pageBreakBefore;
  }
  /**
   * @return bool
   */
  public function getPageBreakBefore()
  {
    return $this->pageBreakBefore;
  }
  /**
   * The shading of the paragraph. If unset, the value is inherited from the
   * parent.
   *
   * @param Shading $shading
   */
  public function setShading(Shading $shading)
  {
    $this->shading = $shading;
  }
  /**
   * @return Shading
   */
  public function getShading()
  {
    return $this->shading;
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
  /**
   * A list of the tab stops for this paragraph. The list of tab stops is not
   * inherited. This property is read-only.
   *
   * @param TabStop[] $tabStops
   */
  public function setTabStops($tabStops)
  {
    $this->tabStops = $tabStops;
  }
  /**
   * @return TabStop[]
   */
  public function getTabStops()
  {
    return $this->tabStops;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParagraphStyle::class, 'Google_Service_Docs_ParagraphStyle');
