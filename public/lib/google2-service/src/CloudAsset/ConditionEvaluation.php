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

namespace Google\Service\CloudAsset;

class ConditionEvaluation extends \Google\Model
{
  /**
   * Reserved for future use.
   */
  public const EVALUATION_VALUE_EVALUATION_VALUE_UNSPECIFIED = 'EVALUATION_VALUE_UNSPECIFIED';
  /**
   * The evaluation result is `true`.
   */
  public const EVALUATION_VALUE_TRUE = 'TRUE';
  /**
   * The evaluation result is `false`.
   */
  public const EVALUATION_VALUE_FALSE = 'FALSE';
  /**
   * The evaluation result is `conditional` when the condition expression
   * contains variables that are either missing input values or have not been
   * supported by Policy Analyzer yet.
   */
  public const EVALUATION_VALUE_CONDITIONAL = 'CONDITIONAL';
  /**
   * The evaluation result.
   *
   * @var string
   */
  public $evaluationValue;

  /**
   * The evaluation result.
   *
   * Accepted values: EVALUATION_VALUE_UNSPECIFIED, TRUE, FALSE, CONDITIONAL
   *
   * @param self::EVALUATION_VALUE_* $evaluationValue
   */
  public function setEvaluationValue($evaluationValue)
  {
    $this->evaluationValue = $evaluationValue;
  }
  /**
   * @return self::EVALUATION_VALUE_*
   */
  public function getEvaluationValue()
  {
    return $this->evaluationValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConditionEvaluation::class, 'Google_Service_CloudAsset_ConditionEvaluation');
