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

class UpdateConditionalFormatRuleResponse extends \Google\Model
{
  /**
   * The index of the new rule.
   *
   * @var int
   */
  public $newIndex;
  protected $newRuleType = ConditionalFormatRule::class;
  protected $newRuleDataType = '';
  /**
   * The old index of the rule. Not set if a rule was replaced (because it is
   * the same as new_index).
   *
   * @var int
   */
  public $oldIndex;
  protected $oldRuleType = ConditionalFormatRule::class;
  protected $oldRuleDataType = '';

  /**
   * The index of the new rule.
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
   * The new rule that replaced the old rule (if replacing), or the rule that
   * was moved (if moved)
   *
   * @param ConditionalFormatRule $newRule
   */
  public function setNewRule(ConditionalFormatRule $newRule)
  {
    $this->newRule = $newRule;
  }
  /**
   * @return ConditionalFormatRule
   */
  public function getNewRule()
  {
    return $this->newRule;
  }
  /**
   * The old index of the rule. Not set if a rule was replaced (because it is
   * the same as new_index).
   *
   * @param int $oldIndex
   */
  public function setOldIndex($oldIndex)
  {
    $this->oldIndex = $oldIndex;
  }
  /**
   * @return int
   */
  public function getOldIndex()
  {
    return $this->oldIndex;
  }
  /**
   * The old (deleted) rule. Not set if a rule was moved (because it is the same
   * as new_rule).
   *
   * @param ConditionalFormatRule $oldRule
   */
  public function setOldRule(ConditionalFormatRule $oldRule)
  {
    $this->oldRule = $oldRule;
  }
  /**
   * @return ConditionalFormatRule
   */
  public function getOldRule()
  {
    return $this->oldRule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateConditionalFormatRuleResponse::class, 'Google_Service_Sheets_UpdateConditionalFormatRuleResponse');
