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

class AlgorithmRulesSignalComparison extends \Google\Model
{
  /**
   * Unknown operator.
   */
  public const COMPARISON_OPERATOR_COMPARISON_OPERATOR_UNSPECIFIED = 'COMPARISON_OPERATOR_UNSPECIFIED';
  /**
   * Values are equal.
   */
  public const COMPARISON_OPERATOR_EQUAL = 'EQUAL';
  /**
   * Signal value is greater than the comparison value.
   */
  public const COMPARISON_OPERATOR_GREATER_THAN = 'GREATER_THAN';
  /**
   * Signal value is less than the second.
   */
  public const COMPARISON_OPERATOR_LESS_THAN = 'LESS_THAN';
  /**
   * Signal value is greater than or equal to the second.
   */
  public const COMPARISON_OPERATOR_GREATER_THAN_OR_EQUAL_TO = 'GREATER_THAN_OR_EQUAL_TO';
  /**
   * Signal value is less than or equal to the comparison value.
   */
  public const COMPARISON_OPERATOR_LESS_THAN_OR_EQUAL_TO = 'LESS_THAN_OR_EQUAL_TO';
  /**
   * Signal value is a list and contains the comparison value.
   */
  public const COMPARISON_OPERATOR_LIST_CONTAINS = 'LIST_CONTAINS';
  /**
   * Operator used to compare the two values. In the resulting experession, the
   * `signal` will be the first value and the `comparisonValue will be the
   * second.
   *
   * @var string
   */
  public $comparisonOperator;
  protected $comparisonValueType = AlgorithmRulesComparisonValue::class;
  protected $comparisonValueDataType = '';
  protected $signalType = AlgorithmRulesSignal::class;
  protected $signalDataType = '';

  /**
   * Operator used to compare the two values. In the resulting experession, the
   * `signal` will be the first value and the `comparisonValue will be the
   * second.
   *
   * Accepted values: COMPARISON_OPERATOR_UNSPECIFIED, EQUAL, GREATER_THAN,
   * LESS_THAN, GREATER_THAN_OR_EQUAL_TO, LESS_THAN_OR_EQUAL_TO, LIST_CONTAINS
   *
   * @param self::COMPARISON_OPERATOR_* $comparisonOperator
   */
  public function setComparisonOperator($comparisonOperator)
  {
    $this->comparisonOperator = $comparisonOperator;
  }
  /**
   * @return self::COMPARISON_OPERATOR_*
   */
  public function getComparisonOperator()
  {
    return $this->comparisonOperator;
  }
  /**
   * Value to compare signal to.
   *
   * @param AlgorithmRulesComparisonValue $comparisonValue
   */
  public function setComparisonValue(AlgorithmRulesComparisonValue $comparisonValue)
  {
    $this->comparisonValue = $comparisonValue;
  }
  /**
   * @return AlgorithmRulesComparisonValue
   */
  public function getComparisonValue()
  {
    return $this->comparisonValue;
  }
  /**
   * Signal to compare.
   *
   * @param AlgorithmRulesSignal $signal
   */
  public function setSignal(AlgorithmRulesSignal $signal)
  {
    $this->signal = $signal;
  }
  /**
   * @return AlgorithmRulesSignal
   */
  public function getSignal()
  {
    return $this->signal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlgorithmRulesSignalComparison::class, 'Google_Service_DisplayVideo_AlgorithmRulesSignalComparison');
