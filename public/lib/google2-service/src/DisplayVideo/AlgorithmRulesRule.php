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

namespace Google\Service\DisplayVideo;

class AlgorithmRulesRule extends \Google\Collection
{
  protected $collection_key = 'conditions';
  protected $conditionsType = AlgorithmRulesRuleCondition::class;
  protected $conditionsDataType = 'array';
  protected $defaultReturnValueType = AlgorithmRulesSignalValue::class;
  protected $defaultReturnValueDataType = '';

  /**
   * List of conditions in this rule. The criteria among conditions should be
   * mutually exclusive.
   *
   * @param AlgorithmRulesRuleCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return AlgorithmRulesRuleCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * The default return value applied when none of the conditions are met.
   *
   * @param AlgorithmRulesSignalValue $defaultReturnValue
   */
  public function setDefaultReturnValue(AlgorithmRulesSignalValue $defaultReturnValue)
  {
    $this->defaultReturnValue = $defaultReturnValue;
  }
  /**
   * @return AlgorithmRulesSignalValue
   */
  public function getDefaultReturnValue()
  {
    return $this->defaultReturnValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRulesRule::class, 'Google_Service_DisplayVideo_AlgorithmRulesRule');
