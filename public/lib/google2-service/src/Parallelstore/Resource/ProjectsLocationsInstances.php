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

namespace Google\Service\Parallelstore\Resource;

use Google\Service\Parallelstore\ExportDataRequest;
use Google\Service\Parallelstore\ImportDataRequest;
use Google\Service\Parallelstore\Instance;
use Google\Service\Parallelstore\ListInstancesResponse;
use Google\Service\Parallelstore\Operation;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $parallelstoreService = new Google\Service\Parallelstore(...);
 *   $instances = $parallelstoreService->projects_locations_instances;
 *  </code>
 */
class ProjectsLocationsInstances extends \Google\Service\Resource
{
  /**
   * Creates a Parallelstore instance in a given project and location.
   * (instances.create)
   *
   * @param string $parent Required. The instance's project and location, in the
   * format `projects/{project}/locations/{location}`. Locations map to Google
   * Cloud zones; for example, `us-west1-b`.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string instanceId Required. The name of the Parallelstore
   * instance. * Must contain only lowercase letters, numbers, and hyphens. * Must
   * start with a letter. * Must be between 1-63 characters. * Must end with a
   * number or a letter. * Must be unique within the customer project / location
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
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Instance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single instance. (instances.delete)
   *
   * @param string $name Required. Name of the resource
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
   * Copies data from Parallelstore to Cloud Storage. (instances.exportData)
   *
   * @param string $name Required. Name of the resource.
   * @param ExportDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function exportData($name, ExportDataRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('exportData', [$params], Operation::class);
  }
  /**
   * Gets details of a single instance. (instances.get)
   *
   * @param string $name Required. The instance resource name, in the format
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`.
   * @param array $optParams Optional parameters.
   * @return Instance
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Instance::class);
  }
  /**
   * Copies data from Cloud Storage to Parallelstore. (instances.importData)
   *
   * @param string $name Required. Name of the resource.
   * @param ImportDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function importData($name, ImportDataRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('importData', [$params], Operation::class);
  }
  /**
   * Lists all instances in a given project and location.
   * (instances.listProjectsLocationsInstances)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve instance information, in the format
   * `projects/{project_id}/locations/{location}`. To retrieve instance
   * information for all locations, use "-" as the value of `{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, the server will pick an
   * appropriate default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListInstancesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInstances($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstancesResponse::class);
  }
  /**
   * Updates the parameters of a single instance. (instances.patch)
   *
   * @param string $name Identifier. The resource name of the instance, in the
   * format `projects/{project}/locations/{location}/instances/{instance_id}`.
   * @param Instance $postBody
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
   * @opt_param string updateMask Required. Mask of fields to update. Field mask
   * is used to specify the fields to be overwritten in the Instance resource by
   * the update. At least one path must be supplied in this field. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Instance $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInstances::class, 'Google_Service_Parallelstore_Resource_ProjectsLocationsInstances');
