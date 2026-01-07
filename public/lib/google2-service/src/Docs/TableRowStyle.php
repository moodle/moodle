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

class TableRowStyle extends \Google\Model
{
  protected $minRowHeightType = Dimension::class;
  protected $minRowHeightDataType = '';
  /**
   * Whether the row cannot overflow across page or column boundaries.
   *
   * @var bool
   */
  public $preventOverflow;
  /**
   * Whether the row is a table header.
   *
   * @var bool
   */
  public $tableHeader;

  /**
   * The minimum height of the row. The row will be rendered in the Docs editor
   * at a height equal to or greater than this value in order to show all the
   * content in the row's cells.
   *
   * @param Dimension $minRowHeight
   */
  public function setMinRowHeight(Dimension $minRowHeight)
  {
    $this->minRowHeight = $minRowHeight;
  }
  /**
   * @return Dimension
   */
  public function getMinRowHeight()
  {
    return $this->minRowHeight;
  }
  /**
   * Whether the row cannot overflow across page or column boundaries.
   *
   * @param bool $preventOverflow
   */
  public function setPreventOverflow($preventOverflow)
  {
    $this->preventOverflow = $preventOverflow;
  }
  /**
   * @return bool
   */
  public function getPreventOverflow()
  {
    return $this->preventOverflow;
  }
  /**
   * Whether the row is a table header.
   *
   * @param bool $tableHeader
   */
  public function setTableHeader($tableHeader)
  {
    $this->tableHeader = $tableHeader;
  }
  /**
   * @return bool
   */
  public function getTableHeader()
  {
    return $this->tableHeader;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableRowStyle::class, 'Google_Service_Docs_TableRowStyle');
