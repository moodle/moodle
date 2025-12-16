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

use Google\Service\APIManagement\ApiOperation;
use Google\Service\APIManagement\ListApiOperationsResponse;

/**
 * The "apiOperations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apimService = new Google\Service\APIManagement(...);
 *   $apiOperations = $apimService->projects_locations_observationJobs_apiObservations_apiOperations;
 *  </code>
 */
class ProjectsLocationsObservationJobsApiObservationsApiOperations extends \Google\Service\Resource
{
  /**
   * GetApiOperation retrieves a single ApiOperation by name. (apiOperations.get)
   *
   * @param string $name Required. The name of the ApiOperation to retrieve.
   * Format: projects/{project}/locations/{location}/observationJobs/{observation_
   * job}/apiObservations/{api_observation}/apiOperation/{api_operation}
   * @param array $optParams Optional parameters.
   * @return ApiOperation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ApiOperation::class);
  }
  /**
   * ListApiOperations gets all ApiOperations for a given project and location and
   * ObservationJob and ApiObservation. (apiOperations.listProjectsLocationsObserv
   * ationJobsApiObservationsApiOperations)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * ApiOperations. Format: projects/{project}/locations/{location}/observationJob
   * s/{observation_job}/apiObservations/{api_observation}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of ApiOperations to
   * return. The service may return fewer than this value. If unspecified, at most
   * 10 ApiOperations will be returned. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListApiApiOperations` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListApiApiOperations` must
   * match the call that provided the page token.
   * @return ListApiOperationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsObservationJobsApiObservationsApiOperations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListApiOperationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsObservationJobsApiObservationsApiOperations::class, 'Google_Service_APIManagement_Resource_ProjectsLocationsObservationJobsApiObservationsApiOperations');
