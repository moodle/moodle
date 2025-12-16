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

class PivotFilterCriteria extends \Google\Collection
{
  protected $collection_key = 'visibleValues';
  protected $conditionType = BooleanCondition::class;
  protected $conditionDataType = '';
  /**
   * Whether values are visible by default. If true, the visible_values are
   * ignored, all values that meet condition (if specified) are shown. If false,
   * values that are both in visible_values and meet condition are shown.
   *
   * @var bool
   */
  public $visibleByDefault;
  /**
   * Values that should be included. Values not listed here are excluded.
   *
   * @var string[]
   */
  public $visibleValues;

  /**
   * A condition that must be true for values to be shown. (`visibleValues` does
   * not override this -- even if a value is listed there, it is still hidden if
   * it does not meet the condition.) Condition values that refer to ranges in
   * A1-notation are evaluated relative to the pivot table sheet. References are
   * treated absolutely, so are not filled down the pivot table. For example, a
   * condition value of `=A1` on "Pivot Table 1" is treated as `'Pivot Table
   * 1'!$A$1`. The source data of the pivot table can be referenced by column
   * header name. For example, if the source data has columns named "Revenue"
   * and "Cost" and a condition is applied to the "Revenue" column with type
   * `NUMBER_GREATER` and value `=Cost`, then only columns where "Revenue" >
   * "Cost" are included.
   *
   * @param BooleanCondition $condition
   */
  public function setCondition(BooleanCondition $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return BooleanCondition
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Whether values are visible by default. If true, the visible_values are
   * ignored, all values that meet condition (if specified) are shown. If false,
   * values that are both in visible_values and meet condition are shown.
   *
   * @param bool $visibleByDefault
   */
  public function setVisibleByDefault($visibleByDefault)
  {
    $this->visibleByDefault = $visibleByDefault;
  }
  /**
   * @return bool
   */
  public function getVisibleByDefault()
  {
    return $this->visibleByDefault;
  }
  /**
   * Values that should be included. Values not listed here are excluded.
   *
   * @param string[] $visibleValues
   */
  public function setVisibleValues($visibleValues)
  {
    $this->visibleValues = $visibleValues;
  }
  /**
   * @return string[]
   */
  public function getVisibleValues()
  {
    return $this->visibleValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PivotFilterCriteria::class, 'Google_Service_Sheets_PivotFilterCriteria');
