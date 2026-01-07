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

class ParagraphStyleSuggestionState extends \Google\Model
{
  /**
   * Indicates if there was a suggested change to alignment.
   *
   * @var bool
   */
  public $alignmentSuggested;
  /**
   * Indicates if there was a suggested change to avoid_widow_and_orphan.
   *
   * @var bool
   */
  public $avoidWidowAndOrphanSuggested;
  /**
   * Indicates if there was a suggested change to border_between.
   *
   * @var bool
   */
  public $borderBetweenSuggested;
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
   * Indicates if there was a suggested change to direction.
   *
   * @var bool
   */
  public $directionSuggested;
  /**
   * Indicates if there was a suggested change to heading_id.
   *
   * @var bool
   */
  public $headingIdSuggested;
  /**
   * Indicates if there was a suggested change to indent_end.
   *
   * @var bool
   */
  public $indentEndSuggested;
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
   * Indicates if there was a suggested change to keep_lines_together.
   *
   * @var bool
   */
  public $keepLinesTogetherSuggested;
  /**
   * Indicates if there was a suggested change to keep_with_next.
   *
   * @var bool
   */
  public $keepWithNextSuggested;
  /**
   * Indicates if there was a suggested change to line_spacing.
   *
   * @var bool
   */
  public $lineSpacingSuggested;
  /**
   * Indicates if there was a suggested change to named_style_type.
   *
   * @var bool
   */
  public $namedStyleTypeSuggested;
  /**
   * Indicates if there was a suggested change to page_break_before.
   *
   * @var bool
   */
  public $pageBreakBeforeSuggested;
  protected $shadingSuggestionStateType = ShadingSuggestionState::class;
  protected $shadingSuggestionStateDataType = '';
  /**
   * Indicates if there was a suggested change to space_above.
   *
   * @var bool
   */
  public $spaceAboveSuggested;
  /**
   * Indicates if there was a suggested change to space_below.
   *
   * @var bool
   */
  public $spaceBelowSuggested;
  /**
   * Indicates if there was a suggested change to spacing_mode.
   *
   * @var bool
   */
  public $spacingModeSuggested;

