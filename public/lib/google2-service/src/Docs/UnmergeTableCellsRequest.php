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

class UnmergeTableCellsRequest extends \Google\Model
{
  protected $tableRangeType = TableRange::class;
  protected $tableRangeDataType = '';

  /**
   * The table range specifying which cells of the table to unmerge. All merged
   * cells in this range will be unmerged, and cells that are already unmerged
   * will not be affected. If the range has no merged cells, the request will do
   * nothing. If there is text in any of the merged cells, the text will remain
   * in the "head" cell of the resulting block of unmerged cells. The "head"
   * cell is the upper-left cell when the content direction is from left to
   * right, and the upper-right otherwise.
   *
   * @param TableRange $tableRange
   */
  public function setTableRange(TableRange $tableRange)
  {
    $this->tableRange = $tableRange;
  }
  /**
   * @return TableRange
   */
  public function getTableRange()
  {
    return $this->tableRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnmergeTableCellsRequest::class, 'Google_Service_Docs_UnmergeTableCellsRequest');
