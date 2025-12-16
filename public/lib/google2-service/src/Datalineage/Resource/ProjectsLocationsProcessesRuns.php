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

use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1ListRunsResponse;
use Google\Service\Datalineage\GoogleCloudDatacatalogLineageV1Run;
use Google\Service\Datalineage\GoogleLongrunningOperation;

/**
 * The "runs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datalineageService = new Google\Service\Datalineage(...);
 *   $runs = $datalineageService->projects_locations_processes_runs;
 *  </code>
 */
class ProjectsLocationsProcessesRuns extends \Google\Service\Resource
{
  /**
   * Creates a new run. (runs.create)
   *
   * @param string $parent Required. The name of the process that should own the
   * run.
   * @param GoogleCloudDatacatalogLineageV1Run $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Restricted to 36 ASCII characters. A random UUID is recommended. This request
   * is idempotent only if a `request_id` is provided.
   * @return GoogleCloudDatacatalogLineageV1Run
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDatacatalogLineageV1Run $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDatacatalogLineageV1Run::class);
  }
  /**
   * Deletes the run with the specified name. (runs.delete)
   *
   * @param string $name Required. The name of the run to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true and the run is not found, the
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
   * Gets the details of the specified run. (runs.get)
   *
   * @param string $name Required. The name of the run to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDatacatalogLineageV1Run
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDatacatalogLineageV1Run::class);
  }
  /**
   * Lists runs in the given project and location. List order is descending by
   * `start_time`. (runs.listProjectsLocationsProcessesRuns)
   *
   * @param string $parent Required. The name of process that owns this collection
   * of runs.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of runs to return. The service may
   * return fewer than this value. If unspecified, at most 50 runs are returned.
   * The maximum value is 100; values greater than 100 are cut to 100.
   * @opt_param string pageToken The page token received from a previous
   * `ListRuns` call. Specify it to get the next page. When paginating, all other
   * parameters specified in this call must match the parameters of the call that
   * provided the page token.
   * @return GoogleCloudDatacatalogLineageV1ListRunsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsProcessesRuns($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDatacatalogLineageV1ListRunsResponse::class);
  }
  /**
   * Updates a run. (runs.patch)
   *
   * @param string $name Immutable. The resource name of the run. Format:
   * `projects/{project}/locations/{location}/processes/{process}/runs/{run}`. Can
   * be specified or auto-assigned. {run} must be not longer than 200 characters
   * and only contain characters in a set: `a-zA-Z0-9_-:.`
   * @param GoogleCloudDatacatalogLineageV1Run $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing If set to true and the run is not found, the
   * request creates it.
   * @opt_param string updateMask The list of fields to update. Currently not
   * used. The whole message is updated.
   * @return GoogleCloudDatacatalogLineageV1Run
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDatacatalogLineageV1Run $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDatacatalogLineageV1Run::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsProcessesRuns::class, 'Google_Service_Datalineage_Resource_ProjectsLocationsProcessesRuns');
