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

use Google\Service\APIManagement\ApiObservation;
use Google\Service\APIManagement\BatchEditTagsApiObservationsRequest;
use Google\Service\APIManagement\BatchEditTagsApiObservationsResponse;
use Google\Service\APIManagement\ListApiObservationsResponse;

/**
 * The "apiObservations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apimService = new Google\Service\APIManagement(...);
 *   $apiObservations = $apimService->projects_locations_observationJobs_apiObservations;
 *  </code>
 */
class ProjectsLocationsObservationJobsApiObservations extends \Google\Service\Resource
{
  /**
   * BatchEditTagsApiObservations adds or removes Tags for ApiObservations.
   * (apiObservations.batchEditTags)
   *
   * @param string $parent Required. The parent resource shared by all
   * ApiObservations being edited. Format:
   * projects/{project}/locations/{location}/observationJobs/{observation_job}
   * @param BatchEditTagsApiObservationsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return BatchEditTagsApiObservationsResponse
   * @throws \Google\Service\Exception
   */
  public function batchEditTags($parent, BatchEditTagsApiObservationsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchEditTags', [$params], BatchEditTagsApiObservationsResponse::class);
  }
  /**
   * GetApiObservation retrieves a single ApiObservation by name.
   * (apiObservations.get)
   *
   * @param string $name Required. The name of the ApiObservation to retrieve.
   * Format: projects/{project}/locations/{location}/observationJobs/{observation_
   * job}/apiObservations/{api_observation}
   * @param array $optParams Optional parameters.
   * @return ApiObservation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ApiObservation::class);
  }
  /**
   * ListApiObservations gets all ApiObservations for a given project and location
   * and ObservationJob.
   * (apiObservations.listProjectsLocationsObservationJobsApiObservations)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * ApiObservations. Format:
   * projects/{project}/locations/{location}/observationJobs/{observation_job}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of ApiObservations to
   * return. The service may return fewer than this value. If unspecified, at most
   * 10 ApiObservations will be returned. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListApiObservations` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListApiObservations` must
   * match the call that provided the page token.
   * @return ListApiObservationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsObservationJobsApiObservations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListApiObservationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsObservationJobsApiObservations::class, 'Google_Service_APIManagement_Resource_ProjectsLocationsObservationJobsApiObservations');
