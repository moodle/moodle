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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListSessionsResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1Session;
use Google\Service\DiscoveryEngine\GoogleProtobufEmpty;

/**
 * The "sessions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $sessions = $discoveryengineService->projects_locations_collections_engines_sessions;
 *  </code>
 */
class ProjectsLocationsCollectionsEnginesSessions extends \Google\Service\Resource
{
  /**
   * Creates a Session. If the Session to create already exists, an ALREADY_EXISTS
   * error is returned. (sessions.create)
   *
   * @param string $parent Required. Full resource name of parent data store.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/dat
   * aStores/{data_store_id}`
   * @param GoogleCloudDiscoveryengineV1Session $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1Session
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1Session $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDiscoveryengineV1Session::class);
  }
  /**
   * Deletes a Session. If the Session to delete does not exist, a NOT_FOUND error
   * is returned. (sessions.delete)
   *
   * @param string $name Required. The resource name of the Session to delete.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/dat
   * aStores/{data_store_id}/sessions/{session_id}`
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
   * Gets a Session. (sessions.get)
   *
   * @param string $name Required. The resource name of the Session to get.
   * Format: `projects/{project}/locations/{location}/collections/{collection}/dat
   * aStores/{data_store_id}/sessions/{session_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool includeAnswerDetails Optional. If set to true, the full
   * session including all answer details will be returned.
   * @return GoogleCloudDiscoveryengineV1Session
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1Session::class);
  }
  /**
   * Lists all Sessions by their parent DataStore.
   * (sessions.listProjectsLocationsCollectionsEnginesSessions)
   *
   * @param string $parent Required. The data store resource name. Format: `projec
   * ts/{project}/locations/{location}/collections/{collection}/dataStores/{data_s
   * tore_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A comma-separated list of fields to filter by, in
   * EBNF grammar. The supported fields are: * `user_pseudo_id` * `state` *
   * `display_name` * `starred` * `is_pinned` * `labels` * `create_time` *
   * `update_time` Examples: * `user_pseudo_id = some_id` * `display_name =
   * "some_name"` * `starred = true` * `is_pinned=true AND (NOT labels:hidden)` *
   * `create_time > "1970-01-01T12:00:00Z"`
   * @opt_param string orderBy A comma-separated list of fields to order by,
   * sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `update_time` * `create_time` * `session_name` *
   * `is_pinned` Example: * `update_time desc` * `create_time` * `is_pinned
   * desc,update_time desc`: list sessions by is_pinned first, then by
   * update_time.
   * @opt_param int pageSize Maximum number of results to return. If unspecified,
   * defaults to 50. Max allowed value is 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListSessions` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudDiscoveryengineV1ListSessionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsEnginesSessions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListSessionsResponse::class);
  }
  /**
   * Updates a Session. Session action type cannot be changed. If the Session to
   * update does not exist, a NOT_FOUND error is returned. (sessions.patch)
   *
   * @param string $name Immutable. Fully qualified name `projects/{project}/locat
   * ions/global/collections/{collection}/engines/{engine}/sessions`
   * @param GoogleCloudDiscoveryengineV1Session $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Indicates which fields in the provided Session
   * to update. The following are NOT supported: * Session.name If not set or
   * empty, all supported fields are updated.
   * @return GoogleCloudDiscoveryengineV1Session
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1Session $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1Session::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsEnginesSessions::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsEnginesSessions');
