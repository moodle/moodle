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

namespace Google\Service\DLP\Resource;

use Google\Service\DLP\GooglePrivacyDlpV2Connection;
use Google\Service\DLP\GooglePrivacyDlpV2CreateConnectionRequest;
use Google\Service\DLP\GooglePrivacyDlpV2ListConnectionsResponse;
use Google\Service\DLP\GooglePrivacyDlpV2SearchConnectionsResponse;
use Google\Service\DLP\GooglePrivacyDlpV2UpdateConnectionRequest;
use Google\Service\DLP\GoogleProtobufEmpty;

/**
 * The "connections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dlpService = new Google\Service\DLP(...);
 *   $connections = $dlpService->projects_locations_connections;
 *  </code>
 */
class ProjectsLocationsConnections extends \Google\Service\Resource
{
  /**
   * Create a Connection to an external data source. (connections.create)
   *
   * @param string $parent Required. Parent resource name. The format of this
   * value varies depending on the scope of the request (project or organization):
   * + Projects scope: `projects/{project_id}/locations/{location_id}` +
   * Organizations scope: `organizations/{org_id}/locations/{location_id}`
   * @param GooglePrivacyDlpV2CreateConnectionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2Connection
   * @throws \Google\Service\Exception
   */
  public function create($parent, GooglePrivacyDlpV2CreateConnectionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GooglePrivacyDlpV2Connection::class);
  }
  /**
   * Delete a Connection. (connections.delete)
   *
   * @param string $name Required. Resource name of the Connection to be deleted,
   * in the format:
   * `projects/{project}/locations/{location}/connections/{connection}`.
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
   * Get a Connection by name. (connections.get)
   *
   * @param string $name Required. Resource name in the format:
   * `projects/{project}/locations/{location}/connections/{connection}`.
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2Connection
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GooglePrivacyDlpV2Connection::class);
  }
  /**
   * Lists Connections in a parent. Use SearchConnections to see all connections
   * within an organization. (connections.listProjectsLocationsConnections)
   *
   * @param string $parent Required. Resource name of the organization or project,
   * for example, `organizations/433245324/locations/europe` or `projects/project-
   * id/locations/asia`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Supported field/value: `state` -
   * MISSING|AVAILABLE|ERROR The syntax is based on https://google.aip.dev/160.
   * @opt_param int pageSize Optional. Number of results per page, max 1000.
   * @opt_param string pageToken Optional. Page token from a previous page to
   * return the next set of results. If set, all other request fields must match
   * the original request.
   * @return GooglePrivacyDlpV2ListConnectionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnections($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GooglePrivacyDlpV2ListConnectionsResponse::class);
  }
  /**
   * Update a Connection. (connections.patch)
   *
   * @param string $name Required. Resource name in the format:
   * `projects/{project}/locations/{location}/connections/{connection}`.
   * @param GooglePrivacyDlpV2UpdateConnectionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GooglePrivacyDlpV2Connection
   * @throws \Google\Service\Exception
   */
  public function patch($name, GooglePrivacyDlpV2UpdateConnectionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GooglePrivacyDlpV2Connection::class);
  }
  /**
   * Searches for Connections in a parent. (connections.search)
   *
   * @param string $parent Required. Resource name of the organization or project
   * with a wildcard location, for example, `organizations/433245324/locations/-`
   * or `projects/project-id/locations/-`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Supported field/value: - `state` -
   * MISSING|AVAILABLE|ERROR The syntax is based on https://google.aip.dev/160.
   * @opt_param int pageSize Optional. Number of results per page, max 1000.
   * @opt_param string pageToken Optional. Page token from a previous page to
   * return the next set of results. If set, all other request fields must match
   * the original request.
   * @return GooglePrivacyDlpV2SearchConnectionsResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], GooglePrivacyDlpV2SearchConnectionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnections::class, 'Google_Service_DLP_Resource_ProjectsLocationsConnections');
