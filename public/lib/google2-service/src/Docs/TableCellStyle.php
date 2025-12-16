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

class TableCellStyle extends \Google\Model
{
  /**
   * An unspecified content alignment. The content alignment is inherited from
   * the parent if one exists.
   */
  public const CONTENT_ALIGNMENT_CONTENT_ALIGNMENT_UNSPECIFIED = 'CONTENT_ALIGNMENT_UNSPECIFIED';
  /**
   * An unsupported content alignment.
   */
  public const CONTENT_ALIGNMENT_CONTENT_ALIGNMENT_UNSUPPORTED = 'CONTENT_ALIGNMENT_UNSUPPORTED';
  /**
   * An alignment that aligns the content to the top of the content holder.
   * Corresponds to ECMA-376 ST_TextAnchoringType 't'.
   */
  public const CONTENT_ALIGNMENT_TOP = 'TOP';
  /**
   * An alignment that aligns the content to the middle of the content holder.
   * Corresponds to ECMA-376 ST_TextAnchoringType 'ctr'.
   */
  public const CONTENT_ALIGNMENT_MIDDLE = 'MIDDLE';
  /**
   * An alignment that aligns the content to the bottom of the content holder.
   * Corresponds to ECMA-376 ST_TextAnchoringType 'b'.
   */
  public const CONTENT_ALIGNMENT_BOTTOM = 'BOTTOM';
  protected $backgroundColorType = OptionalColor::class;
  protected $backgroundColorDataType = '';
  protected $borderBottomType = TableCellBorder::class;
  protected $borderBottomDataType = '';
  protected $borderLeftType = TableCellBorder::class;
  protected $borderLeftDataType = '';
  protected $borderRightType = TableCellBorder::class;
  protected $borderRightDataType = '';
  protected $borderTopType = TableCellBorder::class;
  protected $borderTopDataType = '';
  /**
   * The column span of the cell. This property is read-only.
   *
   * @var int
   */
  public $columnSpan;
  /**
   * The alignment of the content in the table cell. The default alignment
   * matches the alignment for newly created table cells in the Docs editor.
   *
   * @var string
   */
  public $contentAlignment;
  protected $paddingBottomType = Dimension::class;
  protected $paddingBottomDataType = '';
  protected $paddingLeftType = Dimension::class;
  protected $paddingLeftDataType = '';
  protected $paddingRightType = Dimension::class;
  protected $paddingRightDataType = '';
  protected $paddingTopType = Dimension::class;
  protected $paddingTopDataType = '';
  /**
   * The row span of the cell. This property is read-only.
   *
   * @var int
   */
  public $rowSpan;

  /**
   * The background color of the cell.
   *
   * @param OptionalColor $backgroundColor
   */
  public function setBackgroundColor(OptionalColor $backgroundColor)
  {
    $this->backgroundColor = $backgroundColor;
  }
  /**
   * @return OptionalColor
   */
  public function getBackgroundColor()
  {
    return $this->backgroundColor;
  }
  /**
   * The bottom border of the cell.
   *
   * @param TableCellBorder $borderBottom
   */
  public function setBorderBottom(TableCellBorder $borderBottom)
  {
    $this->borderBottom = $borderBottom;
  }
  /**
   * @return TableCellBorder
   */
  public function getBorderBottom()
  {
    return $this->borderBottom;
  }
  /**
   * The left border of the cell.
   *
   * @param TableCellBorder $borderLeft
   */
  public function setBorderLeft(TableCellBorder $borderLeft)
  {
    $this->borderLeft = $borderLeft;
  }
  /**
   * @return TableCellBorder
   */
  public function getBorderLeft()
  {
    return $this->borderLeft;
  }
  /**
   * The right border of the cell.
   *
   * @param TableCellBorder $borderRight
   */
  public function setBorderRight(TableCellBorder $borderRight)
  {
    $this->borderRight = $borderRight;
  }
  /**
   * @return TableCellBorder
   */
  public function getBorderRight()
  {
    return $this->borderRight;
  }
  /**
   * The top border of the cell.
   *
   * @param TableCellBorder $borderTop
   */
  public function setBorderTop(TableCellBorder $borderTop)
  {
    $this->borderTop = $borderTop;
  }
  /**
   * @return TableCellBorder
   */
  public function getBorderTop()
  {
    return $this->borderTop;
  }
  /**
   * The column span of the cell. This property is read-only.
   *
   * @param int $columnSpan
   */
  public function setColumnSpan($columnSpan)
  {
    $this->columnSpan = $columnSpan;
  }
  /**
   * @return int
   */
  public function getColumnSpan()
  {
    return $this->columnSpan;
  }
  /**
   * The alignment of the content in the table cell. The default alignment
   * matches the alignment for newly created table cells in the Docs editor.
   *
   * Accepted values: CONTENT_ALIGNMENT_UNSPECIFIED,
   * CONTENT_ALIGNMENT_UNSUPPORTED, TOP, MIDDLE, BOTTOM
   *
   * @param self::CONTENT_ALIGNMENT_* $contentAlignment
   */
  public function setContentAlignment($contentAlignment)
  {
    $this->contentAlignment = $contentAlignment;
  }
  /**
   * @return self::CONTENT_ALIGNMENT_*
   */
  public function getContentAlignment()
  {
    return $this->contentAlignment;
  }
  /**
   * The bottom padding of the cell.
   *
   * @param Dimension $paddingBottom
   */
  public function setPaddingBottom(Dimension $paddingBottom)
  {
    $this->paddingBottom = $paddingBottom;
  }
  /**
   * @return Dimension
   */
  public function getPaddingBottom()
  {
    return $this->paddingBottom;
  }
  /**
   * The left padding of the cell.
   *
   * @param Dimension $paddingLeft
   */
  public function setPaddingLeft(Dimension $paddingLeft)
  {
    $this->paddingLeft = $paddingLeft;
  }
  /**
   * @return Dimension
   */
  public function getPaddingLeft()
  {
    return $this->paddingLeft;
  }
  /**
   * The right padding of the cell.
   *
   * @param Dimension $paddingRight
   */
  public function setPaddingRight(Dimension $paddingRight)
  {
    $this->paddingRight = $paddingRight;
  }
  /**
   * @return Dimension
   */
  public function getPaddingRight()
  {
    return $this->paddingRight;
  }
  /**
   * The top padding of the cell.
   *
   * @param Dimension $paddingTop
   */
  public function setPaddingTop(Dimension $paddingTop)
  {
    $this->paddingTop = $paddingTop;
  }
  /**
   * @return Dimension
   */
  public function getPaddingTop()
  {
    return $this->paddingTop;
  }
  /**
   * The row span of the cell. This property is read-only.
   *
   * @param int $rowSpan
   */
  public function setRowSpan($rowSpan)
  {
    $this->rowSpan = $rowSpan;
  }
  /**
   * @return int
   */
  public function getRowSpan()
  {
    return $this->rowSpan;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableCellStyle::class, 'Google_Service_Docs_TableCellStyle');
