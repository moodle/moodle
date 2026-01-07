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

namespace Google\Service\CloudBuild;

class WhenExpression extends \Google\Collection
{
  /**
   * Default enum type; should not be used.
   */
  public const EXPRESSION_OPERATOR_EXPRESSION_OPERATOR_UNSPECIFIED = 'EXPRESSION_OPERATOR_UNSPECIFIED';
  /**
   * Input is in values.
   */
  public const EXPRESSION_OPERATOR_IN = 'IN';
  /**
   * Input is not in values.
   */
  public const EXPRESSION_OPERATOR_NOT_IN = 'NOT_IN';
  protected $collection_key = 'values';
  /**
   * Operator that represents an Input's relationship to the values
   *
   * @var string
   */
  public $expressionOperator;
  /**
   * Input is the string for guard checking which can be a static input or an
   * output from a parent Task.
   *
   * @var string
   */
  public $input;
  /**
   * Values is an array of strings, which is compared against the input, for
   * guard checking.
   *
   * @var string[]
   */
  public $values;

  /**
   * Operator that represents an Input's relationship to the values
   *
   * Accepted values: EXPRESSION_OPERATOR_UNSPECIFIED, IN, NOT_IN
   *
   * @param self::EXPRESSION_OPERATOR_* $expressionOperator
   */
  public function setExpressionOperator($expressionOperator)
  {
    $this->expressionOperator = $expressionOperator;
  }
  /**
   * @return self::EXPRESSION_OPERATOR_*
   */
  public function getExpressionOperator()
  {
    return $this->expressionOperator;
  }
  /**
   * Input is the string for guard checking which can be a static input or an
   * output from a parent Task.
   *
   * @param string $input
   */
  public function setInput($input)
  {
    $this->input = $input;
  }
  /**
   * @return string
   */
  public function getInput()
  {
    return $this->input;
  }
  /**
   * Values is an array of strings, which is compared against the input, for
   * guard checking.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WhenExpression::class, 'Google_Service_CloudBuild_WhenExpression');
