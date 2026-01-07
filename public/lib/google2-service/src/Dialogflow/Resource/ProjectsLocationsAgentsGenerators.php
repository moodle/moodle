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

use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3Generator;
use Google\Service\Dialogflow\GoogleCloudDialogflowCxV3ListGeneratorsResponse;
use Google\Service\Dialogflow\GoogleProtobufEmpty;

/**
 * The "generators" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dialogflowService = new Google\Service\Dialogflow(...);
 *   $generators = $dialogflowService->projects_locations_agents_generators;
 *  </code>
 */
class ProjectsLocationsAgentsGenerators extends \Google\Service\Resource
{
  /**
   * Creates a generator in the specified agent. (generators.create)
   *
   * @param string $parent Required. The agent to create a generator for. Format:
   * `projects//locations//agents/`.
   * @param GoogleCloudDialogflowCxV3Generator $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode The language to create generators for the
   * following fields: * `Generator.prompt_text.text` If not specified, the
   * agent's default language is used.
   * @return GoogleCloudDialogflowCxV3Generator
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDialogflowCxV3Generator $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDialogflowCxV3Generator::class);
  }
  /**
   * Deletes the specified generators. (generators.delete)
   *
   * @param string $name Required. The name of the generator to delete. Format:
   * `projects//locations//agents//generators/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force This field has no effect for generators not being used.
   * For generators that are used by pages/flows/transition route groups: * If
   * `force` is set to false, an error will be returned with message indicating
   * the referenced resources. * If `force` is set to true, Dialogflow will remove
   * the generator, as well as any references to the generator (i.e. Generator) in
   * fulfillments.
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
   * Retrieves the specified generator. (generators.get)
   *
   * @param string $name Required. The name of the generator. Format:
   * `projects//locations//agents//generators/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode The language to list generators for.
   * @return GoogleCloudDialogflowCxV3Generator
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDialogflowCxV3Generator::class);
  }
  /**
   * Returns the list of all generators in the specified agent.
   * (generators.listProjectsLocationsAgentsGenerators)
   *
   * @param string $parent Required. The agent to list all generators for. Format:
   * `projects//locations//agents/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode The language to list generators for.
   * @opt_param int pageSize The maximum number of items to return in a single
   * page. By default 100 and at most 1000.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous list request.
   * @return GoogleCloudDialogflowCxV3ListGeneratorsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAgentsGenerators($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDialogflowCxV3ListGeneratorsResponse::class);
  }
  /**
   * Update the specified generator. (generators.patch)
   *
   * @param string $name The unique identifier of the generator. Must be set for
   * the Generators.UpdateGenerator method. Generators.CreateGenerate populates
   * the name automatically. Format: `projects//locations//agents//generators/`.
   * @param GoogleCloudDialogflowCxV3Generator $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string languageCode The language to list generators for.
   * @opt_param string updateMask The mask to control which fields get updated. If
   * the mask is not present, all fields will be updated.
   * @return GoogleCloudDialogflowCxV3Generator
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDialogflowCxV3Generator $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDialogflowCxV3Generator::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAgentsGenerators::class, 'Google_Service_Dialogflow_Resource_ProjectsLocationsAgentsGenerators');
