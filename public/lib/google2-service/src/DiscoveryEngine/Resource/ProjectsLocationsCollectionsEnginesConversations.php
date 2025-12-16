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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1Conversation;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ConverseConversationRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ConverseConversationResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListConversationsResponse;
use Google\Service\DiscoveryEngine\GoogleProtobufEmpty;

/**
 * The "conversations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $conversations = $discoveryengineService->projects_locations_collections_engines_conversations;
 *  </code>
 */
class ProjectsLocationsCollectionsEnginesConversations extends \Google\Service\Resource
{
  /**
   * Converses a conversation. (conversations.converse)
   *
   * @param string $name Required. The resource name of the Conversation to get.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/dat
   * aStores/{data_store_id}/conversations/{conversation_id}`. Use `projects/{proj
   * ect}/locations/{location}/collections/{collection}/dataStores/{data_store_id}
   * /conversations/-` to activate auto session mode, which automatically creates
   * a new conversation inside a ConverseConversation session.
   * @param GoogleCloudDiscoveryengineV1ConverseConversationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1ConverseConversationResponse
   * @throws \Google\Service\Exception
   */
  public function converse($name, GoogleCloudDiscoveryengineV1ConverseConversationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('converse', [$params], GoogleCloudDiscoveryengineV1ConverseConversationResponse::class);
  }
  /**
   * Creates a Conversation. If the Conversation to create already exists, an
   * ALREADY_EXISTS error is returned. (conversations.create)
   *
   * @param string $parent Required. Full resource name of parent data store.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/dat
   * aStores/{data_store_id}`
   * @param GoogleCloudDiscoveryengineV1Conversation $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1Conversation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1Conversation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDiscoveryengineV1Conversation::class);
  }
  /**
   * Deletes a Conversation. If the Conversation to delete does not exist, a
   * NOT_FOUND error is returned. (conversations.delete)
   *
   * @param string $name Required. The resource name of the Conversation to
   * delete. Format: `projects/{project}/locations/{location}/collections/{collect
   * ion}/dataStores/{data_store_id}/conversations/{conversation_id}`
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
   * Gets a Conversation. (conversations.get)
   *
   * @param string $name Required. The resource name of the Conversation to get.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/dat
   * aStores/{data_store_id}/conversations/{conversation_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1Conversation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1Conversation::class);
  }
  /**
   * Lists all Conversations by their parent DataStore.
   * (conversations.listProjectsLocationsCollectionsEnginesConversations)
   *
   * @param string $parent Required. The data store resource name. Format: `projec
   * ts/{project}/locations/{location}/collections/{collection}/dataStores/{data_s
   * tore_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter to apply on the list results. The supported
   * features are: user_pseudo_id, state. Example: "user_pseudo_id = some_id"
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `update_time` * `create_time` * `conversation_name`
   * Example: "update_time desc" "create_time"
   * @opt_param int pageSize Maximum number of results to return. If unspecified,
   * defaults to 50. Max allowed value is 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListConversations` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudDiscoveryengineV1ListConversationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsEnginesConversations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListConversationsResponse::class);
  }
  /**
   * Updates a Conversation. Conversation action type cannot be changed. If the
   * Conversation to update does not exist, a NOT_FOUND error is returned.
   * (conversations.patch)
   *
   * @param string $name Immutable. Fully qualified name `projects/{project}/locat
   * ions/global/collections/{collection}/dataStore/conversations` or `projects/{p
   * roject}/locations/global/collections/{collection}/engines/conversations`.
   * @param GoogleCloudDiscoveryengineV1Conversation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Indicates which fields in the provided
   * Conversation to update. The following are NOT supported: * Conversation.name
   * If not set or empty, all supported fields are updated.
   * @return GoogleCloudDiscoveryengineV1Conversation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1Conversation $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1Conversation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsEnginesConversations::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsEnginesConversations');
