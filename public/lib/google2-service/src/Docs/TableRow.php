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

class TableRow extends \Google\Collection
{
  protected $collection_key = 'tableCells';
  /**
   * The zero-based end index of this row, exclusive, in UTF-16 code units.
   *
   * @var int
   */
  public $endIndex;
  /**
   * The zero-based start index of this row, in UTF-16 code units.
   *
   * @var int
   */
  public $startIndex;
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @var string[]
   */
  public $suggestedDeletionIds;
  /**
   * The suggested insertion IDs. A TableRow may have multiple insertion IDs if
   * it's a nested suggested change. If empty, then this is not a suggested
   * insertion.
   *
   * @var string[]
   */
  public $suggestedInsertionIds;
  protected $suggestedTableRowStyleChangesType = SuggestedTableRowStyle::class;
  protected $suggestedTableRowStyleChangesDataType = 'map';
  protected $tableCellsType = TableCell::class;
  protected $tableCellsDataType = 'array';
  protected $tableRowStyleType = TableRowStyle::class;
  protected $tableRowStyleDataType = '';

  /**
   * The zero-based end index of this row, exclusive, in UTF-16 code units.
   *
   * @param int $endIndex
   */
  public function setEndIndex($endIndex)
  {
    $this->endIndex = $endIndex;
  }
  /**
   * @return int
   */
  public function getEndIndex()
  {
    return $this->endIndex;
  }
  /**
   * The zero-based start index of this row, in UTF-16 code units.
   *
   * @param int $startIndex
   */
  public function setStartIndex($startIndex)
  {
    $this->startIndex = $startIndex;
  }
  /**
   * @return int
   */
  public function getStartIndex()
  {
    return $this->startIndex;
  }
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @param string[] $suggestedDeletionIds
   */
  public function setSuggestedDeletionIds($suggestedDeletionIds)
  {
    $this->suggestedDeletionIds = $suggestedDeletionIds;
  }
  /**
   * @return string[]
   */
  public function getSuggestedDeletionIds()
  {
    return $this->suggestedDeletionIds;
  }
  /**
   * The suggested insertion IDs. A TableRow may have multiple insertion IDs if
   * it's a nested suggested change. If empty, then this is not a suggested
   * insertion.
   *
   * @param string[] $suggestedInsertionIds
   */
  public function setSuggestedInsertionIds($suggestedInsertionIds)
  {
    $this->suggestedInsertionIds = $suggestedInsertionIds;
  }
  /**
   * @return string[]
   */
  public function getSuggestedInsertionIds()
  {
    return $this->suggestedInsertionIds;
  }
  /**
   * The suggested style changes to this row, keyed by suggestion ID.
   *
   * @param SuggestedTableRowStyle[] $suggestedTableRowStyleChanges
   */
  public function setSuggestedTableRowStyleChanges($suggestedTableRowStyleChanges)
  {
    $this->suggestedTableRowStyleChanges = $suggestedTableRowStyleChanges;
  }
  /**
   * @return SuggestedTableRowStyle[]
   */
  public function getSuggestedTableRowStyleChanges()
  {
    return $this->suggestedTableRowStyleChanges;
  }
  /**
   * The contents and style of each cell in this row. It's possible for a table
   * to be non-rectangular, so some rows may have a different number of cells
   * than other rows in the same table.
   *
   * @param TableCell[] $tableCells
   */
  public function setTableCells($tableCells)
  {
    $this->tableCells = $tableCells;
  }
  /**
   * @return TableCell[]
   */
  public function getTableCells()
  {
    return $this->tableCells;
  }
  /**
   * The style of the table row.
   *
   * @param TableRowStyle $tableRowStyle
   */
  public function setTableRowStyle(TableRowStyle $tableRowStyle)
  {
    $this->tableRowStyle = $tableRowStyle;
  }
  /**
   * @return TableRowStyle
   */
  public function getTableRowStyle()
  {
    return $this->tableRowStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableRow::class, 'Google_Service_Docs_TableRow');
