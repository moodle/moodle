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

namespace Google\Service\Batch\Resource;

use Google\Service\Batch\CancelJobRequest;
use Google\Service\Batch\Job;
use Google\Service\Batch\ListJobsResponse;
use Google\Service\Batch\Operation;

/**
 * The "jobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $batchService = new Google\Service\Batch(...);
 *   $jobs = $batchService->projects_locations_jobs;
 *  </code>
 */
class ProjectsLocationsJobs extends \Google\Service\Resource
{
  /**
   * Cancel a Job. (jobs.cancel)
   *
   * @param string $name Required. Job name.
   * @param CancelJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], Operation::class);
  }
  /**
   * Create a Job. (jobs.create)
   *
   * @param string $parent Required. The parent resource name where the Job will
   * be created. Pattern: "projects/{project}/locations/{location}"
   * @param Job $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string jobId ID used to uniquely identify the Job within its
   * parent scope. This field should contain at most 63 characters and must start
   * with lowercase characters. Only lowercase characters, numbers and '-' are
   * accepted. The '-' character cannot be the first or the last one. A system
   * generated ID will be used if the field is not set. The job.name field in the
   * request will be ignored and the created resource name of the Job will be
   * "{parent}/jobs/{job_id}".
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
   * @return Job
   * @throws \Google\Service\Exception
   */
  public function create($parent, Job $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Job::class);
  }
  /**
   * Delete a Job. (jobs.delete)
   *
   * @param string $name Job name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string reason Optional. Reason for this deletion.
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
   * Get a Job specified by its resource name. (jobs.get)
   *
   * @param string $name Required. Job name.
   * @param array $optParams Optional parameters.
   * @return Job
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Job::class);
  }
  /**
   * List all Jobs for a project within a region. (jobs.listProjectsLocationsJobs)
   *
   * @param string $parent Parent path.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter List filter.
   * @opt_param string orderBy Optional. Sort results. Supported are "name", "name
   * desc", "create_time", and "create_time desc".
   * @opt_param int pageSize Page size.
   * @opt_param string pageToken Page token.
   * @return ListJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsJobs::class, 'Google_Service_Batch_Resource_ProjectsLocationsJobs');
