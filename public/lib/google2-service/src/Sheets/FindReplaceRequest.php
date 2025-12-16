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

namespace Google\Service\Sheets;

class FindReplaceRequest extends \Google\Model
{
  /**
   * True to find/replace over all sheets.
   *
   * @var bool
   */
  public $allSheets;
  /**
   * The value to search.
   *
   * @var string
   */
  public $find;
  /**
   * True if the search should include cells with formulas. False to skip cells
   * with formulas.
   *
   * @var bool
   */
  public $includeFormulas;
  /**
   * True if the search is case sensitive.
   *
   * @var bool
   */
  public $matchCase;
  /**
   * True if the find value should match the entire cell.
   *
   * @var bool
   */
  public $matchEntireCell;
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';
  /**
   * The value to use as the replacement.
   *
   * @var string
   */
  public $replacement;
  /**
   * True if the find value is a regex. The regular expression and replacement
   * should follow Java regex rules at
   * https://docs.oracle.com/javase/8/docs/api/java/util/regex/Pattern.html. The
   * replacement string is allowed to refer to capturing groups. For example, if
   * one cell has the contents `"Google Sheets"` and another has `"Google
   * Docs"`, then searching for `"o.* (.*)"` with a replacement of `"$1 Rocks"`
   * would change the contents of the cells to `"GSheets Rocks"` and `"GDocs
   * Rocks"` respectively.
   *
   * @var bool
   */
  public $searchByRegex;
  /**
   * The sheet to find/replace over.
   *
   * @var int
   */
  public $sheetId;

  /**
   * True to find/replace over all sheets.
   *
   * @param bool $allSheets
   */
  public function setAllSheets($allSheets)
  {
    $this->allSheets = $allSheets;
  }
  /**
   * @return bool
   */
  public function getAllSheets()
  {
    return $this->allSheets;
  }
  /**
   * The value to search.
   *
   * @param string $find
   */
  public function setFind($find)
  {
    $this->find = $find;
  }
  /**
   * @return string
   */
  public function getFind()
  {
    return $this->find;
  }
  /**
   * True if the search should include cells with formulas. False to skip cells
   * with formulas.
   *
   * @param bool $includeFormulas
   */
  public function setIncludeFormulas($includeFormulas)
  {
    $this->includeFormulas = $includeFormulas;
  }
  /**
   * @return bool
   */
  public function getIncludeFormulas()
  {
    return $this->includeFormulas;
  }
  /**
   * True if the search is case sensitive.
   *
   * @param bool $matchCase
   */
  public function setMatchCase($matchCase)
  {
    $this->matchCase = $matchCase;
  }
  /**
   * @return bool
   */
  public function getMatchCase()
  {
    return $this->matchCase;
  }
  /**
   * True if the find value should match the entire cell.
   *
   * @param bool $matchEntireCell
   */
  public function setMatchEntireCell($matchEntireCell)
  {
    $this->matchEntireCell = $matchEntireCell;
  }
  /**
   * @return bool
   */
  public function getMatchEntireCell()
  {
    return $this->matchEntireCell;
  }
  /**
   * The range to find/replace over.
   *
   * @param GridRange $range
   */
  public function setRange(GridRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return GridRange
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * The value to use as the replacement.
   *
   * @param string $replacement
   */
  public function setReplacement($replacement)
  {
    $this->replacement = $replacement;
  }
  /**
   * @return string
   */
  public function getReplacement()
  {
    return $this->replacement;
  }
  /**
   * True if the find value is a regex. The regular expression and replacement
   * should follow Java regex rules at
   * https://docs.oracle.com/javase/8/docs/api/java/util/regex/Pattern.html. The
   * replacement string is allowed to refer to capturing groups. For example, if
   * one cell has the contents `"Google Sheets"` and another has `"Google
   * Docs"`, then searching for `"o.* (.*)"` with a replacement of `"$1 Rocks"`
   * would change the contents of the cells to `"GSheets Rocks"` and `"GDocs
   * Rocks"` respectively.
   *
   * @param bool $searchByRegex
   */
  public function setSearchByRegex($searchByRegex)
  {
    $this->searchByRegex = $searchByRegex;
  }
  /**
   * @return bool
   */
  public function getSearchByRegex()
  {
    return $this->searchByRegex;
  }
  /**
   * The sheet to find/replace over.
   *
   * @param int $sheetId
   */
  public function setSheetId($sheetId)
  {
    $this->sheetId = $sheetId;
  }
  /**
   * @return int
   */
  public function getSheetId()
  {
    return $this->sheetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FindReplaceRequest::class, 'Google_Service_Sheets_FindReplaceRequest');
