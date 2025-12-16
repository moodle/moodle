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

class TableCellProperties extends \Google\Model
{
  /**
   * An unspecified content alignment. The content alignment is inherited from
   * the parent if it exists.
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
  /**
   * The alignment of the content in the table cell. The default alignment
   * matches the alignment for newly created table cells in the Slides editor.
   *
   * @var string
   */
  public $contentAlignment;
  protected $tableCellBackgroundFillType = TableCellBackgroundFill::class;
  protected $tableCellBackgroundFillDataType = '';

  /**
   * The alignment of the content in the table cell. The default alignment
   * matches the alignment for newly created table cells in the Slides editor.
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
   * The background fill of the table cell. The default fill matches the fill
   * for newly created table cells in the Slides editor.
   *
   * @param TableCellBackgroundFill $tableCellBackgroundFill
   */
  public function setTableCellBackgroundFill(TableCellBackgroundFill $tableCellBackgroundFill)
  {
    $this->tableCellBackgroundFill = $tableCellBackgroundFill;
  }
  /**
   * @return TableCellBackgroundFill
   */
  public function getTableCellBackgroundFill()
  {
    return $this->tableCellBackgroundFill;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableCellProperties::class, 'Google_Service_Slides_TableCellProperties');
