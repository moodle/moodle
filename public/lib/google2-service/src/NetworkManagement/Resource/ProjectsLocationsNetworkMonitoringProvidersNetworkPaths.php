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

namespace Google\Service\NetworkManagement\Resource;

use Google\Service\NetworkManagement\ListNetworkPathsResponse;
use Google\Service\NetworkManagement\NetworkPath;

/**
 * The "networkPaths" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkmanagementService = new Google\Service\NetworkManagement(...);
 *   $networkPaths = $networkmanagementService->projects_locations_networkMonitoringProviders_networkPaths;
 *  </code>
 */
class ProjectsLocationsNetworkMonitoringProvidersNetworkPaths extends \Google\Service\Resource
{
  /**
   * Gets the NetworkPath resource. (networkPaths.get)
   *
   * @param string $name Required. Name of the resource. Format: projects/{project
   * }/locations/{location}/networkMonitoringProviders/{network_monitoring_provide
   * r}/networkPaths/{network_path}
   * @param array $optParams Optional parameters.
   * @return NetworkPath
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], NetworkPath::class);
  }
  /**
   * Lists NetworkPaths for a given network monitoring provider.
   * (networkPaths.listProjectsLocationsNetworkMonitoringProvidersNetworkPaths)
   *
   * @param string $parent Required. Parent value for ListNetworkPathsRequest.
   * Format: projects/{project}/locations/{location}/networkMonitoringProviders/{n
   * etwork_monitoring_provider}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of network paths to
   * return. The service may return fewer than this value. If unspecified, at most
   * 20 network pathswill be returned. The maximum value is 1000; values above
   * 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListNetworkPaths` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListNetworkPaths` must match
   * the call that provided the page token.
   * @return ListNetworkPathsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNetworkMonitoringProvidersNetworkPaths($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListNetworkPathsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNetworkMonitoringProvidersNetworkPaths::class, 'Google_Service_NetworkManagement_Resource_ProjectsLocationsNetworkMonitoringProvidersNetworkPaths');
