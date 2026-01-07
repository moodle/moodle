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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1AccessControlAction extends \Google\Model
{
  /**
   * The unknown operation type.
   */
  public const OPERATION_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Adds newly given policy bindings in the existing bindings list.
   */
  public const OPERATION_TYPE_ADD_POLICY_BINDING = 'ADD_POLICY_BINDING';
  /**
   * Removes newly given policy bindings from the existing bindings list.
   */
  public const OPERATION_TYPE_REMOVE_POLICY_BINDING = 'REMOVE_POLICY_BINDING';
  /**
   * Replaces existing policy bindings with the given policy binding list
   */
  public const OPERATION_TYPE_REPLACE_POLICY_BINDING = 'REPLACE_POLICY_BINDING';
  /**
   * Identifies the type of operation.
   *
   * @var string
   */
  public $operationType;
  protected $policyType = GoogleIamV1Policy::class;
  protected $policyDataType = '';

  /**
   * Identifies the type of operation.
   *
   * Accepted values: UNKNOWN, ADD_POLICY_BINDING, REMOVE_POLICY_BINDING,
   * REPLACE_POLICY_BINDING
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * Represents the new policy from which bindings are added, removed or
   * replaced based on the type of the operation. the policy is limited to a few
   * 10s of KB.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1AccessControlAction::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1AccessControlAction');
