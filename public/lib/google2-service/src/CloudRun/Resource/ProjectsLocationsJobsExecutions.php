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

namespace Google\Service\CloudRun\Resource;

use Google\Service\CloudRun\GoogleCloudRunV2CancelExecutionRequest;
use Google\Service\CloudRun\GoogleCloudRunV2Execution;
use Google\Service\CloudRun\GoogleCloudRunV2ExportStatusResponse;
use Google\Service\CloudRun\GoogleCloudRunV2ListExecutionsResponse;
use Google\Service\CloudRun\GoogleLongrunningOperation;

/**
 * The "executions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $runService = new Google\Service\CloudRun(...);
 *   $executions = $runService->projects_locations_jobs_executions;
 *  </code>
 */
class ProjectsLocationsJobsExecutions extends \Google\Service\Resource
{
  /**
   * Cancels an Execution. (executions.cancel)
   *
   * @param string $name Required. The name of the Execution to cancel. Format:
   * `projects/{project}/locations/{location}/jobs/{job}/executions/{execution}`,
   * where `{project}` can be project id or number.
   * @param GoogleCloudRunV2CancelExecutionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function cancel($name, GoogleCloudRunV2CancelExecutionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes an Execution. (executions.delete)
   *
   * @param string $name Required. The name of the Execution to delete. Format:
   * `projects/{project}/locations/{location}/jobs/{job}/executions/{execution}`,
   * where `{project}` can be project id or number.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag A system-generated fingerprint for this version of the
   * resource. This may be used to detect modification conflict during updates.
   * @opt_param bool validateOnly Indicates that the request should be validated
   * without actually deleting any resources.
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
   * Read the status of an image export operation. (executions.exportStatus)
   *
   * @param string $name Required. The name of the resource of which image export
   * operation status has to be fetched. Format: `projects/{project_id_or_number}/
   * locations/{location}/services/{service}/revisions/{revision}` for Revision `p
   * rojects/{project_id_or_number}/locations/{location}/jobs/{job}/executions/{ex
   * ecution}` for Execution
   * @param string $operationId Required. The operation id returned from
   * ExportImage.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2ExportStatusResponse
   * @throws \Google\Service\Exception
   */
  public function exportStatus($name, $operationId, $optParams = [])
  {
    $params = ['name' => $name, 'operationId' => $operationId];
    $params = array_merge($params, $optParams);
    return $this->call('exportStatus', [$params], GoogleCloudRunV2ExportStatusResponse::class);
  }
  /**
   * Gets information about an Execution. (executions.get)
   *
   * @param string $name Required. The full name of the Execution. Format:
   * `projects/{project}/locations/{location}/jobs/{job}/executions/{execution}`,
   * where `{project}` can be project id or number.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRunV2Execution
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudRunV2Execution::class);
  }
  /**
   * Lists Executions from a Job. Results are sorted by creation time, descending.
   * (executions.listProjectsLocationsJobsExecutions)
   *
   * @param string $parent Required. The Execution from which the Executions
   * should be listed. To list all Executions across Jobs, use "-" instead of Job
   * name. Format: `projects/{project}/locations/{location}/jobs/{job}`, where
   * `{project}` can be project id or number.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of Executions to return in this call.
   * @opt_param string pageToken A page token received from a previous call to
   * ListExecutions. All other parameters must match.
   * @opt_param bool showDeleted If true, returns deleted (but unexpired)
   * resources along with active ones.
   * @return GoogleCloudRunV2ListExecutionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsJobsExecutions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudRunV2ListExecutionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsJobsExecutions::class, 'Google_Service_CloudRun_Resource_ProjectsLocationsJobsExecutions');
