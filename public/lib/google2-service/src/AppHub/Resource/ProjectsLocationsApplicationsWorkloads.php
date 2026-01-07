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

namespace Google\Service\AppHub\Resource;

use Google\Service\AppHub\ListWorkloadsResponse;
use Google\Service\AppHub\Operation;
use Google\Service\AppHub\Workload;

/**
 * The "workloads" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apphubService = new Google\Service\AppHub(...);
 *   $workloads = $apphubService->projects_locations_applications_workloads;
 *  </code>
 */
class ProjectsLocationsApplicationsWorkloads extends \Google\Service\Resource
{
  /**
   * Creates a Workload in an Application. (workloads.create)
   *
   * @param string $parent Required. Fully qualified name of the Application to
   * create Workload in. Expected format:
   * `projects/{project}/locations/{location}/applications/{application}`.
   * @param Workload $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string workloadId Required. The Workload identifier. Must contain
   * only lowercase letters, numbers or hyphens, with the first character a
   * letter, the last a letter or a number, and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Workload $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a Workload from an Application. (workloads.delete)
   *
   * @param string $name Required. Fully qualified name of the Workload to delete
   * from an Application. Expected format: `projects/{project}/locations/{location
   * }/applications/{application}/workloads/{workload}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Gets a Workload in an Application. (workloads.get)
   *
   * @param string $name Required. Fully qualified name of the Workload to fetch.
   * Expected format: `projects/{project}/locations/{location}/applications/{appli
   * cation}/workloads/{workload}`.
   * @param array $optParams Optional parameters.
   * @return Workload
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Workload::class);
  }
  /**
   * Lists Workloads in an Application.
   * (workloads.listProjectsLocationsApplicationsWorkloads)
   *
   * @param string $parent Required. Fully qualified name of the parent
   * Application to list Workloads for. Expected format:
   * `projects/{project}/locations/{location}/applications/{application}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListWorkloadsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsApplicationsWorkloads($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWorkloadsResponse::class);
  }
  /**
   * Updates a Workload in an Application. (workloads.patch)
   *
   * @param string $name Identifier. The resource name of the Workload. Format:
   * `"projects/{host-project-id}/locations/{location}/applications/{application-
   * id}/workloads/{workload-id}"`
   * @param Workload $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Workload resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. The API changes the values of the fields as specified in the
   * update_mask. The API ignores the values of all fields not covered by the
   * update_mask. You can also unset a field by not specifying it in the updated
   * message, but adding the field to the mask. This clears whatever value the
   * field previously had.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Workload $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsApplicationsWorkloads::class, 'Google_Service_AppHub_Resource_ProjectsLocationsApplicationsWorkloads');
