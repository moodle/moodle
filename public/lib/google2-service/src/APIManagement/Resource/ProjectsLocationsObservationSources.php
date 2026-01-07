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

use Google\Service\APIManagement\ListObservationSourcesResponse;
use Google\Service\APIManagement\ObservationSource;
use Google\Service\APIManagement\Operation;

/**
 * The "observationSources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apimService = new Google\Service\APIManagement(...);
 *   $observationSources = $apimService->projects_locations_observationSources;
 *  </code>
 */
class ProjectsLocationsObservationSources extends \Google\Service\Resource
{
  /**
   * CreateObservationSource creates a new ObservationSource but does not affect
   * any deployed infrastructure. It is a configuration that can be used in an
   * Observation Job to collect data about APIs running in user's dataplane.
   * (observationSources.create)
   *
   * @param string $parent Required. Value for parent.
   * @param ObservationSource $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string observationSourceId Required. The ID to use for the
   * Observation Source. This value should be 4-63 characters, and valid
   * characters are /a-z-/.
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
  public function create($parent, ObservationSource $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * DeleteObservationSource deletes an observation source. This method will fail
   * if the observation source is currently being used by any ObservationJob, even
   * if not enabled. (observationSources.delete)
   *
   * @param string $name Required. Name of the resource Format:
   * projects/{project}/locations/{location}/observationSources/{source}
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
   * GetObservationSource retrieves a single ObservationSource by name.
   * (observationSources.get)
   *
   * @param string $name Required. The name of the ObservationSource to retrieve.
   * Format: projects/{project}/locations/{location}/observationSources/{source}
   * @param array $optParams Optional parameters.
   * @return ObservationSource
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ObservationSource::class);
  }
  /**
   * ListObservationSources gets all ObservationSources for a given project and
   * location. (observationSources.listProjectsLocationsObservationSources)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * ObservationSources. Format: projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of ObservationSources to
   * return. The service may return fewer than this value. If unspecified, at most
   * 10 ObservationSources will be returned. The maximum value is 1000; values
   * above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListObservationSources` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListObservationSources`
   * must match the call that provided the page token.
   * @return ListObservationSourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsObservationSources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListObservationSourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsObservationSources::class, 'Google_Service_APIManagement_Resource_ProjectsLocationsObservationSources');
