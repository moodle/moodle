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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResult extends \Google\Collection
{
  /**
   * Unknown value.
   */
  public const VERDICT_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * There was no policy violation.
   */
  public const VERDICT_ALLOW = 'ALLOW';
  /**
   * Processing was blocked by the customer policy.
   */
  public const VERDICT_BLOCK = 'BLOCK';
  protected $collection_key = 'policyResults';
  protected $policyResultsType = GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultPolicyEnforcementResult::class;
  protected $policyResultsDataType = 'array';
  /**
   * Final verdict of the customer policy enforcement. If only one policy
   * blocked the processing, the verdict is BLOCK.
   *
   * @var string
   */
  public $verdict;

  /**
   * Customer policy enforcement results. Populated only if the assist call was
   * skipped due to a policy violation. It contains results from those filters
   * that blocked the processing of the query.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultPolicyEnforcementResult[] $policyResults
   */
  public function setPolicyResults($policyResults)
  {
    $this->policyResults = $policyResults;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResultPolicyEnforcementResult[]
   */
  public function getPolicyResults()
  {
    return $this->policyResults;
  }
  /**
   * Final verdict of the customer policy enforcement. If only one policy
   * blocked the processing, the verdict is BLOCK.
   *
   * Accepted values: UNSPECIFIED, ALLOW, BLOCK
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
class_alias(GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResult::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAssistAnswerCustomerPolicyEnforcementResult');
