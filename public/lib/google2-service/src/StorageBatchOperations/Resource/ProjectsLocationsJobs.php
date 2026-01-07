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

namespace Google\Service\StorageBatchOperations\Resource;

use Google\Service\StorageBatchOperations\CancelJobRequest;
use Google\Service\StorageBatchOperations\CancelJobResponse;
use Google\Service\StorageBatchOperations\Job;
use Google\Service\StorageBatchOperations\ListJobsResponse;
use Google\Service\StorageBatchOperations\Operation;
use Google\Service\StorageBatchOperations\StoragebatchoperationsEmpty;

/**
 * The "jobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $storagebatchoperationsService = new Google\Service\StorageBatchOperations(...);
 *   $jobs = $storagebatchoperationsService->projects_locations_jobs;
 *  </code>
 */
class ProjectsLocationsJobs extends \Google\Service\Resource
{
  /**
   * Cancels a batch job. (jobs.cancel)
   *
   * @param string $name Required. The `name` of the job to cancel. Format:
   * projects/{project_id}/locations/global/jobs/{job_id}.
   * @param CancelJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CancelJobResponse
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], CancelJobResponse::class);
  }
  /**
   * Creates a batch job. (jobs.create)
   *
   * @param string $parent Required. Value for parent.
   * @param Job $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string jobId Required. The optional `job_id` for this Job . If not
   * specified, an id is generated. `job_id` should be no more than 128 characters
   * and must include only characters available in DNS names, as defined by
   * RFC-1123.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID in case you need to retry your request.
   * Requests with same `request_id` will be ignored for at least 60 minutes since
   * the first request. The request ID must be a valid UUID with the exception
   * that zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Job $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a batch job. (jobs.delete)
   *
   * @param string $name Required. The `name` of the job to delete. Format:
   * projects/{project_id}/locations/global/jobs/{job_id} .
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID in case you need to retry your request.
   * Requests with same `request_id` will be ignored for at least 60 minutes since
   * the first request. The request ID must be a valid UUID with the exception
   * that zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @return StoragebatchoperationsEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], StoragebatchoperationsEmpty::class);
  }
  /**
   * Gets a batch job. (jobs.get)
   *
   * @param string $name Required. `name` of the job to retrieve. Format:
   * projects/{project_id}/locations/global/jobs/{job_id} .
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
   * Lists Jobs in a given project. (jobs.listProjectsLocationsJobs)
   *
   * @param string $parent Required. Format:
   * projects/{project_id}/locations/global.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filters results as defined by
   * https://google.aip.dev/160.
   * @opt_param string orderBy Optional. Field to sort by. Supported fields are
   * name, create_time.
   * @opt_param int pageSize Optional. The list page size. default page size is
   * 100.
   * @opt_param string pageToken Optional. The list page token.
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
class_alias(ProjectsLocationsJobs::class, 'Google_Service_StorageBatchOperations_Resource_ProjectsLocationsJobs');
