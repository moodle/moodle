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

namespace Google\Service\CloudSecurityToken;

class GoogleIdentityStsV1AccessBoundaryRule extends \Google\Collection
{
  protected $collection_key = 'availablePermissions';
  protected $availabilityConditionType = GoogleTypeExpr::class;
  protected $availabilityConditionDataType = '';
  /**
   * A list of permissions that may be allowed for use on the specified
   * resource. The only supported values in the list are IAM roles, following
   * the format of google.iam.v1.Binding.role. Example value:
   * `inRole:roles/logging.viewer` for predefined roles and
   * `inRole:organizations/{ORGANIZATION_ID}/roles/logging.viewer` for custom
   * roles.
   *
   * @var string[]
   */
  public $availablePermissions;
  /**
   * The full resource name of a Google Cloud resource entity. The format
   * definition is at https://cloud.google.com/apis/design/resource_names.
   * Example value: `//cloudresourcemanager.googleapis.com/projects/my-project`.
   *
   * @var string
   */
  public $availableResource;

  /**
   * The availability condition further constrains the access allowed by the
   * access boundary rule. If the condition evaluates to `true`, then this
   * access boundary rule will provide access to the specified resource,
   * assuming the principal has the required permissions for the resource. If
   * the condition does not evaluate to `true`, then access to the specified
   * resource will not be available. Note that all access boundary rules in an
   * access boundary are evaluated together as a union. As such, another access
   * boundary rule may allow access to the resource, even if this access
   * boundary rule does not allow access. To learn which resources support
   * conditions in their IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies). The maximum length of the `expression` field is 2048 characters.
   *
   * @param GoogleTypeExpr $availabilityCondition
   */
  public function setAvailabilityCondition(GoogleTypeExpr $availabilityCondition)
  {
    $this->availabilityCondition = $availabilityCondition;
  }
  /**
   * @return GoogleTypeExpr
   */
  public function getAvailabilityCondition()
  {
    return $this->availabilityCondition;
  }
  /**
   * A list of permissions that may be allowed for use on the specified
   * resource. The only supported values in the list are IAM roles, following
   * the format of google.iam.v1.Binding.role. Example value:
   * `inRole:roles/logging.viewer` for predefined roles and
   * `inRole:organizations/{ORGANIZATION_ID}/roles/logging.viewer` for custom
   * roles.
   *
   * @param string[] $availablePermissions
   */
  public function setAvailablePermissions($availablePermissions)
  {
    $this->availablePermissions = $availablePermissions;
  }
  /**
   * @return string[]
   */
  public function getAvailablePermissions()
  {
    return $this->availablePermissions;
  }
  /**
   * The full resource name of a Google Cloud resource entity. The format
   * definition is at https://cloud.google.com/apis/design/resource_names.
   * Example value: `//cloudresourcemanager.googleapis.com/projects/my-project`.
   *
   * @param string $availableResource
   */
  public function setAvailableResource($availableResource)
  {
    $this->availableResource = $availableResource;
  }
  /**
   * @return string
   */
  public function getAvailableResource()
  {
    return $this->availableResource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityStsV1AccessBoundaryRule::class, 'Google_Service_CloudSecurityToken_GoogleIdentityStsV1AccessBoundaryRule');
