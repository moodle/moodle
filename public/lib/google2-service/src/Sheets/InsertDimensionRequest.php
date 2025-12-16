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

class InsertDimensionRequest extends \Google\Model
{
  /**
   * Whether dimension properties should be extended from the dimensions before
   * or after the newly inserted dimensions. True to inherit from the dimensions
   * before (in which case the start index must be greater than 0), and false to
   * inherit from the dimensions after. For example, if row index 0 has red
   * background and row index 1 has a green background, then inserting 2 rows at
   * index 1 can inherit either the green or red background. If
   * `inheritFromBefore` is true, the two new rows will be red (because the row
   * before the insertion point was red), whereas if `inheritFromBefore` is
   * false, the two new rows will be green (because the row after the insertion
   * point was green).
   *
   * @var bool
   */
  public $inheritFromBefore;
  protected $rangeType = DimensionRange::class;
  protected $rangeDataType = '';

  /**
   * Whether dimension properties should be extended from the dimensions before
   * or after the newly inserted dimensions. True to inherit from the dimensions
   * before (in which case the start index must be greater than 0), and false to
   * inherit from the dimensions after. For example, if row index 0 has red
   * background and row index 1 has a green background, then inserting 2 rows at
   * index 1 can inherit either the green or red background. If
   * `inheritFromBefore` is true, the two new rows will be red (because the row
   * before the insertion point was red), whereas if `inheritFromBefore` is
   * false, the two new rows will be green (because the row after the insertion
   * point was green).
   *
   * @param bool $inheritFromBefore
   */
  public function setInheritFromBefore($inheritFromBefore)
  {
    $this->inheritFromBefore = $inheritFromBefore;
  }
  /**
   * @return bool
   */
  public function getInheritFromBefore()
  {
    return $this->inheritFromBefore;
  }
  /**
   * The dimensions to insert. Both the start and end indexes must be bounded.
   *
   * @param DimensionRange $range
   */
  public function setRange(DimensionRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return DimensionRange
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertDimensionRequest::class, 'Google_Service_Sheets_InsertDimensionRequest');
