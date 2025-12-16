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

class TableCellStyleSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to background_color.
   *
   * @var bool
   */
  public $backgroundColorSuggested;
  /**
   * Indicates if there was a suggested change to border_bottom.
   *
   * @var bool
   */
  public $borderBottomSuggested;
  /**
   * Indicates if there was a suggested change to border_left.
   *
   * @var bool
   */
  public $borderLeftSuggested;
  /**
   * Indicates if there was a suggested change to border_right.
   *
   * @var bool
   */
  public $borderRightSuggested;
  /**
   * Indicates if there was a suggested change to border_top.
   *
   * @var bool
   */
  public $borderTopSuggested;
  /**
   * Indicates if there was a suggested change to column_span.
   *
   * @var bool
   */
  public $columnSpanSuggested;
  /**
   * Indicates if there was a suggested change to content_alignment.
   *
   * @var bool
   */
  public $contentAlignmentSuggested;
  /**
   * Indicates if there was a suggested change to padding_bottom.
   *
   * @var bool
   */
  public $paddingBottomSuggested;
  /**
   * Indicates if there was a suggested change to padding_left.
   *
   * @var bool
   */
  public $paddingLeftSuggested;
  /**
   * Indicates if there was a suggested change to padding_right.
   *
   * @var bool
   */
  public $paddingRightSuggested;
  /**
   * Indicates if there was a suggested change to padding_top.
   *
   * @var bool
   */
  public $paddingTopSuggested;
  /**
   * Indicates if there was a suggested change to row_span.
   *
   * @var bool
   */
  public $rowSpanSuggested;

  /**
   * Indicates if there was a suggested change to background_color.
   *
   * @param bool $backgroundColorSuggested
   */
  public function setBackgroundColorSuggested($backgroundColorSuggested)
  {
    $this->backgroundColorSuggested = $backgroundColorSuggested;
  }
  /**
   * @return bool
   */
  public function getBackgroundColorSuggested()
  {
    return $this->backgroundColorSuggested;
  }
  /**
   * Indicates if there was a suggested change to border_bottom.
   *
   * @param bool $borderBottomSuggested
   */
  public function setBorderBottomSuggested($borderBottomSuggested)
  {
    $this->borderBottomSuggested = $borderBottomSuggested;
  }
  /**
   * @return bool
   */
  public function getBorderBottomSuggested()
  {
    return $this->borderBottomSuggested;
  }
  /**
   * Indicates if there was a suggested change to border_left.
   *
   * @param bool $borderLeftSuggested
   */
  public function setBorderLeftSuggested($borderLeftSuggested)
  {
    $this->borderLeftSuggested = $borderLeftSuggested;
  }
  /**
   * @return bool
   */
  public function getBorderLeftSuggested()
  {
    return $this->borderLeftSuggested;
  }
  /**
   * Indicates if there was a suggested change to border_right.
   *
   * @param bool $borderRightSuggested
   */
  public function setBorderRightSuggested($borderRightSuggested)
  {
    $this->borderRightSuggested = $borderRightSuggested;
  }
  /**
   * @return bool
   */
  public function getBorderRightSuggested()
  {
    return $this->borderRightSuggested;
  }
  /**
   * Indicates if there was a suggested change to border_top.
   *
   * @param bool $borderTopSuggested
   */
  public function setBorderTopSuggested($borderTopSuggested)
  {
    $this->borderTopSuggested = $borderTopSuggested;
  }
  /**
   * @return bool
   */
  public function getBorderTopSuggested()
  {
    return $this->borderTopSuggested;
  }
  /**
   * Indicates if there was a suggested change to column_span.
   *
   * @param bool $columnSpanSuggested
   */
  public function setColumnSpanSuggested($columnSpanSuggested)
  {
    $this->columnSpanSuggested = $columnSpanSuggested;
  }
  /**
   * @return bool
   */
  public function getColumnSpanSuggested()
  {
    return $this->columnSpanSuggested;
  }
  /**
   * Indicates if there was a suggested change to content_alignment.
   *
   * @param bool $contentAlignmentSuggested
   */
  public function setContentAlignmentSuggested($contentAlignmentSuggested)
  {
    $this->contentAlignmentSuggested = $contentAlignmentSuggested;
  }
  /**
   * @return bool
   */
  public function getContentAlignmentSuggested()
  {
    return $this->contentAlignmentSuggested;
  }
  /**
   * Indicates if there was a suggested change to padding_bottom.
   *
   * @param bool $paddingBottomSuggested
   */
  public function setPaddingBottomSuggested($paddingBottomSuggested)
  {
    $this->paddingBottomSuggested = $paddingBottomSuggested;
  }
  /**
   * @return bool
   */
  public function getPaddingBottomSuggested()
  {
    return $this->paddingBottomSuggested;
  }
  /**
   * Indicates if there was a suggested change to padding_left.
   *
   * @param bool $paddingLeftSuggested
   */
  public function setPaddingLeftSuggested($paddingLeftSuggested)
  {
    $this->paddingLeftSuggested = $paddingLeftSuggested;
  }
  /**
   * @return bool
   */
  public function getPaddingLeftSuggested()
  {
    return $this->paddingLeftSuggested;
  }
  /**
   * Indicates if there was a suggested change to padding_right.
   *
   * @param bool $paddingRightSuggested
   */
  public function setPaddingRightSuggested($paddingRightSuggested)
  {
    $this->paddingRightSuggested = $paddingRightSuggested;
  }
  /**
   * @return bool
   */
  public function getPaddingRightSuggested()
  {
    return $this->paddingRightSuggested;
  }
  /**
   * Indicates if there was a suggested change to padding_top.
   *
   * @param bool $paddingTopSuggested
   */
  public function setPaddingTopSuggested($paddingTopSuggested)
  {
    $this->paddingTopSuggested = $paddingTopSuggested;
  }
  /**
   * @return bool
   */
  public function getPaddingTopSuggested()
  {
    return $this->paddingTopSuggested;
  }
  /**
   * Indicates if there was a suggested change to row_span.
   *
   * @param bool $rowSpanSuggested
   */
  public function setRowSpanSuggested($rowSpanSuggested)
  {
    $this->rowSpanSuggested = $rowSpanSuggested;
  }
  /**
   * @return bool
   */
  public function getRowSpanSuggested()
  {
    return $this->rowSpanSuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableCellStyleSuggestionState::class, 'Google_Service_Docs_TableCellStyleSuggestionState');
