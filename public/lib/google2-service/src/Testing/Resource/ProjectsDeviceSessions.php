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

namespace Google\Service\Testing\Resource;

use Google\Service\Testing\CancelDeviceSessionRequest;
use Google\Service\Testing\DeviceSession;
use Google\Service\Testing\ListDeviceSessionsResponse;
use Google\Service\Testing\TestingEmpty;

/**
 * The "deviceSessions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $testingService = new Google\Service\Testing(...);
 *   $deviceSessions = $testingService->projects_deviceSessions;
 *  </code>
 */
class ProjectsDeviceSessions extends \Google\Service\Resource
{
  /**
   * POST /v1/projects/{project_id}/deviceSessions/{device_session_id}:cancel
   * Changes the DeviceSession to state FINISHED and terminates all connections.
   * Canceled sessions are not deleted and can be retrieved or listed by the user
   * until they expire based on the 28 day deletion policy.
   * (deviceSessions.cancel)
   *
   * @param string $name Required. Name of the DeviceSession, e.g.
   * "projects/{project_id}/deviceSessions/{session_id}"
   * @param CancelDeviceSessionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestingEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelDeviceSessionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], TestingEmpty::class);
  }
  /**
   * POST /v1/projects/{project_id}/deviceSessions (deviceSessions.create)
   *
   * @param string $parent Required. The Compute Engine project under which this
   * device will be allocated. "projects/{project_id}"
   * @param DeviceSession $postBody
   * @param array $optParams Optional parameters.
   * @return DeviceSession
   * @throws \Google\Service\Exception
   */
  public function create($parent, DeviceSession $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], DeviceSession::class);
  }
  /**
   * GET /v1/projects/{project_id}/deviceSessions/{device_session_id} Return a
   * DeviceSession, which documents the allocation status and whether the device
   * is allocated. Clients making requests from this API must poll
   * GetDeviceSession. (deviceSessions.get)
   *
   * @param string $name Required. Name of the DeviceSession, e.g.
   * "projects/{project_id}/deviceSessions/{session_id}"
   * @param array $optParams Optional parameters.
   * @return DeviceSession
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DeviceSession::class);
  }
  /**
   * GET /v1/projects/{project_id}/deviceSessions Lists device Sessions owned by
   * the project user. (deviceSessions.listProjectsDeviceSessions)
   *
   * @param string $parent Required. The name of the parent to request, e.g.
   * "projects/{project_id}"
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. If specified, responses will be filtered
   * by the given filter. Allowed fields are: session_state.
   * @opt_param int pageSize Optional. The maximum number of DeviceSessions to
   * return.
   * @opt_param string pageToken Optional. A continuation token for paging.
   * @return ListDeviceSessionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsDeviceSessions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDeviceSessionsResponse::class);
  }
  /**
   * PATCH
   * /v1/projects/{projectId}/deviceSessions/deviceSessionId}:updateDeviceSession
   * Updates the current device session to the fields described by the
   * update_mask. (deviceSessions.patch)
   *
   * @param string $name Optional. Name of the DeviceSession, e.g.
   * "projects/{project_id}/deviceSessions/{session_id}"
   * @param DeviceSession $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The list of fields to update.
   * @return DeviceSession
   * @throws \Google\Service\Exception
   */
  public function patch($name, DeviceSession $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], DeviceSession::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsDeviceSessions::class, 'Google_Service_Testing_Resource_ProjectsDeviceSessions');
