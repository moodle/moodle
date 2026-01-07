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

namespace Google\Service\OrgPolicyAPI;

class GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinition extends \Google\Collection
{
  /**
   * This is only used for distinguishing unset values and should never be used.
   * Results in an error.
   */
  public const ACTION_TYPE_ACTION_TYPE_UNSPECIFIED = 'ACTION_TYPE_UNSPECIFIED';
  /**
   * Allowed action type.
   */
  public const ACTION_TYPE_ALLOW = 'ALLOW';
  /**
   * Deny action type.
   */
  public const ACTION_TYPE_DENY = 'DENY';
  protected $collection_key = 'resourceTypes';
  /**
   * Allow or deny type.
   *
   * @var string
   */
  public $actionType;
  /**
   * Org policy condition/expression. For example:
   * `resource.instanceName.matches("(production|test)_(.+_)?[\d]+")` or,
   * `resource.management.auto_upgrade == true` The max length of the condition
   * is 1000 characters.
   *
   * @var string
   */
  public $condition;
  /**
   * All the operations being applied for this constraint.
   *
   * @var string[]
   */
  public $methodTypes;
  protected $parametersType = GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameter::class;
  protected $parametersDataType = 'map';
  /**
   * The resource instance type on which this policy applies. Format will be of
   * the form : `/` Example: * `compute.googleapis.com/Instance`.
   *
   * @var string[]
   */
  public $resourceTypes;

  /**
   * Allow or deny type.
   *
   * Accepted values: ACTION_TYPE_UNSPECIFIED, ALLOW, DENY
   *
   * @param self::ACTION_TYPE_* $actionType
   */
  public function setActionType($actionType)
  {
    $this->actionType = $actionType;
  }
  /**
   * @return self::ACTION_TYPE_*
   */
  public function getActionType()
  {
    return $this->actionType;
  }
  /**
   * Org policy condition/expression. For example:
   * `resource.instanceName.matches("(production|test)_(.+_)?[\d]+")` or,
   * `resource.management.auto_upgrade == true` The max length of the condition
   * is 1000 characters.
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * All the operations being applied for this constraint.
   *
   * @param string[] $methodTypes
   */
  public function setMethodTypes($methodTypes)
  {
    $this->methodTypes = $methodTypes;
  }
  /**
   * @return string[]
   */
  public function getMethodTypes()
  {
    return $this->methodTypes;
  }
  /**
   * Stores the structure of `Parameters` used by the constraint condition. The
   * key of `map` represents the name of the parameter.
   *
   * @param GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinitionParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * The resource instance type on which this policy applies. Format will be of
   * the form : `/` Example: * `compute.googleapis.com/Instance`.
   *
   * @param string[] $resourceTypes
   */
  public function setResourceTypes($resourceTypes)
  {
    $this->resourceTypes = $resourceTypes;
  }
  /**
   * @return string[]
   */
  public function getResourceTypes()
  {
    return $this->resourceTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinition::class, 'Google_Service_OrgPolicyAPI_GoogleCloudOrgpolicyV2ConstraintCustomConstraintDefinition');
