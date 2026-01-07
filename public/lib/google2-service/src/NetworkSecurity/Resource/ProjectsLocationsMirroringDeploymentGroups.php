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

namespace Google\Service\NetworkSecurity\Resource;

use Google\Service\NetworkSecurity\ListMirroringDeploymentGroupsResponse;
use Google\Service\NetworkSecurity\MirroringDeploymentGroup;
use Google\Service\NetworkSecurity\Operation;

/**
 * The "mirroringDeploymentGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $mirroringDeploymentGroups = $networksecurityService->projects_locations_mirroringDeploymentGroups;
 *  </code>
 */
class ProjectsLocationsMirroringDeploymentGroups extends \Google\Service\Resource
{
  /**
   * Creates a deployment group in a given project and location. See
   * https://google.aip.dev/133. (mirroringDeploymentGroups.create)
   *
   * @param string $parent Required. The parent resource where this deployment
   * group will be created. Format: projects/{project}/locations/{location}
   * @param MirroringDeploymentGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string mirroringDeploymentGroupId Required. The ID to use for the
   * new deployment group, which will become the final component of the deployment
   * group's resource name.
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Must be a UUID4. This request is only idempotent if a `request_id` is
   * provided. See https://google.aip.dev/155 for more details.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, MirroringDeploymentGroup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a deployment group. See https://google.aip.dev/135.
   * (mirroringDeploymentGroups.delete)
   *
   * @param string $name Required. The deployment group to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Must be a UUID4. This request is only idempotent if a `request_id` is
   * provided. See https://google.aip.dev/155 for more details.
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
   * Gets a specific deployment group. See https://google.aip.dev/131.
   * (mirroringDeploymentGroups.get)
   *
   * @param string $name Required. The name of the deployment group to retrieve.
   * Format: projects/{project}/locations/{location}/mirroringDeploymentGroups/{mi
   * rroring_deployment_group}
   * @param array $optParams Optional parameters.
   * @return MirroringDeploymentGroup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MirroringDeploymentGroup::class);
  }
  /**
   * Lists deployment groups in a given project and location. See
   * https://google.aip.dev/132.
   * (mirroringDeploymentGroups.listProjectsLocationsMirroringDeploymentGroups)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * deployment groups. Example: `projects/123456789/locations/global`. See
   * https://google.aip.dev/132 for more details.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression. See
   * https://google.aip.dev/160#filtering for more details.
   * @opt_param string orderBy Optional. Sort expression. See
   * https://google.aip.dev/132#ordering for more details.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default. See https://google.aip.dev/158 for more details.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListMirroringDeploymentGroups` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListMirroringDeploymentGroups` must match the call that provided the page
   * token. See https://google.aip.dev/158 for more details.
   * @return ListMirroringDeploymentGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMirroringDeploymentGroups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMirroringDeploymentGroupsResponse::class);
  }
  /**
   * Updates a deployment group. See https://google.aip.dev/134.
   * (mirroringDeploymentGroups.patch)
   *
   * @param string $name Immutable. Identifier. The resource name of this
   * deployment group, for example:
   * `projects/123456789/locations/global/mirroringDeploymentGroups/my-dg`. See
   * https://google.aip.dev/122 for more details.
   * @param MirroringDeploymentGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Must be a UUID4. This request is only idempotent if a `request_id` is
   * provided. See https://google.aip.dev/155 for more details.
   * @opt_param string updateMask Optional. The list of fields to update. Fields
   * are specified relative to the deployment group (e.g. `description`; *not*
   * `mirroring_deployment_group.description`). See https://google.aip.dev/161 for
   * more details.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, MirroringDeploymentGroup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMirroringDeploymentGroups::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsMirroringDeploymentGroups');
