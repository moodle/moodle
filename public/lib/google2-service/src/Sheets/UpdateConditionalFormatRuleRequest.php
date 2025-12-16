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

class UpdateConditionalFormatRuleRequest extends \Google\Model
{
  /**
   * The zero-based index of the rule that should be replaced or moved.
   *
   * @var int
   */
  public $index;
  /**
   * The zero-based new index the rule should end up at.
   *
   * @var int
   */
  public $newIndex;
  protected $ruleType = ConditionalFormatRule::class;
  protected $ruleDataType = '';
  /**
   * The sheet of the rule to move. Required if new_index is set, unused
   * otherwise.
   *
   * @var int
   */
  public $sheetId;

  /**
   * The zero-based index of the rule that should be replaced or moved.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * The zero-based new index the rule should end up at.
   *
   * @param int $newIndex
   */
  public function setNewIndex($newIndex)
  {
    $this->newIndex = $newIndex;
  }
  /**
   * @return int
   */
  public function getNewIndex()
  {
    return $this->newIndex;
  }
  /**
   * The rule that should replace the rule at the given index.
   *
   * @param ConditionalFormatRule $rule
   */
  public function setRule(ConditionalFormatRule $rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return ConditionalFormatRule
   */
  public function getRule()
  {
    return $this->rule;
  }
  /**
   * The sheet of the rule to move. Required if new_index is set, unused
   * otherwise.
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
class_alias(UpdateConditionalFormatRuleRequest::class, 'Google_Service_Sheets_UpdateConditionalFormatRuleRequest');
