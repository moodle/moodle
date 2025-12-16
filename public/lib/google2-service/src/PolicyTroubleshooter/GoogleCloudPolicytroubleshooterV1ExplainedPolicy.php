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

namespace Google\Service\PolicyTroubleshooter;

class GoogleCloudPolicytroubleshooterV1ExplainedPolicy extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const ACCESS_ACCESS_STATE_UNSPECIFIED = 'ACCESS_STATE_UNSPECIFIED';
  /**
   * The principal has the permission.
   */
  public const ACCESS_GRANTED = 'GRANTED';
  /**
   * The principal does not have the permission.
   */
  public const ACCESS_NOT_GRANTED = 'NOT_GRANTED';
  /**
   * The principal has the permission only if a condition expression evaluates
   * to `true`.
   */
  public const ACCESS_UNKNOWN_CONDITIONAL = 'UNKNOWN_CONDITIONAL';
  /**
   * The sender of the request does not have access to all of the policies that
   * Policy Troubleshooter needs to evaluate.
   */
  public const ACCESS_UNKNOWN_INFO_DENIED = 'UNKNOWN_INFO_DENIED';
  /**
   * Default value. This value is unused.
   */
  public const RELEVANCE_HEURISTIC_RELEVANCE_UNSPECIFIED = 'HEURISTIC_RELEVANCE_UNSPECIFIED';
  /**
   * The data point has a limited effect on the result. Changing the data point
   * is unlikely to affect the overall determination.
   */
  public const RELEVANCE_NORMAL = 'NORMAL';
  /**
   * The data point has a strong effect on the result. Changing the data point
   * is likely to affect the overall determination.
   */
  public const RELEVANCE_HIGH = 'HIGH';
  protected $collection_key = 'bindingExplanations';
  /**
   * Indicates whether _this policy_ provides the specified permission to the
   * specified principal for the specified resource. This field does _not_
   * indicate whether the principal actually has the permission for the
   * resource. There might be another policy that overrides this policy. To
   * determine whether the principal actually has the permission, use the
   * `access` field in the TroubleshootIamPolicyResponse.
   *
   * @var string
   */
  public $access;
  protected $bindingExplanationsType = GoogleCloudPolicytroubleshooterV1BindingExplanation::class;
  protected $bindingExplanationsDataType = 'array';
  /**
   * The full resource name that identifies the resource. For example,
   * `//compute.googleapis.com/projects/my-project/zones/us-
   * central1-a/instances/my-instance`. If the sender of the request does not
   * have access to the policy, this field is omitted. For examples of full
   * resource names for Google Cloud services, see
   * https://cloud.google.com/iam/help/troubleshooter/full-resource-names.
   *
   * @var string
   */
  public $fullResourceName;
  protected $policyType = GoogleIamV1Policy::class;
  protected $policyDataType = '';
  /**
   * The relevance of this policy to the overall determination in the
   * TroubleshootIamPolicyResponse. If the sender of the request does not have
   * access to the policy, this field is omitted.
   *
   * @var string
   */
  public $relevance;

  /**
   * Indicates whether _this policy_ provides the specified permission to the
   * specified principal for the specified resource. This field does _not_
   * indicate whether the principal actually has the permission for the
   * resource. There might be another policy that overrides this policy. To
   * determine whether the principal actually has the permission, use the
   * `access` field in the TroubleshootIamPolicyResponse.
   *
   * Accepted values: ACCESS_STATE_UNSPECIFIED, GRANTED, NOT_GRANTED,
   * UNKNOWN_CONDITIONAL, UNKNOWN_INFO_DENIED
   *
   * @param self::ACCESS_* $access
   */
  public function setAccess($access)
  {
    $this->access = $access;
  }
  /**
   * @return self::ACCESS_*
   */
  public function getAccess()
  {
    return $this->access;
  }
  /**
   * Details about how each binding in the policy affects the principal's
   * ability, or inability, to use the permission for the resource. If the
   * sender of the request does not have access to the policy, this field is
   * omitted.
   *
   * @param GoogleCloudPolicytroubleshooterV1BindingExplanation[] $bindingExplanations
   */
  public function setBindingExplanations($bindingExplanations)
  {
    $this->bindingExplanations = $bindingExplanations;
  }
  /**
   * @return GoogleCloudPolicytroubleshooterV1BindingExplanation[]
   */
  public function getBindingExplanations()
  {
    return $this->bindingExplanations;
  }
  /**
   * The full resource name that identifies the resource. For example,
   * `//compute.googleapis.com/projects/my-project/zones/us-
   * central1-a/instances/my-instance`. If the sender of the request does not
   * have access to the policy, this field is omitted. For examples of full
   * resource names for Google Cloud services, see
   * https://cloud.google.com/iam/help/troubleshooter/full-resource-names.
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * The IAM policy attached to the resource. If the sender of the request does
   * not have access to the policy, this field is empty.
   *
   * @param GoogleIamV1Policy $policy
   */
  public function setPolicy(GoogleIamV1Policy $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return GoogleIamV1Policy
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * The relevance of this policy to the overall determination in the
   * TroubleshootIamPolicyResponse. If the sender of the request does not have
   * access to the policy, this field is omitted.
   *
   * Accepted values: HEURISTIC_RELEVANCE_UNSPECIFIED, NORMAL, HIGH
   *
   * @param self::RELEVANCE_* $relevance
   */
  public function setRelevance($relevance)
  {
    $this->relevance = $relevance;
  }
  /**
   * @return self::RELEVANCE_*
   */
  public function getRelevance()
  {
    return $this->relevance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicytroubleshooterV1ExplainedPolicy::class, 'Google_Service_PolicyTroubleshooter_GoogleCloudPolicytroubleshooterV1ExplainedPolicy');
