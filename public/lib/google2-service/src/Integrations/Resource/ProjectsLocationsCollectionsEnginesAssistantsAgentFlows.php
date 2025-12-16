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

namespace Google\Service\Integrations\Resource;

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaAgentFlow;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaGenerateAgentFlowRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaGenerateAgentFlowResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaGenerateAndUpdateAgentFlowRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaGenerateAndUpdateAgentFlowResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaListAgentFlowsResponse;
use Google\Service\Integrations\GoogleProtobufEmpty;

/**
 * The "agentFlows" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $agentFlows = $integrationsService->projects_locations_collections_engines_assistants_agentFlows;
 *  </code>
 */
class ProjectsLocationsCollectionsEnginesAssistantsAgentFlows extends \Google\Service\Resource
{
  /**
   * Request to create a new AgentFlow with user-provided flow configuration.
   * (agentFlows.create)
   *
   * @param string $parent Required. Parent resource name where this AgentFlow
   * will be created.
   * @param GoogleCloudIntegrationsV1alphaAgentFlow $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaAgentFlow
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudIntegrationsV1alphaAgentFlow $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudIntegrationsV1alphaAgentFlow::class);
  }
  /**
   * Deletes an existing AgentFlow. (agentFlows.delete)
   *
   * @param string $name Required. The resource name of the AgentFlow to delete.
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
   * Uses Natural Language (NL) to generate an AgentFlow configuration and create
   * a new AgentFlow. (agentFlows.generate)
   *
   * @param string $parent Required. Parent resource name where this AgentFlow
   * will be created.
   * @param GoogleCloudIntegrationsV1alphaGenerateAgentFlowRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaGenerateAgentFlowResponse
   * @throws \Google\Service\Exception
   */
  public function generate($parent, GoogleCloudIntegrationsV1alphaGenerateAgentFlowRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generate', [$params], GoogleCloudIntegrationsV1alphaGenerateAgentFlowResponse::class);
  }
  /**
   * Uses Natural Language (NL) to generate an AgentFlow configuration and update
   * an existing AgentFlow. (agentFlows.generateAndUpdate)
   *
   * @param string $name Required. The resource name of the AgentFlow to update.
   * @param GoogleCloudIntegrationsV1alphaGenerateAndUpdateAgentFlowRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaGenerateAndUpdateAgentFlowResponse
   * @throws \Google\Service\Exception
   */
  public function generateAndUpdate($name, GoogleCloudIntegrationsV1alphaGenerateAndUpdateAgentFlowRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('generateAndUpdate', [$params], GoogleCloudIntegrationsV1alphaGenerateAndUpdateAgentFlowResponse::class);
  }
  /**
   * Gets an existing AgentFlow. (agentFlows.get)
   *
   * @param string $name Required. The resource name of the AgentFlow to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaAgentFlow
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudIntegrationsV1alphaAgentFlow::class);
  }
  /**
   * Lists all AgentFlows.
   * (agentFlows.listProjectsLocationsCollectionsEnginesAssistantsAgentFlows)
   *
   * @param string $parent Required. The parent resource where this AgentFlow was
   * created.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Standard filter field. Filtering as
   * supported in https://developers.google.com/authorized-
   * buyers/apis/guides/list-filters.
   * @opt_param string orderBy Optional. The results would be returned in order
   * specified here. Currently supported sort keys are: Descending sort order for
   * "create_time", "update_time". Ascending sort order for "agent_flow_id",
   * "display_name".
   * @opt_param int pageSize Optional. The maximum number of AgentFlows to return.
   * The service may return fewer than this value. If unspecified, at most 100
   * AgentFlows will be returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListAgentFlows` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListAgentFlows` must match the
   * call that provided the page token.
   * @opt_param string readMask Optional. The mask which specifies fields that
   * need to be returned in the AgentFlow's response.
   * @return GoogleCloudIntegrationsV1alphaListAgentFlowsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsEnginesAssistantsAgentFlows($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudIntegrationsV1alphaListAgentFlowsResponse::class);
  }
  /**
   * Updates an existing AgentFlow. (agentFlows.patch)
   *
   * @param string $name Required. Resource name of the agent flow.
   * @param GoogleCloudIntegrationsV1alphaAgentFlow $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask specifying the fields in
   * the above AgentFlow that have been modified and need to be updated.
   * @return GoogleCloudIntegrationsV1alphaAgentFlow
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudIntegrationsV1alphaAgentFlow $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudIntegrationsV1alphaAgentFlow::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsEnginesAssistantsAgentFlows::class, 'Google_Service_Integrations_Resource_ProjectsLocationsCollectionsEnginesAssistantsAgentFlows');
