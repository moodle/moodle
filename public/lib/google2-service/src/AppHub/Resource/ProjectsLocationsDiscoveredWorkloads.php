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

use Google\Service\AppHub\DiscoveredWorkload;
use Google\Service\AppHub\ListDiscoveredWorkloadsResponse;
use Google\Service\AppHub\LookupDiscoveredWorkloadResponse;

/**
 * The "discoveredWorkloads" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apphubService = new Google\Service\AppHub(...);
 *   $discoveredWorkloads = $apphubService->projects_locations_discoveredWorkloads;
 *  </code>
 */
class ProjectsLocationsDiscoveredWorkloads extends \Google\Service\Resource
{
  /**
   * Gets a Discovered Workload in a host project and location.
   * (discoveredWorkloads.get)
   *
   * @param string $name Required. Fully qualified name of the Discovered Workload
   * to fetch. Expected format: `projects/{project}/locations/{location}/discovere
   * dWorkloads/{discoveredWorkload}`.
   * @param array $optParams Optional parameters.
   * @return DiscoveredWorkload
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DiscoveredWorkload::class);
  }
  /**
   * Lists Discovered Workloads that can be added to an Application in a host
   * project and location.
   * (discoveredWorkloads.listProjectsLocationsDiscoveredWorkloads)
   *
   * @param string $parent Required. Project and location to list Discovered
   * Workloads on. Expected format: `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListDiscoveredWorkloadsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDiscoveredWorkloads($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDiscoveredWorkloadsResponse::class);
  }
  /**
   * Lists a Discovered Workload in a host project and location, with a given
   * resource URI. (discoveredWorkloads.lookup)
   *
   * @param string $parent Required. Host project ID and location to lookup
   * Discovered Workload in. Expected format:
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string uri Required. Resource URI to find Discovered Workload for.
   * Accepts both project number and project ID and does translation when needed.
   * @return LookupDiscoveredWorkloadResponse
   * @throws \Google\Service\Exception
   */
  public function lookup($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('lookup', [$params], LookupDiscoveredWorkloadResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDiscoveredWorkloads::class, 'Google_Service_AppHub_Resource_ProjectsLocationsDiscoveredWorkloads');
