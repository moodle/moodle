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

class GoogleCloudSecuritypostureV1CustomConstraint extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const ACTION_TYPE_ACTION_TYPE_UNSPECIFIED = 'ACTION_TYPE_UNSPECIFIED';
  /**
   * Allow the action.
   */
  public const ACTION_TYPE_ALLOW = 'ALLOW';
  /**
   * Deny the action.
   */
  public const ACTION_TYPE_DENY = 'DENY';
  protected $collection_key = 'resourceTypes';
  /**
   * Whether to allow or deny the action.
   *
   * @var string
   */
  public $actionType;
  /**
   * A Common Expression Language (CEL) condition expression that must evaluate
   * to `true` for the constraint to be enforced. The maximum length is 1000
   * characters. For example: +
   * `resource.instanceName.matches('(production|test)_(.+_)?[\d]+')`: Evaluates
   * to `true` if the resource's `instanceName` attribute contains the
   * following: + The prefix `production` or `test` + An underscore (`_`) +
   * Optional: One or more characters, followed by an underscore (`_`) + One or
   * more digits + `resource.management.auto_upgrade == true`: Evaluates to
   * `true` if the resource's `management.auto_upgrade` attribute is `true`.
   *
   * @var string
   */
  public $condition;
  /**
   * A description of the constraint. The maximum length is 2000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * A display name for the constraint. The maximum length is 200 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * The types of operations that the constraint applies to.
   *
   * @var string[]
   */
  public $methodTypes;
  /**
   * Immutable. The name of the constraint, in the format `organizations/{organi
   * zation_id}/customConstraints/custom.{custom_constraint_id}`. For example,
   * `organizations/123456789012/customConstraints/custom.createOnlyE2TypeVms`.
   * Must contain 1 to 62 characters, excluding the prefix
   * `organizations/{organization_id}/customConstraints/custom.`.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The resource type that the constraint applies to, in the format
   * `{canonical_service_name}/{resource_type_name}`. For example,
   * `compute.googleapis.com/Instance`.
   *
   * @var string[]
   */
  public $resourceTypes;
  /**
   * Output only. The last time at which the constraint was updated or created.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Whether to allow or deny the action.
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
   * A Common Expression Language (CEL) condition expression that must evaluate
   * to `true` for the constraint to be enforced. The maximum length is 1000
   * characters. For example: +
   * `resource.instanceName.matches('(production|test)_(.+_)?[\d]+')`: Evaluates
   * to `true` if the resource's `instanceName` attribute contains the
   * following: + The prefix `production` or `test` + An underscore (`_`) +
   * Optional: One or more characters, followed by an underscore (`_`) + One or
   * more digits + `resource.management.auto_upgrade == true`: Evaluates to
   * `true` if the resource's `management.auto_upgrade` attribute is `true`.
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
   * A description of the constraint. The maximum length is 2000 characters.
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
   * A display name for the constraint. The maximum length is 200 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The types of operations that the constraint applies to.
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
   * Immutable. The name of the constraint, in the format `organizations/{organi
   * zation_id}/customConstraints/custom.{custom_constraint_id}`. For example,
   * `organizations/123456789012/customConstraints/custom.createOnlyE2TypeVms`.
   * Must contain 1 to 62 characters, excluding the prefix
   * `organizations/{organization_id}/customConstraints/custom.`.
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
   * Immutable. The resource type that the constraint applies to, in the format
   * `{canonical_service_name}/{resource_type_name}`. For example,
   * `compute.googleapis.com/Instance`.
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
  /**
   * Output only. The last time at which the constraint was updated or created.
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
class_alias(GoogleCloudSecuritypostureV1CustomConstraint::class, 'Google_Service_SecurityPosture_GoogleCloudSecuritypostureV1CustomConstraint');
