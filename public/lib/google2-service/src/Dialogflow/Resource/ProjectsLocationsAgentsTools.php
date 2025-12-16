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

namespace Google\Service\Dialogflow\Resource;

use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3ListToolsResponse;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3Tool;
use Google\Service\Dialogflow\GoogleProtobufEmpty;

/**
 * The "tools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dialogflowService = new Google\Service\Dialogflow(...);
 *   $tools = $dialogflowService->projects_locations_agents_tools;
 *  </code>
 */
class ProjectsLocationsAgentsTools extends \Google\Service\Resource
{
  /**
   * Creates a Tool in the specified agent. (tools.create)
   *
   * @param string $parent Required. The agent to create a Tool for. Format:
   * `projects//locations//agents/`.
   * @param GoogleCloudDialogflowCxV3Tool $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3Tool
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDialogflowCxV3Tool $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDialogflowCxV3Tool::class);
  }
  /**
   * Deletes a specified Tool. (tools.delete)
   *
   * @param string $name Required. The name of the Tool to be deleted. Format:
   * `projects//locations//agents//tools/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force This field has no effect for Tools not being used. For
   * Tools that are used: * If `force` is set to false, an error will be returned
   * with message indicating the referenced resources. * If `force` is set to
   * true, Dialogflow will remove the tool, as well as any references to the tool.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Retrieves the specified Tool. (tools.get)
   *
   * @param string $name Required. The name of the Tool. Format:
   * `projects//locations//agents//tools/`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3Tool
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDialogflowCxV3Tool::class);
  }
  /**
   * Returns a list of Tools in the specified agent.
   * (tools.listProjectsLocationsAgentsTools)
   *
   * @param string $parent Required. The agent to list the Tools from. Format:
   * `projects//locations//agents/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of items to return in a single
   * page. By default 100 and at most 1000.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous list request.
   * @return GoogleCloudDialogflowCxV3ListToolsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAgentsTools($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDialogflowCxV3ListToolsResponse::class);
  }
  /**
   * Update the specified Tool. (tools.patch)
   *
   * @param string $name The unique identifier of the Tool. Format:
   * `projects//locations//agents//tools/`.
   * @param GoogleCloudDialogflowCxV3Tool $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The mask to control which fields get updated. If
   * the mask is not present, all fields will be updated.
   * @return GoogleCloudDialogflowCxV3Tool
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDialogflowCxV3Tool $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDialogflowCxV3Tool::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAgentsTools::class, 'Google_Service_Dialogflow_Resource_ProjectsLocationsAgentsTools');
