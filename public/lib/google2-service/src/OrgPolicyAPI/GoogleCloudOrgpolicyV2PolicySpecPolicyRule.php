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

class GoogleCloudOrgpolicyV2PolicySpecPolicyRule extends \Google\Model
{
  /**
   * Setting this to true means that all values are allowed. This field can be
   * set only in policies for list constraints.
   *
   * @var bool
   */
  public $allowAll;
  protected $conditionType = GoogleTypeExpr::class;
  protected $conditionDataType = '';
  /**
   * Setting this to true means that all values are denied. This field can be
   * set only in policies for list constraints.
   *
   * @var bool
   */
  public $denyAll;
  /**
   * If `true`, then the policy is enforced. If `false`, then any configuration
   * is acceptable. This field can be set in policies for boolean constraints,
   * custom constraints and managed constraints.
   *
   * @var bool
   */
  public $enforce;
  /**
   * Optional. Required for managed constraints if parameters are defined.
   * Passes parameter values when policy enforcement is enabled. Ensure that
   * parameter value types match those defined in the constraint definition. For
   * example: ``` { "allowedLocations" : ["us-east1", "us-west1"], "allowAll" :
   * true } ```
   *
   * @var array[]
   */
  public $parameters;
  protected $valuesType = GoogleCloudOrgpolicyV2PolicySpecPolicyRuleStringValues::class;
  protected $valuesDataType = '';

  /**
   * Setting this to true means that all values are allowed. This field can be
   * set only in policies for list constraints.
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
   * must use the `resource.matchTag()`, `resource.matchTagId()`,
   * `resource.hasTagKey()`, or `resource.hasTagKeyId()` Common Expression
   * Language (CEL) function. The `resource.matchTag()` function takes the
   * following arguments: * `key_name`: the namespaced name of the tag key, with
   * the organization ID and a slash (`/`) as a prefix; for example,
   * `123456789012/environment` * `value_name`: the short name of the tag value
   * For example: `resource.matchTag('123456789012/environment, 'prod')` The
   * `resource.matchTagId()` function takes the following arguments: * `key_id`:
   * the permanent ID of the tag key; for example, `tagKeys/123456789012` *
   * `value_id`: the permanent ID of the tag value; for example,
   * `tagValues/567890123456` For example:
   * `resource.matchTagId('tagKeys/123456789012', 'tagValues/567890123456')` The
   * `resource.hasTagKey()` function takes the following argument: * `key_name`:
   * the namespaced name of the tag key, with the organization ID and a slash
   * (`/`) as a prefix; for example, `123456789012/environment` For example:
   * `resource.hasTagKey('123456789012/environment')` The
   * `resource.hasTagKeyId()` function takes the following arguments: *
   * `key_id`: the permanent ID of the tag key; for example,
   * `tagKeys/123456789012` For example:
   * `resource.hasTagKeyId('tagKeys/123456789012')`
   *
   * @param GoogleTypeExpr $condition
   */
  public function setCondition(GoogleTypeExpr $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return GoogleTypeExpr
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Setting this to true means that all values are denied. This field can be
   * set only in policies for list constraints.
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
   * If `true`, then the policy is enforced. If `false`, then any configuration
   * is acceptable. This field can be set in policies for boolean constraints,
   * custom constraints and managed constraints.
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
   * example: ``` { "allowedLocations" : ["us-east1", "us-west1"], "allowAll" :
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
   * List of values to be used for this policy rule. This field can be set only
   * in policies for list constraints.
   *
   * @param GoogleCloudOrgpolicyV2PolicySpecPolicyRuleStringValues $values
   */
  public function setValues(GoogleCloudOrgpolicyV2PolicySpecPolicyRuleStringValues $values)
  {
    $this->values = $values;
  }
  /**
   * @return GoogleCloudOrgpolicyV2PolicySpecPolicyRuleStringValues
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV2PolicySpecPolicyRule::class, 'Google_Service_OrgPolicyAPI_GoogleCloudOrgpolicyV2PolicySpecPolicyRule');
