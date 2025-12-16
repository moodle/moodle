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

namespace Google\Service\CloudDeploy;

class PolicyViolationDetails extends \Google\Model
{
  /**
   * User readable message about why the request violated a policy. This is not
   * intended for machine parsing.
   *
   * @var string
   */
  public $failureMessage;
  /**
   * Name of the policy that was violated. Policy resource will be in the format
   * of `projects/{project}/locations/{location}/policies/{policy}`.
   *
   * @var string
   */
  public $policy;
  /**
   * Id of the rule that triggered the policy violation.
   *
   * @var string
   */
  public $ruleId;

  /**
   * User readable message about why the request violated a policy. This is not
   * intended for machine parsing.
   *
   * @param string $failureMessage
   */
  public function setFailureMessage($failureMessage)
  {
    $this->failureMessage = $failureMessage;
  }
  /**
   * @return string
   */
  public function getFailureMessage()
  {
    return $this->failureMessage;
  }
  /**
   * Name of the policy that was violated. Policy resource will be in the format
   * of `projects/{project}/locations/{location}/policies/{policy}`.
   *
   * @param string $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return string
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * Id of the rule that triggered the policy violation.
   *
   * @param string $ruleId
   */
  public function setRuleId($ruleId)
  {
    $this->ruleId = $ruleId;
  }
  /**
   * @return string
   */
  public function getRuleId()
  {
    return $this->ruleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyViolationDetails::class, 'Google_Service_CloudDeploy_PolicyViolationDetails');