  /**
   * Indicates if there was a suggested change to alignment.
   *
   * @param bool $alignmentSuggested
   */
  public function setAlignmentSuggested($alignmentSuggested)
  {
    $this->alignmentSuggested = $alignmentSuggested;
  }
  /**
   * @return bool
   */
  public function getAlignmentSuggested()
  {
    return $this->alignmentSuggested;
  }
  /**
   * Indicates if there was a suggested change to avoid_widow_and_orphan.
   *
   * @param bool $avoidWidowAndOrphanSuggested
   */
  public function setAvoidWidowAndOrphanSuggested($avoidWidowAndOrphanSuggested)
  {
    $this->avoidWidowAndOrphanSuggested = $avoidWidowAndOrphanSuggested;
  }
  /**
   * @return bool
   */
  public function getAvoidWidowAndOrphanSuggested()
  {
    return $this->avoidWidowAndOrphanSuggested;
  }
  /**
   * Indicates if there was a suggested change to border_between.
   *
   * @param bool $borderBetweenSuggested
   */
  public function setBorderBetweenSuggested($borderBetweenSuggested)
  {
    $this->borderBetweenSuggested = $borderBetweenSuggested;
  }
  /**
   * @return bool
   */
  public function getBorderBetweenSuggested()
  {
    return $this->borderBetweenSuggested;
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
   * Indicates if there was a suggested change to direction.
   *
   * @param bool $directionSuggested
   */
  public function setDirectionSuggested($directionSuggested)
  {
    $this->directionSuggested = $directionSuggested;
  }
  /**
   * @return bool
   */
  public function getDirectionSuggested()
  {
    return $this->directionSuggested;
  }
  /**
   * Indicates if there was a suggested change to heading_id.
   *
   * @param bool $headingIdSuggested
   */
  public function setHeadingIdSuggested($headingIdSuggested)
  {
    $this->headingIdSuggested = $headingIdSuggested;
  }
  /**
   * @return bool
   */
  public function getHeadingIdSuggested()
  {
    return $this->headingIdSuggested;
  }
  /**
   * Indicates if there was a suggested change to indent_end.
   *
   * @param bool $indentEndSuggested
   */
  public function setIndentEndSuggested($indentEndSuggested)
  {
    $this->indentEndSuggested = $indentEndSuggested;
  }
  /**
   * @return bool
   */
  public function getIndentEndSuggested()
  {
    return $this->indentEndSuggested;
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
   * Indicates if there was a suggested change to keep_lines_together.
   *
   * @param bool $keepLinesTogetherSuggested
   */
  public function setKeepLinesTogetherSuggested($keepLinesTogetherSuggested)
  {
    $this->keepLinesTogetherSuggested = $keepLinesTogetherSuggested;
  }
  /**
   * @return bool
   */
  public function getKeepLinesTogetherSuggested()
  {
    return $this->keepLinesTogetherSuggested;
  }
  /**
   * Indicates if there was a suggested change to keep_with_next.
   *
   * @param bool $keepWithNextSuggested
   */
  public function setKeepWithNextSuggested($keepWithNextSuggested)
  {
    $this->keepWithNextSuggested = $keepWithNextSuggested;
  }
  /**
   * @return bool
   */
  public function getKeepWithNextSuggested()
  {
    return $this->keepWithNextSuggested;
  }
  /**
   * Indicates if there was a suggested change to line_spacing.
   *
   * @param bool $lineSpacingSuggested
   */
  public function setLineSpacingSuggested($lineSpacingSuggested)
  {
    $this->lineSpacingSuggested = $lineSpacingSuggested;
  }
  /**
   * @return bool
   */
  public function getLineSpacingSuggested()
  {
    return $this->lineSpacingSuggested;
  }
  /**
   * Indicates if there was a suggested change to named_style_type.
   *
   * @param bool $namedStyleTypeSuggested
   */
  public function setNamedStyleTypeSuggested($namedStyleTypeSuggested)
  {
    $this->namedStyleTypeSuggested = $namedStyleTypeSuggested;
  }
  /**
   * @return bool
   */
  public function getNamedStyleTypeSuggested()
  {
    return $this->namedStyleTypeSuggested;
  }
  /**
   * Indicates if there was a suggested change to page_break_before.
   *
   * @param bool $pageBreakBeforeSuggested
   */
  public function setPageBreakBeforeSuggested($pageBreakBeforeSuggested)
  {
    $this->pageBreakBeforeSuggested = $pageBreakBeforeSuggested;
  }
  /**
   * @return bool
   */
  public function getPageBreakBeforeSuggested()
  {
    return $this->pageBreakBeforeSuggested;
  }
  /**
   * A mask that indicates which of the fields in shading have been changed in
   * this suggestion.
   *
   * @param ShadingSuggestionState $shadingSuggestionState
   */
  public function setShadingSuggestionState(ShadingSuggestionState $shadingSuggestionState)
  {
    $this->shadingSuggestionState = $shadingSuggestionState;
  }
  /**
   * @return ShadingSuggestionState
   */
  public function getShadingSuggestionState()
  {
    return $this->shadingSuggestionState;
  }
  /**
   * Indicates if there was a suggested change to space_above.
   *
   * @param bool $spaceAboveSuggested
   */
  public function setSpaceAboveSuggested($spaceAboveSuggested)
  {
    $this->spaceAboveSuggested = $spaceAboveSuggested;
  }
  /**
   * @return bool
   */
  public function getSpaceAboveSuggested()
  {
    return $this->spaceAboveSuggested;
  }
  /**
   * Indicates if there was a suggested change to space_below.
   *
   * @param bool $spaceBelowSuggested
   */
  public function setSpaceBelowSuggested($spaceBelowSuggested)
  {
    $this->spaceBelowSuggested = $spaceBelowSuggested;
  }
  /**
   * @return bool
   */
  public function getSpaceBelowSuggested()
  {
    return $this->spaceBelowSuggested;
  }
  /**
   * Indicates if there was a suggested change to spacing_mode.
   *
   * @param bool $spacingModeSuggested
   */
  public function setSpacingModeSuggested($spacingModeSuggested)
  {
    $this->spacingModeSuggested = $spacingModeSuggested;
  }
  /**
   * @return bool
   */
  public function getSpacingModeSuggested()
  {
    return $this->spacingModeSuggested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ParagraphStyleSuggestionState::class, 'Google_Service_Docs_ParagraphStyleSuggestionState');
