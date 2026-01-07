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

class AlgorithmRulesRuleCondition extends \Google\Collection
{
  protected $collection_key = 'signalComparisons';
  protected $returnValueType = AlgorithmRulesSignalValue::class;
  protected $returnValueDataType = '';
  protected $signalComparisonsType = AlgorithmRulesSignalComparison::class;
  protected $signalComparisonsDataType = 'array';

  /**
   * The value returned if the `signalComparisons` condition evaluates to
   * `TRUE`.
   *
   * @param AlgorithmRulesSignalValue $returnValue
   */
  public function setReturnValue(AlgorithmRulesSignalValue $returnValue)
  {
    $this->returnValue = $returnValue;
  }
  /**
   * @return AlgorithmRulesSignalValue
   */
  public function getReturnValue()
  {
    return $this->returnValue;
  }
  /**
   * List of comparisons that build `if` statement condition. The comparisons
   * are combined into a single condition with `AND` logical operators.
   *
   * @param AlgorithmRulesSignalComparison[] $signalComparisons
   */
  public function setSignalComparisons($signalComparisons)
  {
    $this->signalComparisons = $signalComparisons;
  }
  /**
   * @return AlgorithmRulesSignalComparison[]
   */
  public function getSignalComparisons()
  {
    return $this->signalComparisons;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRulesRuleCondition::class, 'Google_Service_DisplayVideo_AlgorithmRulesRuleCondition');
