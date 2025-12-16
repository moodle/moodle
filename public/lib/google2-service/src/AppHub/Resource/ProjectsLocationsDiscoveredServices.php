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

namespace Google\Service\AppHub\Resource;

use Google\Service\AppHub\DiscoveredService;
use Google\Service\AppHub\ListDiscoveredServicesResponse;
use Google\Service\AppHub\LookupDiscoveredServiceResponse;

/**
 * The "discoveredServices" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apphubService = new Google\Service\AppHub(...);
 *   $discoveredServices = $apphubService->projects_locations_discoveredServices;
 *  </code>
 */
class ProjectsLocationsDiscoveredServices extends \Google\Service\Resource
{
  /**
   * Gets a Discovered Service in a host project and location.
   * (discoveredServices.get)
   *
   * @param string $name Required. Fully qualified name of the Discovered Service
   * to fetch. Expected format: `projects/{project}/locations/{location}/discovere
   * dServices/{discoveredService}`.
   * @param array $optParams Optional parameters.
   * @return DiscoveredService
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DiscoveredService::class);
  }
  /**
   * Lists Discovered Services that can be added to an Application in a host
   * project and location.
   * (discoveredServices.listProjectsLocationsDiscoveredServices)
   *
   * @param string $parent Required. Project and location to list Discovered
   * Services on. Expected format: `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListDiscoveredServicesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDiscoveredServices($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDiscoveredServicesResponse::class);
  }
  /**
   * Lists a Discovered Service in a host project and location, with a given
   * resource URI. (discoveredServices.lookup)
   *
   * @param string $parent Required. Host project ID and location to lookup
   * Discovered Service in. Expected format:
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string uri Required. Resource URI to find DiscoveredService for.
   * Accepts both project number and project ID and does translation when needed.
   * @return LookupDiscoveredServiceResponse
   * @throws \Google\Service\Exception
   */
  public function lookup($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('lookup', [$params], LookupDiscoveredServiceResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDiscoveredServices::class, 'Google_Service_AppHub_Resource_ProjectsLocationsDiscoveredServices');
