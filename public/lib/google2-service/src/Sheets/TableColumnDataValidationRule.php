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

class TableColumnDataValidationRule extends \Google\Model
{
  protected $conditionType = BooleanCondition::class;
  protected $conditionDataType = '';

  /**
   * The condition that data in the cell must match. Valid only if the
   * [BooleanCondition.type] is ONE_OF_LIST.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableColumnDataValidationRule::class, 'Google_Service_Sheets_TableColumnDataValidationRule');
