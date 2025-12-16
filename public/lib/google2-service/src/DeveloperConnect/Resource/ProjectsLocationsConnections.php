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

namespace Google\Service\DeveloperConnect\Resource;

use Google\Service\DeveloperConnect\Connection;
use Google\Service\DeveloperConnect\DeveloperconnectEmpty;
use Google\Service\DeveloperConnect\FetchGitHubInstallationsResponse;
use Google\Service\DeveloperConnect\FetchLinkableGitRepositoriesResponse;
use Google\Service\DeveloperConnect\ListConnectionsResponse;
use Google\Service\DeveloperConnect\Operation;
use Google\Service\DeveloperConnect\ProcessGitHubEnterpriseWebhookRequest;

/**
 * The "connections" collection of methods.
 * Typical usage is:
 *  <code>
 *   $developerconnectService = new Google\Service\DeveloperConnect(...);
 *   $connections = $developerconnectService->projects_locations_connections;
 *  </code>
 */
class ProjectsLocationsConnections extends \Google\Service\Resource
{
  /**
   * Creates a new Connection in a given project and location.
   * (connections.create)
   *
   * @param string $parent Required. Value for parent.
   * @param Connection $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string connectionId Required. Id of the requesting object If auto-
   * generating Id server-side, remove this field and connection_id from the
   * method_signature of Create RPC
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
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Connection $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Connection. (connections.delete)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the Connection. If an
   * etag is provided and does not match the current etag of the Connection,
   * deletion will be blocked and an ABORTED error will be returned.
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
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
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
   * FetchGitHubInstallations returns the list of GitHub Installations that are
   * available to be added to a Connection. For github.com, only installations
   * accessible to the authorizer token are returned. For GitHub Enterprise, all
   * installations are returned. (connections.fetchGitHubInstallations)
   *
   * @param string $connection Required. The resource name of the connection in
   * the format `projects/locations/connections`.
   * @param array $optParams Optional parameters.
   * @return FetchGitHubInstallationsResponse
   * @throws \Google\Service\Exception
   */
  public function fetchGitHubInstallations($connection, $optParams = [])
  {
    $params = ['connection' => $connection];
    $params = array_merge($params, $optParams);
    return $this->call('fetchGitHubInstallations', [$params], FetchGitHubInstallationsResponse::class);
  }
  /**
   * FetchLinkableGitRepositories returns a list of git repositories from an SCM
   * that are available to be added to a Connection.
   * (connections.fetchLinkableGitRepositories)
   *
   * @param string $connection Required. The name of the Connection. Format:
   * `projects/locations/connections`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Number of results to return in the list.
   * Defaults to 20.
   * @opt_param string pageToken Optional. Page start.
   * @return FetchLinkableGitRepositoriesResponse
   * @throws \Google\Service\Exception
   */
  public function fetchLinkableGitRepositories($connection, $optParams = [])
  {
    $params = ['connection' => $connection];
    $params = array_merge($params, $optParams);
    return $this->call('fetchLinkableGitRepositories', [$params], FetchLinkableGitRepositoriesResponse::class);
  }
  /**
   * Gets details of a single Connection. (connections.get)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   * @return Connection
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Connection::class);
  }
  /**
   * Lists Connections in a given project and location.
   * (connections.listProjectsLocationsConnections)
   *
   * @param string $parent Required. Parent value for ListConnectionsRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListConnectionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsConnections($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListConnectionsResponse::class);
  }
  /**
   * Updates the parameters of a single Connection. (connections.patch)
   *
   * @param string $name Identifier. The resource name of the connection, in the
   * format `projects/{project}/locations/{location}/connections/{connection_id}`.
   * @param Connection $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the connection is
   * not found a new connection will be created. In this situation `update_mask`
   * is ignored. The creation will succeed only if the input connection has all
   * the necessary information (e.g a github_config with both user_oauth_token and
   * installation_id properties).
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
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Connection resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Connection $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * ProcessGitHubEnterpriseWebhook is called by the external GitHub Enterprise
   * instances for notifying events. (connections.processGitHubEnterpriseWebhook)
   *
   * @param string $parent Required. Project and location where the webhook will
   * be received. Format: `projects/locations`.
   * @param ProcessGitHubEnterpriseWebhookRequest $postBody
   * @param array $optParams Optional parameters.
   * @return DeveloperconnectEmpty
   * @throws \Google\Service\Exception
   */
  public function processGitHubEnterpriseWebhook($parent, ProcessGitHubEnterpriseWebhookRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('processGitHubEnterpriseWebhook', [$params], DeveloperconnectEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsConnections::class, 'Google_Service_DeveloperConnect_Resource_ProjectsLocationsConnections');
