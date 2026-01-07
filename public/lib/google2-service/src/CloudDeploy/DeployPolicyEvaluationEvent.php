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

class DeployPolicyEvaluationEvent extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const INVOKER_INVOKER_UNSPECIFIED = 'INVOKER_UNSPECIFIED';
  /**
   * The action is user-driven. For example, creating a rollout manually via a
   * gcloud create command.
   */
  public const INVOKER_USER = 'USER';
  /**
   * Automated action by Cloud Deploy.
   */
  public const INVOKER_DEPLOY_AUTOMATION = 'DEPLOY_AUTOMATION';
  /**
   * This should never happen.
   */
  public const VERDICT_POLICY_VERDICT_UNSPECIFIED = 'POLICY_VERDICT_UNSPECIFIED';
  /**
   * Allowed by policy. This enum value is not currently used but may be used in
   * the future. Currently logs are only generated when a request is denied by
   * policy.
   */
  public const VERDICT_ALLOWED_BY_POLICY = 'ALLOWED_BY_POLICY';
  /**
   * Denied by policy.
   */
  public const VERDICT_DENIED_BY_POLICY = 'DENIED_BY_POLICY';
  protected $collection_key = 'overrides';
  /**
   * Whether the request is allowed. Allowed is set as true if: (1) the request
   * complies with the policy; or (2) the request doesn't comply with the policy
   * but the policy was overridden; or (3) the request doesn't comply with the
   * policy but the policy was suspended
   *
   * @var bool
   */
  public $allowed;
  /**
   * The name of the `Delivery Pipeline`.
   *
   * @var string
   */
  public $deliveryPipeline;
  /**
   * The name of the `DeployPolicy`.
   *
   * @var string
   */
  public $deployPolicy;
  /**
   * Unique identifier of the `DeployPolicy`.
   *
   * @var string
   */
  public $deployPolicyUid;
  /**
   * What invoked the action (e.g. a user or automation).
   *
   * @var string
   */
  public $invoker;
  /**
   * Debug message for when a deploy policy event occurs.
   *
   * @var string
   */
  public $message;
  /**
   * Things that could have overridden the policy verdict. Overrides together
   * with verdict decide whether the request is allowed.
   *
   * @var string[]
   */
  public $overrides;
  /**
   * Unique identifier of the `Delivery Pipeline`.
   *
   * @var string
   */
  public $pipelineUid;
  /**
   * Rule id.
   *
   * @var string
   */
  public $rule;
  /**
   * Rule type (e.g. Restrict Rollouts).
   *
   * @var string
   */
  public $ruleType;
  /**
   * The name of the `Target`. This is an optional field, as a `Target` may not
   * always be applicable to a policy.
   *
   * @var string
   */
  public $target;
  /**
   * Unique identifier of the `Target`. This is an optional field, as a `Target`
   * may not always be applicable to a policy.
   *
   * @var string
   */
  public $targetUid;
  /**
   * The policy verdict of the request.
   *
   * @var string
   */
  public $verdict;

  /**
   * Whether the request is allowed. Allowed is set as true if: (1) the request
   * complies with the policy; or (2) the request doesn't comply with the policy
   * but the policy was overridden; or (3) the request doesn't comply with the
   * policy but the policy was suspended
   *
   * @param bool $allowed
   */
  public function setAllowed($allowed)
  {
    $this->allowed = $allowed;
  }
  /**
   * @return bool
   */
  public function getAllowed()
  {
    return $this->allowed;
  }
  /**
   * The name of the `Delivery Pipeline`.
   *
   * @param string $deliveryPipeline
   */
  public function setDeliveryPipeline($deliveryPipeline)
  {
    $this->deliveryPipeline = $deliveryPipeline;
  }
  /**
   * @return string
   */
  public function getDeliveryPipeline()
  {
    return $this->deliveryPipeline;
  }
  /**
   * The name of the `DeployPolicy`.
   *
   * @param string $deployPolicy
   */
  public function setDeployPolicy($deployPolicy)
  {
    $this->deployPolicy = $deployPolicy;
  }
  /**
   * @return string
   */
  public function getDeployPolicy()
  {
    return $this->deployPolicy;
  }
  /**
   * Unique identifier of the `DeployPolicy`.
   *
   * @param string $deployPolicyUid
   */
  public function setDeployPolicyUid($deployPolicyUid)
  {
    $this->deployPolicyUid = $deployPolicyUid;
  }
  /**
   * @return string
   */
  public function getDeployPolicyUid()
  {
    return $this->deployPolicyUid;
  }
  /**
   * What invoked the action (e.g. a user or automation).
   *
   * Accepted values: INVOKER_UNSPECIFIED, USER, DEPLOY_AUTOMATION
   *
   * @param self::INVOKER_* $invoker
   */
  public function setInvoker($invoker)
  {
    $this->invoker = $invoker;
  }
  /**
   * @return self::INVOKER_*
   */
  public function getInvoker()
  {
    return $this->invoker;
  }
  /**
   * Debug message for when a deploy policy event occurs.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Things that could have overridden the policy verdict. Overrides together
   * with verdict decide whether the request is allowed.
   *
   * @param string[] $overrides
   */
  public function setOverrides($overrides)
  {
    $this->overrides = $overrides;
  }
  /**
   * @return string[]
   */
  public function getOverrides()
  {
    return $this->overrides;
  }
  /**
   * Unique identifier of the `Delivery Pipeline`.
   *
   * @param string $pipelineUid
   */
  public function setPipelineUid($pipelineUid)
  {
    $this->pipelineUid = $pipelineUid;
  }
  /**
   * @return string
   */
  public function getPipelineUid()
  {
    return $this->pipelineUid;
  }
  /**
   * Rule id.
   *
   * @param string $rule
   */
  public function setRule($rule)
  {
    $this->rule = $rule;
  }
  /**
   * @return string
   */
  public function getRule()
  {
    return $this->rule;
  }
  /**
   * Rule type (e.g. Restrict Rollouts).
   *
   * @param string $ruleType
   */
  public function setRuleType($ruleType)
  {
    $this->ruleType = $ruleType;
  }
  /**
   * @return string
   */
  public function getRuleType()
  {
    return $this->ruleType;
  }
  /**
   * The name of the `Target`. This is an optional field, as a `Target` may not
   * always be applicable to a policy.
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }
  /**
   * Unique identifier of the `Target`. This is an optional field, as a `Target`
   * may not always be applicable to a policy.
   *
   * @param string $targetUid
   */
  public function setTargetUid($targetUid)
  {
    $this->targetUid = $targetUid;
  }
  /**
   * @return string
   */
  public function getTargetUid()
  {
    return $this->targetUid;
  }
  /**
   * The policy verdict of the request.
   *
   * Accepted values: POLICY_VERDICT_UNSPECIFIED, ALLOWED_BY_POLICY,
   * DENIED_BY_POLICY
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
class_alias(DeployPolicyEvaluationEvent::class, 'Google_Service_CloudDeploy_DeployPolicyEvaluationEvent');
