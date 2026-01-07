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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\GoogleCloudApihubV1DiscoveredApiObservation;
use Google\Service\APIhub\GoogleCloudApihubV1ListDiscoveredApiObservationsResponse;

/**
 * The "discoveredApiObservations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $discoveredApiObservations = $apihubService->projects_locations_discoveredApiObservations;
 *  </code>
 */
class ProjectsLocationsDiscoveredApiObservations extends \Google\Service\Resource
{
  /**
   * Gets a DiscoveredAPIObservation in a given project, location and
   * ApiObservation. (discoveredApiObservations.get)
   *
   * @param string $name Required. The name of the DiscoveredApiObservation to
   * retrieve. Format: projects/{project}/locations/{location}/discoveredApiObserv
   * ations/{discovered_api_observation}
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1DiscoveredApiObservation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApihubV1DiscoveredApiObservation::class);
  }
  /**
   * Lists all the DiscoveredAPIObservations in a given project and location.
   * (discoveredApiObservations.listProjectsLocationsDiscoveredApiObservations)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * ApiObservations. Format: projects/{project}/locations/{location}
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
   * @return GoogleCloudApihubV1ListDiscoveredApiObservationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDiscoveredApiObservations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApihubV1ListDiscoveredApiObservationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDiscoveredApiObservations::class, 'Google_Service_APIhub_Resource_ProjectsLocationsDiscoveredApiObservations');
