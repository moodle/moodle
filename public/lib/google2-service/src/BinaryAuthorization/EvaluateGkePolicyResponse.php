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

namespace Google\Service\BinaryAuthorization;

class EvaluateGkePolicyResponse extends \Google\Collection
{
  /**
   * Not specified. This should never be used.
   */
  public const VERDICT_VERDICT_UNSPECIFIED = 'VERDICT_UNSPECIFIED';
  /**
   * All Pods in the request conform to the policy.
   */
  public const VERDICT_CONFORMANT = 'CONFORMANT';
  /**
   * At least one Pod does not conform to the policy.
   */
  public const VERDICT_NON_CONFORMANT = 'NON_CONFORMANT';
  /**
   * Encountered at least one error evaluating a Pod and all other Pods conform
   * to the policy. Non-conformance has precedence over errors.
   */
  public const VERDICT_ERROR = 'ERROR';
  protected $collection_key = 'results';
  protected $resultsType = PodResult::class;
  protected $resultsDataType = 'array';
  /**
   * The result of evaluating all Pods in the request.
   *
   * @var string
   */
  public $verdict;

  /**
   * Evaluation result for each Pod contained in the request.
   *
   * @param PodResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return PodResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * The result of evaluating all Pods in the request.
   *
   * Accepted values: VERDICT_UNSPECIFIED, CONFORMANT, NON_CONFORMANT, ERROR
   *
   * @param self::VERDICT_* $verdict
   */
  public function setVerdict($verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return self::VERDICT_*
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EvaluateGkePolicyResponse::class, 'Google_Service_BinaryAuthorization_EvaluateGkePolicyResponse');
