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

namespace Google\Service\BigtableAdmin\Resource;

use Google\Service\BigtableAdmin\AuthorizedView;
use Google\Service\BigtableAdmin\BigtableadminEmpty;
use Google\Service\BigtableAdmin\GetIamPolicyRequest;
use Google\Service\BigtableAdmin\ListAuthorizedViewsResponse;
use Google\Service\BigtableAdmin\Operation;
use Google\Service\BigtableAdmin\Policy;
use Google\Service\BigtableAdmin\SetIamPolicyRequest;
use Google\Service\BigtableAdmin\TestIamPermissionsRequest;
use Google\Service\BigtableAdmin\TestIamPermissionsResponse;

/**
 * The "authorizedViews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigtableadminService = new Google\Service\BigtableAdmin(...);
 *   $authorizedViews = $bigtableadminService->projects_instances_tables_authorizedViews;
 *  </code>
 */
class ProjectsInstancesTablesAuthorizedViews extends \Google\Service\Resource
{
  /**
   * Creates a new AuthorizedView in a table. (authorizedViews.create)
   *
   * @param string $parent Required. This is the name of the table the
   * AuthorizedView belongs to. Values are of the form
   * `projects/{project}/instances/{instance}/tables/{table}`.
   * @param AuthorizedView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string authorizedViewId Required. The id of the AuthorizedView to
   * create. This AuthorizedView must not already exist. The `authorized_view_id`
   * appended to `parent` forms the full AuthorizedView name of the form `projects
   * /{project}/instances/{instance}/tables/{table}/authorizedView/{authorized_vie
   * w}`.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, AuthorizedView $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Permanently deletes a specified AuthorizedView. (authorizedViews.delete)
   *
   * @param string $name Required. The unique name of the AuthorizedView to be
   * deleted. Values are of the form `projects/{project}/instances/{instance}/tabl
   * es/{table}/authorizedViews/{authorized_view}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the AuthorizedView. If
   * an etag is provided and does not match the current etag of the
   * AuthorizedView, deletion will be blocked and an ABORTED error will be
   * returned.
   * @return BigtableadminEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], BigtableadminEmpty::class);
  }
  /**
   * Gets information from a specified AuthorizedView. (authorizedViews.get)
   *
   * @param string $name Required. The unique name of the requested
   * AuthorizedView. Values are of the form `projects/{project}/instances/{instanc
   * e}/tables/{table}/authorizedViews/{authorized_view}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. The resource_view to be applied to the
   * returned AuthorizedView's fields. Default to BASIC.
   * @return AuthorizedView
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AuthorizedView::class);
  }
  /**
   * Gets the access control policy for a Bigtable resource. Returns an empty
   * policy if the resource exists but does not have a policy set.
   * (authorizedViews.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, GetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Lists all AuthorizedViews from a specific table.
   * (authorizedViews.listProjectsInstancesTablesAuthorizedViews)
   *
   * @param string $parent Required. The unique name of the table for which
   * AuthorizedViews should be listed. Values are of the form
   * `projects/{project}/instances/{instance}/tables/{table}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of results per page. A
   * page_size of zero lets the server choose the number of items to return. A
   * page_size which is strictly positive will return at most that many items. A
   * negative page_size will cause an error. Following the first request,
   * subsequent paginated calls are not required to pass a page_size. If a
   * page_size is set in subsequent calls, it must match the page_size given in
   * the first request.
   * @opt_param string pageToken Optional. The value of `next_page_token` returned
   * by a previous call.
   * @opt_param string view Optional. The resource_view to be applied to the
   * returned AuthorizedViews' fields. Default to NAME_ONLY.
   * @return ListAuthorizedViewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsInstancesTablesAuthorizedViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAuthorizedViewsResponse::class);
  }
  /**
   * Updates an AuthorizedView in a table. (authorizedViews.patch)
   *
   * @param string $name Identifier. The name of this AuthorizedView. Values are
   * of the form `projects/{project}/instances/{instance}/tables/{table}/authorize
   * dViews/{authorized_view}`
   * @param AuthorizedView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool ignoreWarnings Optional. If true, ignore the safety checks
   * when updating the AuthorizedView.
   * @opt_param string updateMask Optional. The list of fields to update. A mask
   * specifying which fields in the AuthorizedView resource should be updated.
   * This mask is relative to the AuthorizedView resource, not to the request
   * message. A field will be overwritten if it is in the mask. If empty, all
   * fields set in the request will be overwritten. A special value `*` means to
   * overwrite all fields (including fields not set in the request).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, AuthorizedView $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on a Bigtable resource. Replaces any existing
   * policy. (authorizedViews.setIamPolicy)
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
   * Returns permissions that the caller has on the specified Bigtable resource.
   * (authorizedViews.testIamPermissions)
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
class_alias(ProjectsInstancesTablesAuthorizedViews::class, 'Google_Service_BigtableAdmin_Resource_ProjectsInstancesTablesAuthorizedViews');
