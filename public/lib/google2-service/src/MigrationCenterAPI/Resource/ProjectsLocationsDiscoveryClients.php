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

namespace Google\Service\MigrationCenterAPI\Resource;

use Google\Service\MigrationCenterAPI\DiscoveryClient;
use Google\Service\MigrationCenterAPI\ListDiscoveryClientsResponse;
use Google\Service\MigrationCenterAPI\Operation;
use Google\Service\MigrationCenterAPI\SendDiscoveryClientHeartbeatRequest;

/**
 * The "discoveryClients" collection of methods.
 * Typical usage is:
 *  <code>
 *   $migrationcenterService = new Google\Service\MigrationCenterAPI(...);
 *   $discoveryClients = $migrationcenterService->projects_locations_discoveryClients;
 *  </code>
 */
class ProjectsLocationsDiscoveryClients extends \Google\Service\Resource
{
  /**
   * Creates a new discovery client. (discoveryClients.create)
   *
   * @param string $parent Required. Parent resource.
   * @param DiscoveryClient $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string discoveryClientId Required. User specified ID for the
   * discovery client. It will become the last component of the discovery client
   * name. The ID must be unique within the project, is restricted to lower-cased
   * letters and has a maximum length of 63 characters. The ID must match the
   * regular expression: `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, DiscoveryClient $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a discovery client. (discoveryClients.delete)
   *
   * @param string $name Required. The discovery client name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets the details of a discovery client. (discoveryClients.get)
   *
   * @param string $name Required. The discovery client name.
   * @param array $optParams Optional parameters.
   * @return DiscoveryClient
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DiscoveryClient::class);
  }
  /**
   * Lists all the discovery clients in a given project and location.
   * (discoveryClients.listProjectsLocationsDiscoveryClients)
   *
   * @param string $parent Required. Parent resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression to filter results by.
   * @opt_param string orderBy Optional. Field to sort by.
   * @opt_param int pageSize Optional. The maximum number of items to return. The
   * server may return fewer items than requested. If unspecified, the server will
   * pick an appropriate default value.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListDiscoveryClients` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListDiscoveryClients` must
   * match the call that provided the page token.
   * @return ListDiscoveryClientsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDiscoveryClients($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDiscoveryClientsResponse::class);
  }
  /**
   * Updates a discovery client. (discoveryClients.patch)
   *
   * @param string $name Output only. Identifier. Full name of this discovery
   * client.
   * @param DiscoveryClient $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Update mask is used to specify the
   * fields to be overwritten in the `DiscoveryClient` resource by the update. The
   * values specified in the `update_mask` field are relative to the resource, not
   * the full request. A field will be overwritten if it is in the mask. A single
   * * value in the mask lets you to overwrite all fields.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, DiscoveryClient $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Sends a discovery client heartbeat. Healthy clients are expected to send
   * heartbeats regularly (normally every few minutes).
   * (discoveryClients.sendHeartbeat)
   *
   * @param string $name Required. The discovery client name.
   * @param SendDiscoveryClientHeartbeatRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function sendHeartbeat($name, SendDiscoveryClientHeartbeatRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('sendHeartbeat', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDiscoveryClients::class, 'Google_Service_MigrationCenterAPI_Resource_ProjectsLocationsDiscoveryClients');
