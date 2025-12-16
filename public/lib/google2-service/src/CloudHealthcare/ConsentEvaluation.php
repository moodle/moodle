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

namespace Google\Service\CloudHealthcare;

class ConsentEvaluation extends \Google\Model
{
  /**
   * No evaluation result specified. This option is invalid.
   */
  public const EVALUATION_RESULT_EVALUATION_RESULT_UNSPECIFIED = 'EVALUATION_RESULT_UNSPECIFIED';
  /**
   * The Consent is not applicable to the requested access determination. For
   * example, the Consent does not apply to the user for which the access
   * determination is requested, or it has a `state` of `REVOKED`, or it has
   * expired.
   */
  public const EVALUATION_RESULT_NOT_APPLICABLE = 'NOT_APPLICABLE';
  /**
   * The Consent does not have a policy that matches the `resource_attributes`
   * of the evaluated resource.
   */
  public const EVALUATION_RESULT_NO_MATCHING_POLICY = 'NO_MATCHING_POLICY';
  /**
   * The Consent has at least one policy that matches the `resource_attributes`
   * of the evaluated resource, but no `authorization_rule` was satisfied.
   */
  public const EVALUATION_RESULT_NO_SATISFIED_POLICY = 'NO_SATISFIED_POLICY';
  /**
   * The Consent has at least one policy that matches the `resource_attributes`
   * of the evaluated resource, and at least one `authorization_rule` was
   * satisfied.
   */
  public const EVALUATION_RESULT_HAS_SATISFIED_POLICY = 'HAS_SATISFIED_POLICY';
  /**
   * The evaluation result.
   *
   * @var string
   */
  public $evaluationResult;

  /**
   * The evaluation result.
   *
   * Accepted values: EVALUATION_RESULT_UNSPECIFIED, NOT_APPLICABLE,
   * NO_MATCHING_POLICY, NO_SATISFIED_POLICY, HAS_SATISFIED_POLICY
   *
   * @param self::EVALUATION_RESULT_* $evaluationResult
   */
  public function setEvaluationResult($evaluationResult)
  {
    $this->evaluationResult = $evaluationResult;
  }
  /**
   * @return self::EVALUATION_RESULT_*
   */
  public function getEvaluationResult()
  {
    return $this->evaluationResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsentEvaluation::class, 'Google_Service_CloudHealthcare_ConsentEvaluation');
