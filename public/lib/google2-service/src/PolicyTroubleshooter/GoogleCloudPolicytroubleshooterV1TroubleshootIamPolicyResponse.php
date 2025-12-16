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

class GoogleCloudPolicytroubleshooterV1TroubleshootIamPolicyResponse extends \Google\Collection
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
  protected $collection_key = 'explainedPolicies';
  /**
   * Indicates whether the principal has the specified permission for the
   * specified resource, based on evaluating all of the applicable IAM policies.
   *
   * @var string
   */
  public $access;
  protected $errorsType = GoogleRpcStatus::class;
  protected $errorsDataType = 'array';
  protected $explainedPoliciesType = GoogleCloudPolicytroubleshooterV1ExplainedPolicy::class;
  protected $explainedPoliciesDataType = 'array';

  /**
   * Indicates whether the principal has the specified permission for the
   * specified resource, based on evaluating all of the applicable IAM policies.
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
   * The general errors contained in the troubleshooting response.
   *
   * @param GoogleRpcStatus[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * List of IAM policies that were evaluated to check the principal's
   * permissions, with annotations to indicate how each policy contributed to
   * the final result. The list of policies can include the policy for the
   * resource itself. It can also include policies that are inherited from
   * higher levels of the resource hierarchy, including the organization, the
   * folder, and the project. To learn more about the resource hierarchy, see
   * https://cloud.google.com/iam/help/resource-hierarchy.
   *
   * @param GoogleCloudPolicytroubleshooterV1ExplainedPolicy[] $explainedPolicies
   */
  public function setExplainedPolicies($explainedPolicies)
  {
    $this->explainedPolicies = $explainedPolicies;
  }
  /**
   * @return GoogleCloudPolicytroubleshooterV1ExplainedPolicy[]
   */
  public function getExplainedPolicies()
  {
    return $this->explainedPolicies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicytroubleshooterV1TroubleshootIamPolicyResponse::class, 'Google_Service_PolicyTroubleshooter_GoogleCloudPolicytroubleshooterV1TroubleshootIamPolicyResponse');
