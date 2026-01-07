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

namespace Google\Service\NetworkSecurity;

class AuthorizationPolicy extends \Google\Collection
{
  /**
   * Default value.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Grant access.
   */
  public const ACTION_ALLOW = 'ALLOW';
  /**
   * Deny access. Deny rules should be avoided unless they are used to provide a
   * default "deny all" fallback.
   */
  public const ACTION_DENY = 'DENY';
  protected $collection_key = 'rules';
  /**
   * Required. The action to take when a rule match is found. Possible values
   * are "ALLOW" or "DENY".
   *
   * @var string
   */
  public $action;
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Free-text description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Set of label tags associated with the AuthorizationPolicy
   * resource.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. Name of the AuthorizationPolicy resource. It matches pattern
   * `projects/{project}/locations/{location}/authorizationPolicies/`.
   *
   * @var string
   */
  public $name;
  protected $rulesType = Rule::class;
  protected $rulesDataType = 'array';
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The action to take when a rule match is found. Possible values
   * are "ALLOW" or "DENY".
   *
   * Accepted values: ACTION_UNSPECIFIED, ALLOW, DENY
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. Free-text description of the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Set of label tags associated with the AuthorizationPolicy
   * resource.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. Name of the AuthorizationPolicy resource. It matches pattern
   * `projects/{project}/locations/{location}/authorizationPolicies/`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. List of rules to match. Note that at least one of the rules must
   * match in order for the action specified in the 'action' field to be taken.
   * A rule is a match if there is a matching source and destination. If left
   * blank, the action specified in the `action` field will be applied on every
   * request.
   *
   * @param Rule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return Rule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthorizationPolicy::class, 'Google_Service_NetworkSecurity_AuthorizationPolicy');
