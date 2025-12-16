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

use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3Example;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3ListExamplesResponse;
use Google\Service\Dialogflow\GoogleProtobufEmpty;

/**
 * The "examples" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dialogflowService = new Google\Service\Dialogflow(...);
 *   $examples = $dialogflowService->projects_locations_agents_playbooks_examples;
 *  </code>
 */
class ProjectsLocationsAgentsPlaybooksExamples extends \Google\Service\Resource
{
  /**
   * Creates an example in the specified playbook. (examples.create)
   *
   * @param string $parent Required. The playbook to create an example for.
   * Format: `projects//locations//agents//playbooks/`.
   * @param GoogleCloudDialogflowCxV3Example $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3Example
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDialogflowCxV3Example $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDialogflowCxV3Example::class);
  }
  /**
   * Deletes the specified example. (examples.delete)
   *
   * @param string $name Required. The name of the example to delete. Format:
   * `projects//locations//agents//playbooks//examples/`.
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
   * Retrieves the specified example. (examples.get)
   *
   * @param string $name Required. The name of the example. Format:
   * `projects//locations//agents//playbooks//examples/`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDialogflowCxV3Example
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDialogflowCxV3Example::class);
  }
  /**
   * Returns a list of examples in the specified playbook.
   * (examples.listProjectsLocationsAgentsPlaybooksExamples)
   *
   * @param string $parent Required. The playbook to list the examples from.
   * Format: `projects//locations//agents//playbooks/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode Optional. The language to list examples for.
   * If not specified, list all examples under the playbook. Note: languages must
   * be enabled in the agent before they can be used.
   * @opt_param int pageSize Optional. The maximum number of items to return in a
   * single page. By default 100 and at most 1000.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous list request.
   * @return GoogleCloudDialogflowCxV3ListExamplesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAgentsPlaybooksExamples($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDialogflowCxV3ListExamplesResponse::class);
  }
  /**
   * Update the specified example. (examples.patch)
   *
   * @param string $name The unique identifier of the playbook example. Format:
   * `projects//locations//agents//playbooks//examples/`.
   * @param GoogleCloudDialogflowCxV3Example $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The mask to control which fields get
   * updated. If the mask is not present, all fields will be updated.
   * @return GoogleCloudDialogflowCxV3Example
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDialogflowCxV3Example $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDialogflowCxV3Example::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAgentsPlaybooksExamples::class, 'Google_Service_Dialogflow_Resource_ProjectsLocationsAgentsPlaybooksExamples');
