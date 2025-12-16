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

namespace Google\Service\FirebaseRules;

class TestResult extends \Google\Collection
{
  /**
   * Test state is not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Test is a success.
   */
  public const STATE_SUCCESS = 'SUCCESS';
  /**
   * Test is a failure.
   */
  public const STATE_FAILURE = 'FAILURE';
  protected $collection_key = 'visitedExpressions';
  /**
   * Debug messages related to test execution issues encountered during
   * evaluation. Debug messages may be related to too many or too few
   * invocations of function mocks or to runtime errors that occur during
   * evaluation. For example: ```Unable to read variable [name: "resource"]```
   *
   * @var string[]
   */
  public $debugMessages;
  protected $errorPositionType = SourcePosition::class;
  protected $errorPositionDataType = '';
  protected $expressionReportsType = ExpressionReport::class;
  protected $expressionReportsDataType = 'array';
  protected $functionCallsType = FunctionCall::class;
  protected $functionCallsDataType = 'array';
  /**
   * State of the test.
   *
   * @var string
   */
  public $state;
  protected $visitedExpressionsType = VisitedExpression::class;
  protected $visitedExpressionsDataType = 'array';

  /**
   * Debug messages related to test execution issues encountered during
   * evaluation. Debug messages may be related to too many or too few
   * invocations of function mocks or to runtime errors that occur during
   * evaluation. For example: ```Unable to read variable [name: "resource"]```
   *
   * @param string[] $debugMessages
   */
  public function setDebugMessages($debugMessages)
  {
    $this->debugMessages = $debugMessages;
  }
  /**
   * @return string[]
   */
  public function getDebugMessages()
  {
    return $this->debugMessages;
  }
  /**
   * Position in the `Source` or `Ruleset` where the principle runtime error
   * occurs. Evaluation of an expression may result in an error. Rules are deny
   * by default, so a `DENY` expectation when an error is generated is valid.
   * When there is a `DENY` with an error, the `SourcePosition` is returned.
   * E.g. `error_position { line: 19 column: 37 }`
   *
   * @param SourcePosition $errorPosition
   */
  public function setErrorPosition(SourcePosition $errorPosition)
  {
    $this->errorPosition = $errorPosition;
  }
  /**
   * @return SourcePosition
   */
  public function getErrorPosition()
  {
    return $this->errorPosition;
  }
  /**
   * The mapping from expression in the ruleset AST to the values they were
   * evaluated to. Partially-nested to mirror AST structure. Note that this
   * field is actually tracking expressions and not permission statements in
   * contrast to the "visited_expressions" field above. Literal expressions are
   * omitted.
   *
   * @param ExpressionReport[] $expressionReports
   */
  public function setExpressionReports($expressionReports)
  {
    $this->expressionReports = $expressionReports;
  }
  /**
   * @return ExpressionReport[]
   */
  public function getExpressionReports()
  {
    return $this->expressionReports;
  }
  /**
   * The set of function calls made to service-defined methods. Function calls
   * are included in the order in which they are encountered during evaluation,
   * are provided for both mocked and unmocked functions, and included on the
   * response regardless of the test `state`.
   *
   * @param FunctionCall[] $functionCalls
   */
  public function setFunctionCalls($functionCalls)
  {
    $this->functionCalls = $functionCalls;
  }
  /**
   * @return FunctionCall[]
   */
  public function getFunctionCalls()
  {
    return $this->functionCalls;
  }
  /**
   * State of the test.
   *
   * Accepted values: STATE_UNSPECIFIED, SUCCESS, FAILURE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The set of visited permission expressions for a given test. This returns
   * the positions and evaluation results of all visited permission expressions
   * which were relevant to the test case, e.g. ``` match /path { allow read if:
   * } ``` For a detailed report of the intermediate evaluation states, see the
   * `expression_reports` field
   *
   * @param VisitedExpression[] $visitedExpressions
   */
  public function setVisitedExpressions($visitedExpressions)
  {
    $this->visitedExpressions = $visitedExpressions;
  }
  /**
   * @return VisitedExpression[]
   */
  public function getVisitedExpressions()
  {
    return $this->visitedExpressions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestResult::class, 'Google_Service_FirebaseRules_TestResult');
