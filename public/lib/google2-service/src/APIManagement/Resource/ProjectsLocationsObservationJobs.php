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

namespace Google\Service\APIManagement\Resource;

use Google\Service\APIManagement\DisableObservationJobRequest;
use Google\Service\APIManagement\EnableObservationJobRequest;
use Google\Service\APIManagement\ListObservationJobsResponse;
use Google\Service\APIManagement\ObservationJob;
use Google\Service\APIManagement\Operation;

/**
 * The "observationJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apimService = new Google\Service\APIManagement(...);
 *   $observationJobs = $apimService->projects_locations_observationJobs;
 *  </code>
 */
class ProjectsLocationsObservationJobs extends \Google\Service\Resource
{
  /**
   * CreateObservationJob creates a new ObservationJob but does not have any
   * effecton its own. It is a configuration that can be used in an Observation
   * Job to collect data about existing APIs. (observationJobs.create)
   *
   * @param string $parent Required. The parent resource where this ObservationJob
   * will be created. Format: projects/{project}/locations/{location}
   * @param ObservationJob $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string observationJobId Required. The ID to use for the
   * Observation Job. This value should be 4-63 characters, and valid characters
   * are /a-z-/.
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
  public function create($parent, ObservationJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * DeleteObservationJob deletes an ObservationJob. This method will fail if the
   * observation job is currently being used by any ObservationSource, even if not
   * enabled. (observationJobs.delete)
   *
   * @param string $name Required. Name of the resource Format:
   * projects/{project}/locations/{location}/observationJobs/{observation_job}
   * @param array $optParams Optional parameters.
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
   * Disables the given ObservationJob. (observationJobs.disable)
   *
   * @param string $name Required. The name of the ObservationJob to disable.
   * Format: projects/{project}/locations/{location}/observationJobs/{job}
   * @param DisableObservationJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function disable($name, DisableObservationJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], Operation::class);
  }
  /**
   * Enables the given ObservationJob. (observationJobs.enable)
   *
   * @param string $name Required. The name of the ObservationJob to enable.
   * Format: projects/{project}/locations/{location}/observationJobs/{job}
   * @param EnableObservationJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function enable($name, EnableObservationJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enable', [$params], Operation::class);
  }
  /**
   * GetObservationJob retrieves a single ObservationJob by name.
   * (observationJobs.get)
   *
   * @param string $name Required. The name of the ObservationJob to retrieve.
   * Format: projects/{project}/locations/{location}/observationJobs/{job}
   * @param array $optParams Optional parameters.
   * @return ObservationJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ObservationJob::class);
  }
  /**
   * ListObservationJobs gets all ObservationJobs for a given project and
   * location. (observationJobs.listProjectsLocationsObservationJobs)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * ObservationJobs. Format: projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of ObservationJobs to
   * return. The service may return fewer than this value. If unspecified, at most
   * 10 ObservationJobs will be returned. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListObservationJobs` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListObservationJobs` must
   * match the call that provided the page token.
   * @return ListObservationJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsObservationJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListObservationJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsObservationJobs::class, 'Google_Service_APIManagement_Resource_ProjectsLocationsObservationJobs');
