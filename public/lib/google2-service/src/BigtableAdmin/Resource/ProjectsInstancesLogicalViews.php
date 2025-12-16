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

use Google\Service\BigtableAdmin\BigtableadminEmpty;
use Google\Service\BigtableAdmin\GetIamPolicyRequest;
use Google\Service\BigtableAdmin\ListLogicalViewsResponse;
use Google\Service\BigtableAdmin\LogicalView;
use Google\Service\BigtableAdmin\Operation;
use Google\Service\BigtableAdmin\Policy;
use Google\Service\BigtableAdmin\SetIamPolicyRequest;
use Google\Service\BigtableAdmin\TestIamPermissionsRequest;
use Google\Service\BigtableAdmin\TestIamPermissionsResponse;

/**
 * The "logicalViews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigtableadminService = new Google\Service\BigtableAdmin(...);
 *   $logicalViews = $bigtableadminService->projects_instances_logicalViews;
 *  </code>
 */
class ProjectsInstancesLogicalViews extends \Google\Service\Resource
{
  /**
   * Creates a logical view within an instance. (logicalViews.create)
   *
   * @param string $parent Required. The parent instance where this logical view
   * will be created. Format: `projects/{project}/instances/{instance}`.
   * @param LogicalView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string logicalViewId Required. The ID to use for the logical view,
   * which will become the final component of the logical view's resource name.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, LogicalView $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a logical view from an instance. (logicalViews.delete)
   *
   * @param string $name Required. The unique name of the logical view to be
   * deleted. Format:
   * `projects/{project}/instances/{instance}/logicalViews/{logical_view}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the logical view. If an
   * etag is provided and does not match the current etag of the logical view,
   * deletion will be blocked and an ABORTED error will be returned.
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
   * Gets information about a logical view. (logicalViews.get)
   *
   * @param string $name Required. The unique name of the requested logical view.
   * Values are of the form
   * `projects/{project}/instances/{instance}/logicalViews/{logical_view}`.
   * @param array $optParams Optional parameters.
   * @return LogicalView
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], LogicalView::class);
  }
  /**
   * Gets the access control policy for an instance resource. Returns an empty
   * policy if an instance exists but does not have a policy set.
   * (logicalViews.getIamPolicy)
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
   * Lists information about logical views in an instance.
   * (logicalViews.listProjectsInstancesLogicalViews)
   *
   * @param string $parent Required. The unique name of the instance for which the
   * list of logical views is requested. Values are of the form
   * `projects/{project}/instances/{instance}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of logical views to
   * return. The service may return fewer than this value
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListLogicalViews` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListLogicalViews` must match
   * the call that provided the page token.
   * @return ListLogicalViewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsInstancesLogicalViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListLogicalViewsResponse::class);
  }
  /**
   * Updates a logical view within an instance. (logicalViews.patch)
   *
   * @param string $name Identifier. The unique name of the logical view. Format:
   * `projects/{project}/instances/{instance}/logicalViews/{logical_view}`
   * @param LogicalView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, LogicalView $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on an instance resource. Replaces any existing
   * policy. (logicalViews.setIamPolicy)
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
   * Returns permissions that the caller has on the specified instance resource.
   * (logicalViews.testIamPermissions)
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
class_alias(ProjectsInstancesLogicalViews::class, 'Google_Service_BigtableAdmin_Resource_ProjectsInstancesLogicalViews');
