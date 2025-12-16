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

use Google\Service\Connectors\ExecuteToolRequest;
use Google\Service\Connectors\ExecuteToolResponse;
use Google\Service\Connectors\ListToolsResponse;

/**
 * The "tools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $connectorsService = new Google\Service\Connectors(...);
 *   $tools = $connectorsService->projects_locations_connections_tools;
 *  </code>
 */
class ProjectsLocationsConnectionsTools extends \Google\Service\Resource
{
  /**
   * Executes a specific tool. (tools.execute)
   *
   * @param string $name Required. Resource name of the Tool. Format:
   * projects/{project}/locations/{location}/connections/{connection}/tools/{tool}
   * @param ExecuteToolRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ExecuteToolResponse
   * @throws \Google\Service\Exception
   */
  public function execute($name, ExecuteToolRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('execute', [$params], ExecuteToolResponse::class);
  }
  /**
   * Lists all available tools. (tools.listProjectsLocationsConnectionsTools)
   *
   * @param string $parent Required. Resource name of the Connection. Format:
   * projects/{project}/locations/{location}/connections/{connection}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Page size.
   * @opt_param string pageToken Page token.
   * @return ListToolsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnectionsTools($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListToolsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnectionsTools::class, 'Google_Service_Connectors_Resource_ProjectsLocationsConnectionsTools');
