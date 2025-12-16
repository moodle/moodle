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
use Google\Service\BigtableAdmin\ListMaterializedViewsResponse;
use Google\Service\BigtableAdmin\MaterializedView;
use Google\Service\BigtableAdmin\Operation;
use Google\Service\BigtableAdmin\Policy;
use Google\Service\BigtableAdmin\SetIamPolicyRequest;
use Google\Service\BigtableAdmin\TestIamPermissionsRequest;
use Google\Service\BigtableAdmin\TestIamPermissionsResponse;

/**
 * The "materializedViews" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigtableadminService = new Google\Service\BigtableAdmin(...);
 *   $materializedViews = $bigtableadminService->projects_instances_materializedViews;
 *  </code>
 */
class ProjectsInstancesMaterializedViews extends \Google\Service\Resource
{
  /**
   * Creates a materialized view within an instance. (materializedViews.create)
   *
   * @param string $parent Required. The parent instance where this materialized
   * view will be created. Format: `projects/{project}/instances/{instance}`.
   * @param MaterializedView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string materializedViewId Required. The ID to use for the
   * materialized view, which will become the final component of the materialized
   * view's resource name.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, MaterializedView $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a materialized view from an instance. (materializedViews.delete)
   *
   * @param string $name Required. The unique name of the materialized view to be
   * deleted. Format: `projects/{project}/instances/{instance}/materializedViews/{
   * materialized_view}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the materialized view.
   * If an etag is provided and does not match the current etag of the
   * materialized view, deletion will be blocked and an ABORTED error will be
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
   * Gets information about a materialized view. (materializedViews.get)
   *
   * @param string $name Required. The unique name of the requested materialized
   * view. Values are of the form `projects/{project}/instances/{instance}/materia
   * lizedViews/{materialized_view}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. Describes which of the materialized view's
   * fields should be populated in the response. Defaults to SCHEMA_VIEW.
   * @return MaterializedView
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MaterializedView::class);
  }
  /**
   * Gets the access control policy for an instance resource. Returns an empty
   * policy if an instance exists but does not have a policy set.
   * (materializedViews.getIamPolicy)
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
   * Lists information about materialized views in an instance.
   * (materializedViews.listProjectsInstancesMaterializedViews)
   *
   * @param string $parent Required. The unique name of the instance for which the
   * list of materialized views is requested. Values are of the form
   * `projects/{project}/instances/{instance}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of materialized views to
   * return. The service may return fewer than this value
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListMaterializedViews` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListMaterializedViews`
   * must match the call that provided the page token.
   * @opt_param string view Optional. Describes which of the materialized view's
   * fields should be populated in the response. For now, only the default value
   * SCHEMA_VIEW is supported.
   * @return ListMaterializedViewsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsInstancesMaterializedViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMaterializedViewsResponse::class);
  }
  /**
   * Updates a materialized view within an instance. (materializedViews.patch)
   *
   * @param string $name Identifier. The unique name of the materialized view.
   * Format: `projects/{project}/instances/{instance}/materializedViews/{materiali
   * zed_view}` Views: `SCHEMA_VIEW`, `REPLICATION_VIEW`, `FULL`.
   * @param MaterializedView $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to update.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, MaterializedView $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on an instance resource. Replaces any existing
   * policy. (materializedViews.setIamPolicy)
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
   * (materializedViews.testIamPermissions)
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
class_alias(ProjectsInstancesMaterializedViews::class, 'Google_Service_BigtableAdmin_Resource_ProjectsInstancesMaterializedViews');
