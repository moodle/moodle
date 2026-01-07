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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1AppendEventResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListSessionsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1Session;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SessionEvent;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "sessions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $sessions = $aiplatformService->projects_locations_reasoningEngines_sessions;
 *  </code>
 */
class ProjectsLocationsReasoningEnginesSessions extends \Google\Service\Resource
{
  /**
   * Appends an event to a given session. (sessions.appendEvent)
   *
   * @param string $name Required. The resource name of the session to append
   * event to. Format: `projects/{project}/locations/{location}/reasoningEngines/{
   * reasoning_engine}/sessions/{session}`
   * @param GoogleCloudAiplatformV1SessionEvent $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1AppendEventResponse
   * @throws \Google\Service\Exception
   */
  public function appendEvent($name, GoogleCloudAiplatformV1SessionEvent $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('appendEvent', [$params], GoogleCloudAiplatformV1AppendEventResponse::class);
  }
  /**
   * Creates a new Session. (sessions.create)
   *
   * @param string $parent Required. The resource name of the location to create
   * the session in. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param GoogleCloudAiplatformV1Session $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Session $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes details of the specific Session. (sessions.delete)
   *
   * @param string $name Required. The resource name of the session. Format: `proj
   * ects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}/sessi
   * ons/{session}`
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets details of the specific Session. (sessions.get)
   *
   * @param string $name Required. The resource name of the session. Format: `proj
   * ects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}/sessi
   * ons/{session}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Session
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Session::class);
  }
  /**
   * Lists Sessions in a given reasoning engine.
   * (sessions.listProjectsLocationsReasoningEnginesSessions)
   *
   * @param string $parent Required. The resource name of the location to list
   * sessions from. Format:
   * `projects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The standard list filter. Supported
   * fields: * `display_name` * `user_id` * `labels` Example:
   * `display_name="abc"`, `user_id="123"`, `labels.key="value"`.
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `create_time` * `update_time` Example: `create_time
   * desc`.
   * @opt_param int pageSize Optional. The maximum number of sessions to return.
   * The service may return fewer than this value. If unspecified, at most 100
   * sessions will be returned.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous list SessionService.ListSessions call.
   * @return GoogleCloudAiplatformV1ListSessionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsReasoningEnginesSessions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListSessionsResponse::class);
  }
  /**
   * Updates the specific Session. (sessions.patch)
   *
   * @param string $name Identifier. The resource name of the session. Format: 'pr
   * ojects/{project}/locations/{location}/reasoningEngines/{reasoning_engine}/ses
   * sions/{session}'.
   * @param GoogleCloudAiplatformV1Session $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to control which
   * fields get updated. If the mask is not present, all fields will be updated.
   * @return GoogleCloudAiplatformV1Session
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1Session $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1Session::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsReasoningEnginesSessions::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsReasoningEnginesSessions');
