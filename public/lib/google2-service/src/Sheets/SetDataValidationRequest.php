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

class SetDataValidationRequest extends \Google\Model
{
  /**
   * Optional. If true, the data validation rule will be applied to the filtered
   * rows as well.
   *
   * @var bool
   */
  public $filteredRowsIncluded;
  protected $rangeType = GridRange::class;
  protected $rangeDataType = '';
  protected $ruleType = DataValidationRule::class;
  protected $ruleDataType = '';

  /**
   * Optional. If true, the data validation rule will be applied to the filtered
   * rows as well.
   *
   * @param bool $filteredRowsIncluded
   */
  public function setFilteredRowsIncluded($filteredRowsIncluded)
  {
    $this->filteredRowsIncluded = $filteredRowsIncluded;
  }
  /**
   * @return bool
   */
  public function getFilteredRowsIncluded()
  {
    return $this->filteredRowsIncluded;
  }
  /**
   * The range the data validation rule should apply to.
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
   * The data validation rule to set on each cell in the range, or empty to
   * clear the data validation in the range.
   *
   * @param DataValidationRule $rule
   */
  public function setRule(DataValidationRule $rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return DataValidationRule
   */
  public function getRule()
  {
    return $this->rule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SetDataValidationRequest::class, 'Google_Service_Sheets_SetDataValidationRequest');
