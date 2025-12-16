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

namespace Google\Service\GKEHub\Resource;

use Google\Service\GKEHub\ListBoundMembershipsResponse;
use Google\Service\GKEHub\ListPermittedScopesResponse;
use Google\Service\GKEHub\ListScopesResponse;
use Google\Service\GKEHub\Operation;
use Google\Service\GKEHub\Policy;
use Google\Service\GKEHub\Scope;
use Google\Service\GKEHub\SetIamPolicyRequest;
use Google\Service\GKEHub\TestIamPermissionsRequest;
use Google\Service\GKEHub\TestIamPermissionsResponse;

/**
 * The "scopes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gkehubService = new Google\Service\GKEHub(...);
 *   $scopes = $gkehubService->projects_locations_scopes;
 *  </code>
 */
class ProjectsLocationsScopes extends \Google\Service\Resource
{
  /**
   * Creates a Scope. (scopes.create)
   *
   * @param string $parent Required. The parent (project and location) where the
   * Scope will be created. Specified in the format `projects/locations`.
   * @param Scope $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string scopeId Required. Client chosen ID for the Scope.
   * `scope_id` must be a ????
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Scope $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a Scope. (scopes.delete)
   *
   * @param string $name Required. The Scope resource name in the format
   * `projects/locations/scopes`.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Returns the details of a Scope. (scopes.get)
   *
   * @param string $name Required. The Scope resource name in the format
   * `projects/locations/scopes`.
   * @param array $optParams Optional parameters.
   * @return Scope
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Scope::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (scopes.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Lists Scopes. (scopes.listProjectsLocationsScopes)
   *
   * @param string $parent Required. The parent (project and location) where the
   * Scope will be listed. Specified in the format `projects/locations`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. When requesting a 'page' of resources,
   * `page_size` specifies number of resources to return. If unspecified or set to
   * 0, all resources will be returned.
   * @opt_param string pageToken Optional. Token returned by previous call to
   * `ListScopes` which specifies the position in the list from where to continue
   * listing the resources.
   * @return ListScopesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsScopes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListScopesResponse::class);
  }
  /**
   * Lists Memberships bound to a Scope. The response includes relevant
   * Memberships from all regions. (scopes.listMemberships)
   *
   * @param string $scopeName Required. Name of the Scope, in the format
   * `projects/locations/global/scopes`, to which the Memberships are bound.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Lists Memberships that match the filter
   * expression, following the syntax outlined in https://google.aip.dev/160.
   * Currently, filtering can be done only based on Memberships's `name`,
   * `labels`, `create_time`, `update_time`, and `unique_id`.
   * @opt_param int pageSize Optional. When requesting a 'page' of resources,
   * `page_size` specifies number of resources to return. If unspecified or set to
   * 0, all resources will be returned. Pagination is currently not supported;
   * therefore, setting this field does not have any impact for now.
   * @opt_param string pageToken Optional. Token returned by previous call to
   * `ListBoundMemberships` which specifies the position in the list from where to
   * continue listing the resources.
   * @return ListBoundMembershipsResponse
   * @throws \Google\Service\Exception
   */
  public function listMemberships($scopeName, $optParams = [])
  {
    $params = ['scopeName' => $scopeName];
    $params = array_merge($params, $optParams);
    return $this->call('listMemberships', [$params], ListBoundMembershipsResponse::class);
  }
  /**
   * Lists permitted Scopes. (scopes.listPermitted)
   *
   * @param string $parent Required. The parent (project and location) where the
   * Scope will be listed. Specified in the format `projects/locations`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. When requesting a 'page' of resources,
   * `page_size` specifies number of resources to return. If unspecified or set to
   * 0, all resources will be returned.
   * @opt_param string pageToken Optional. Token returned by previous call to
   * `ListPermittedScopes` which specifies the position in the list from where to
   * continue listing the resources.
   * @return ListPermittedScopesResponse
   * @throws \Google\Service\Exception
   */
  public function listPermitted($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('listPermitted', [$params], ListPermittedScopesResponse::class);
  }
  /**
   * Updates a scopes. (scopes.patch)
   *
   * @param string $name The resource name for the scope
   * `projects/{project}/locations/{location}/scopes/{scope}`
   * @param Scope $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The fields to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Scope $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (scopes.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning. (scopes.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsScopes::class, 'Google_Service_GKEHub_Resource_ProjectsLocationsScopes');
