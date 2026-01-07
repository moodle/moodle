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

namespace Google\Service\Connectors\Resource;

use Google\Service\Connectors\GetResourceResponse;
use Google\Service\Connectors\ListResourcesResponse;

/**
 * The "resources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $connectorsService = new Google\Service\Connectors(...);
 *   $resources = $connectorsService->projects_locations_connections_resources;
 *  </code>
 */
class ProjectsLocationsConnectionsResources extends \Google\Service\Resource
{
  /**
   * Gets a specific resource. (resources.get)
   *
   * @param string $name Required. Resource name of the Resource. Format: projects
   * /{project}/locations/{location}/connections/{connection}/resources/{resource}
   * @param array $optParams Optional parameters.
   * @return GetResourceResponse
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GetResourceResponse::class);
  }
  /**
   * Lists all available resources.
   * (resources.listProjectsLocationsConnectionsResources)
   *
   * @param string $parent Required. Resource name of the connection. Format:
   * projects/{project}/locations/{location}/connections/{connection}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Page size for the request.
   * @opt_param string pageToken Optional. Page token for the request.
   * @return ListResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnectionsResources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListResourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnectionsResources::class, 'Google_Service_Connectors_Resource_ProjectsLocationsConnectionsResources');
