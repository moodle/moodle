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

use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3ExportPlaybookRequest;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3ImportPlaybookRequest;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3ListPlaybooksResponse;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3Playbook;
use Google\Service\Dialogflow\GoogleLongrunningOperation;
use Google\Service\Dialogflow\GoogleProtobufEmpty;

/**
 * The "playbooks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dialogflowService = new Google\Service\Dialogflow(...);
 *   $playbooks = $dialogflowService->projects_locations_agents_playbooks;
 *  </code>
 */
class ProjectsLocationsAgentsPlaybooks extends \Google\Service\Resource
{
  /**
   * Creates a playbook in a specified agent. (playbooks.create)
   *
   * @param string $parent Required. The agent to create a playbook for. Format:
   * `projects//locations//agents/`.
   * @param GoogleCloudDialogflowCxV3Playbook $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3Playbook
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDialogflowCxV3Playbook $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDialogflowCxV3Playbook::class);
  }
  /**
   * Deletes a specified playbook. (playbooks.delete)
   *
   * @param string $name Required. The name of the playbook to delete. Format:
   * `projects//locations//agents//playbooks/`.
   * @param array $optParams Optional parameters.
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
   * Exports the specified playbook to a binary file. Note that resources (e.g.
   * examples, tools) that the playbook references will also be exported.
   * (playbooks.export)
   *
   * @param string $name Required. The name of the playbook to export. Format:
   * `projects//locations//agents//playbooks/`.
   * @param GoogleCloudDialogflowCxV3ExportPlaybookRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function export($name, GoogleCloudDialogflowCxV3ExportPlaybookRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('export', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Retrieves the specified Playbook. (playbooks.get)
   *
   * @param string $name Required. The name of the playbook. Format:
   * `projects//locations//agents//playbooks/`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3Playbook
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDialogflowCxV3Playbook::class);
  }
  /**
   * Imports the specified playbook to the specified agent from a binary file.
   * (playbooks.import)
   *
   * @param string $parent Required. The agent to import the playbook into.
   * Format: `projects//locations//agents/`.
   * @param GoogleCloudDialogflowCxV3ImportPlaybookRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function import($parent, GoogleCloudDialogflowCxV3ImportPlaybookRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Returns a list of playbooks in the specified agent.
   * (playbooks.listProjectsLocationsAgentsPlaybooks)
   *
   * @param string $parent Required. The agent to list playbooks from. Format:
   * `projects//locations//agents/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of items to return in a single
   * page. By default 100 and at most 1000.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous list request.
   * @return GoogleCloudDialogflowCxV3ListPlaybooksResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAgentsPlaybooks($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDialogflowCxV3ListPlaybooksResponse::class);
  }
  /**
   * Updates the specified Playbook. (playbooks.patch)
   *
   * @param string $name The unique identifier of the playbook. Format:
   * `projects//locations//agents//playbooks/`.
   * @param GoogleCloudDialogflowCxV3Playbook $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The mask to control which fields get updated. If
   * the mask is not present, all fields will be updated.
   * @return GoogleCloudDialogflowCxV3Playbook
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDialogflowCxV3Playbook $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDialogflowCxV3Playbook::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAgentsPlaybooks::class, 'Google_Service_Dialogflow_Resource_ProjectsLocationsAgentsPlaybooks');
