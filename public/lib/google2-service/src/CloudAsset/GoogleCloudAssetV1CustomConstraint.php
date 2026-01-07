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

namespace Google\Service\CloudAsset;

class GoogleCloudAssetV1CustomConstraint extends \Google\Collection
{
  /**
   * Unspecified. Will results in user error.
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
   * Organization Policy condition/expression. For example:
   * `resource.instanceName.matches("(production|test)_(.+_)?[\d]+")'` or,
   * `resource.management.auto_upgrade == true`
   *
   * @var string
   */
  public $condition;
  /**
   * Detailed information about this custom policy constraint.
   *
   * @var string
   */
  public $description;
  /**
   * One line display name for the UI.
   *
   * @var string
   */
  public $displayName;
  /**
   * All the operations being applied for this constraint.
   *
   * @var string[]
   */
  public $methodTypes;
  /**
   * Name of the constraint. This is unique within the organization. Format of
   * the name should be *
   * `organizations/{organization_id}/customConstraints/{custom_constraint_id}`
   * Example : "organizations/123/customConstraints/custom.createOnlyE2TypeVms"
   *
   * @var string
   */
  public $name;
  /**
   * The Resource Instance type on which this policy applies to. Format will be
   * of the form : "/" Example: * `compute.googleapis.com/Instance`.
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
   * Organization Policy condition/expression. For example:
   * `resource.instanceName.matches("(production|test)_(.+_)?[\d]+")'` or,
   * `resource.management.auto_upgrade == true`
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
   * Detailed information about this custom policy constraint.
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
   * One line display name for the UI.
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
   * Name of the constraint. This is unique within the organization. Format of
   * the name should be *
   * `organizations/{organization_id}/customConstraints/{custom_constraint_id}`
   * Example : "organizations/123/customConstraints/custom.createOnlyE2TypeVms"
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
   * The Resource Instance type on which this policy applies to. Format will be
   * of the form : "/" Example: * `compute.googleapis.com/Instance`.
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
class_alias(GoogleCloudAssetV1CustomConstraint::class, 'Google_Service_CloudAsset_GoogleCloudAssetV1CustomConstraint');
