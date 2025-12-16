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

namespace Google\Service\WorkloadManager\Resource;

use Google\Service\WorkloadManager\Execution;
use Google\Service\WorkloadManager\ListExecutionsResponse;
use Google\Service\WorkloadManager\Operation;
use Google\Service\WorkloadManager\RunEvaluationRequest;

/**
 * The "executions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workloadmanagerService = new Google\Service\WorkloadManager(...);
 *   $executions = $workloadmanagerService->projects_locations_evaluations_executions;
 *  </code>
 */
class ProjectsLocationsEvaluationsExecutions extends \Google\Service\Resource
{
  /**
   * Deletes a single Execution. (executions.delete)
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
   * Gets details of a single Execution. (executions.get)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   * @return Execution
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Execution::class);
  }
  /**
   * Lists Executions in a given project and location.
   * (executions.listProjectsLocationsEvaluationsExecutions)
   *
   * @param string $parent Required. The resource prefix of the Execution using
   * the form: 'projects/{project}/locations/{location}/evaluations/{evaluation}'
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results
   * @opt_param string orderBy Field to sort by. See
   * https://google.aip.dev/132#ordering for more details.
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListExecutionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEvaluationsExecutions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListExecutionsResponse::class);
  }
  /**
   * Creates a new Execution in a given project and location. (executions.run)
   *
   * @param string $name Required. The resource name of the Execution using the
   * form: 'projects/{project}/locations/{location}/evaluations/{evaluation}/execu
   * tions/{execution}'
   * @param RunEvaluationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function run($name, RunEvaluationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('run', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEvaluationsExecutions::class, 'Google_Service_WorkloadManager_Resource_ProjectsLocationsEvaluationsExecutions');
