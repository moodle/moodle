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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1ExplainedAccess extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const ACCESS_STATE_ACCESS_STATE_UNSPECIFIED = 'ACCESS_STATE_UNSPECIFIED';
  /**
   * The principal has the permission.
   */
  public const ACCESS_STATE_GRANTED = 'GRANTED';
  /**
   * The principal does not have the permission.
   */
  public const ACCESS_STATE_NOT_GRANTED = 'NOT_GRANTED';
  /**
   * The principal has the permission only if a condition expression evaluates
   * to `true`.
   */
  public const ACCESS_STATE_UNKNOWN_CONDITIONAL = 'UNKNOWN_CONDITIONAL';
  /**
   * The user who created the Replay does not have access to all of the policies
   * that Policy Simulator needs to evaluate.
   */
  public const ACCESS_STATE_UNKNOWN_INFO_DENIED = 'UNKNOWN_INFO_DENIED';
  protected $collection_key = 'policies';
  /**
   * Whether the principal in the access tuple has permission to access the
   * resource in the access tuple under the given policies.
   *
   * @var string
   */
  public $accessState;
  protected $errorsType = GoogleRpcStatus::class;
  protected $errorsDataType = 'array';
  protected $policiesType = GoogleCloudPolicysimulatorV1ExplainedPolicy::class;
  protected $policiesDataType = 'array';

  /**
   * Whether the principal in the access tuple has permission to access the
   * resource in the access tuple under the given policies.
   *
   * Accepted values: ACCESS_STATE_UNSPECIFIED, GRANTED, NOT_GRANTED,
   * UNKNOWN_CONDITIONAL, UNKNOWN_INFO_DENIED
   *
   * @param self::ACCESS_STATE_* $accessState
   */
  public function setAccessState($accessState)
  {
    $this->accessState = $accessState;
  }
  /**
   * @return self::ACCESS_STATE_*
   */
  public function getAccessState()
  {
    return $this->accessState;
  }
  /**
   * If the AccessState is `UNKNOWN`, this field contains a list of errors
   * explaining why the result is `UNKNOWN`. If the `AccessState` is `GRANTED`
   * or `NOT_GRANTED`, this field is omitted.
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
   * If the AccessState is `UNKNOWN`, this field contains the policies that led
   * to that result. If the `AccessState` is `GRANTED` or `NOT_GRANTED`, this
   * field is omitted.
   *
   * @param GoogleCloudPolicysimulatorV1ExplainedPolicy[] $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1ExplainedPolicy[]
   */
  public function getPolicies()
  {
    return $this->policies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1ExplainedAccess::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1ExplainedAccess');
