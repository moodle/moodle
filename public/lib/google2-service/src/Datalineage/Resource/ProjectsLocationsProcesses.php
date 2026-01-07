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

namespace Google\Service\Datalineage\Resource;

use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1ListProcessesResponse;
use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1Process;
use Google\Service\Datalineage\GoogleLongrunningOperation;

/**
 * The "processes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datalineageService = new Google\Service\Datalineage(...);
 *   $processes = $datalineageService->projects_locations_processes;
 *  </code>
 */
class ProjectsLocationsProcesses extends \Google\Service\Resource
{
  /**
   * Creates a new process. (processes.create)
   *
   * @param string $parent Required. The name of the project and its location that
   * should own the process.
   * @param GoogleCloudDatacatalogLineageV1Process $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Restricted to 36 ASCII characters. A random UUID is recommended. This request
   * is idempotent only if a `request_id` is provided.
   * @return GoogleCloudDatacatalogLineageV1Process
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDatacatalogLineageV1Process $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDatacatalogLineageV1Process::class);
  }
  /**
   * Deletes the process with the specified name. (processes.delete)
   *
   * @param string $name Required. The name of the process to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true and the process is not found, the
   * request succeeds but the server doesn't perform any actions.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets the details of the specified process. (processes.get)
   *
   * @param string $name Required. The name of the process to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatacatalogLineageV1Process
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDatacatalogLineageV1Process::class);
  }
  /**
   * List processes in the given project and location. List order is descending by
   * insertion time. (processes.listProjectsLocationsProcesses)
   *
   * @param string $parent Required. The name of the project and its location that
   * owns this collection of processes.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of processes to return. The
   * service may return fewer than this value. If unspecified, at most 50
   * processes are returned. The maximum value is 100; values greater than 100 are
   * cut to 100.
   * @opt_param string pageToken The page token received from a previous
   * `ListProcesses` call. Specify it to get the next page. When paginating, all
   * other parameters specified in this call must match the parameters of the call
   * that provided the page token.
   * @return GoogleCloudDatacatalogLineageV1ListProcessesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsProcesses($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDatacatalogLineageV1ListProcessesResponse::class);
  }
  /**
   * Updates a process. (processes.patch)
   *
   * @param string $name Immutable. The resource name of the lineage process.
   * Format: `projects/{project}/locations/{location}/processes/{process}`. Can be
   * specified or auto-assigned. {process} must be not longer than 200 characters
   * and only contain characters in a set: `a-zA-Z0-9_-:.`
   * @param GoogleCloudDatacatalogLineageV1Process $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true and the process is not found, the
   * request inserts it.
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Restricted to 36 ASCII characters. A random UUID is recommended. This request
   * is idempotent only if a `request_id` is provided.
   * @opt_param string updateMask The list of fields to update. Currently not
   * used. The whole message is updated.
   * @return GoogleCloudDatacatalogLineageV1Process
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDatacatalogLineageV1Process $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDatacatalogLineageV1Process::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsProcesses::class, 'Google_Service_Datalineage_Resource_ProjectsLocationsProcesses');
