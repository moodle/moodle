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

namespace Google\Service\SecurityPosture;

class GoogleCloudSecuritypostureV1PolicyRule extends \Google\Model
{
  /**
   * Whether to allow any value for a list constraint. Valid only for list
   * constraints.
   *
   * @var bool
   */
  public $allowAll;
  protected $conditionType = Expr::class;
  protected $conditionDataType = '';
  /**
   * Whether to deny all values for a list constraint. Valid only for list
   * constraints.
   *
   * @var bool
   */
  public $denyAll;
  /**
   * Whether to enforce the constraint. Valid only for boolean constraints.
   *
   * @var bool
   */
  public $enforce;
  /**
   * Optional. Required for managed constraints if parameters are defined.
   * Passes parameter values when policy enforcement is enabled. Ensure that
   * parameter value types match those defined in the constraint definition. For
   * example: ``` { "allowedLocations": ["us-east1", "us-west1"], "allowAll":
   * true } ```
   *
   * @var array[]
   */
  public $parameters;
  protected $resourceTypesType = ResourceTypes::class;
  protected $resourceTypesDataType = '';
  protected $valuesType = GoogleCloudSecuritypostureV1PolicyRuleStringValues::class;
  protected $valuesDataType = '';

  /**
   * Whether to allow any value for a list constraint. Valid only for list
   * constraints.
   *
   * @param bool $allowAll
   */
  public function setAllowAll($allowAll)
  {
    $this->allowAll = $allowAll;
  }
  /**
   * @return bool
   */
  public function getAllowAll()
  {
    return $this->allowAll;
  }
  /**
   * A condition that determines whether this rule is used to evaluate the
   * policy. When set, the google.type.Expr.expression field must contain 1 to
   * 10 subexpressions, joined by the `||` or `&&` operators. Each subexpression
   * must use the `resource.matchTag()` or `resource.matchTagId()` Common
   * Expression Language (CEL) function. The `resource.matchTag()` function
   * takes the following arguments: * `key_name`: the namespaced name of the tag
   * key, with the organization ID and a slash (`/`) as a prefix; for example,
   * `123456789012/environment` * `value_name`: the short name of the tag value
   * For example: `resource.matchTag('123456789012/environment, 'prod')` The
   * `resource.matchTagId()` function takes the following arguments: * `key_id`:
   * the permanent ID of the tag key; for example, `tagKeys/123456789012` *
   * `value_id`: the permanent ID of the tag value; for example,
   * `tagValues/567890123456` For example:
   * `resource.matchTagId('tagKeys/123456789012', 'tagValues/567890123456')`
   *
   * @param Expr $condition
   */
  public function setCondition(Expr $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return Expr
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Whether to deny all values for a list constraint. Valid only for list
   * constraints.
   *
   * @param bool $denyAll
   */
  public function setDenyAll($denyAll)
  {
    $this->denyAll = $denyAll;
  }
  /**
   * @return bool
   */
  public function getDenyAll()
  {
    return $this->denyAll;
  }
  /**
   * Whether to enforce the constraint. Valid only for boolean constraints.
   *
   * @param bool $enforce
   */
  public function setEnforce($enforce)
  {
    $this->enforce = $enforce;
  }
  /**
   * @return bool
   */
  public function getEnforce()
  {
    return $this->enforce;
  }
  /**
   * Optional. Required for managed constraints if parameters are defined.
   * Passes parameter values when policy enforcement is enabled. Ensure that
   * parameter value types match those defined in the constraint definition. For
   * example: ``` { "allowedLocations": ["us-east1", "us-west1"], "allowAll":
   * true } ```
   *
   * @param array[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return array[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. The resource types policies can support, only used for managed
   * constraints. Method type is `GOVERN_TAGS`.
   *
   * @param ResourceTypes $resourceTypes
   */
  public function setResourceTypes(ResourceTypes $resourceTypes)
  {
    $this->resourceTypes = $resourceTypes;
  }
  /**
   * @return ResourceTypes
   */
  public function getResourceTypes()
  {
    return $this->resourceTypes;
  }
  /**
   * The allowed and denied values for a list constraint. Valid only for list
   * constraints.
   *
   * @param GoogleCloudSecuritypostureV1PolicyRuleStringValues $values
   */
  public function setValues(GoogleCloudSecuritypostureV1PolicyRuleStringValues $values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudSecuritypostureV1PolicyRuleStringValues
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritypostureV1PolicyRule::class, 'Google_Service_SecurityPosture_GoogleCloudSecuritypostureV1PolicyRule');
