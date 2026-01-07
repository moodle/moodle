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

use Google\Service\DeveloperConnect\AccountConnector;
use Google\Service\DeveloperConnect\ListAccountConnectorsResponse;
use Google\Service\DeveloperConnect\Operation;

/**
 * The "accountConnectors" collection of methods.
 * Typical usage is:
 *  <code>
 *   $developerconnectService = new Google\Service\DeveloperConnect(...);
 *   $accountConnectors = $developerconnectService->projects_locations_accountConnectors;
 *  </code>
 */
class ProjectsLocationsAccountConnectors extends \Google\Service\Resource
{
  /**
   * Creates a new AccountConnector in a given project and location.
   * (accountConnectors.create)
   *
   * @param string $parent Required. Location resource name as the
   * account_connector’s parent.
   * @param AccountConnector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string accountConnectorId Required. The ID to use for the
   * AccountConnector, which will become the final component of the
   * AccountConnector's resource name. Its format should adhere to
   * https://google.aip.dev/122#resource-id-segments Names must be unique per-
   * project per-location.
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
  public function create($parent, AccountConnector $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single AccountConnector. (accountConnectors.delete)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the AccountConnectorn.
   * If an etag is provided and does not match the current etag of the
   * AccountConnector, deletion will be blocked and an ABORTED error will be
   * returned.
   * @opt_param bool force Optional. If set to true, any Users from this
   * AccountConnector will also be deleted. (Otherwise, the request will only work
   * if the AccountConnector has no Users.)
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
   * Gets details of a single AccountConnector. (accountConnectors.get)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   * @return AccountConnector
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AccountConnector::class);
  }
  /**
   * Lists AccountConnectors in a given project and location.
   * (accountConnectors.listProjectsLocationsAccountConnectors)
   *
   * @param string $parent Required. Parent value for ListAccountConnectorsRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListAccountConnectorsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAccountConnectors($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAccountConnectorsResponse::class);
  }
  /**
   * Updates the parameters of a single AccountConnector.
   * (accountConnectors.patch)
   *
   * @param string $name Identifier. The resource name of the accountConnector, in
   * the format `projects/{project}/locations/{location}/accountConnectors/{accoun
   * t_connector_id}`.
   * @param AccountConnector $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, and the
   * accountConnector is not found a new accountConnector will be created. In this
   * situation `update_mask` is ignored. The creation will succeed only if the
   * input accountConnector has all the necessary
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
   * @opt_param string updateMask Optional. The list of fields to be updated.
   * @opt_param bool validateOnly Optional. If set, validate the request, but do
   * not actually post it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, AccountConnector $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAccountConnectors::class, 'Google_Service_DeveloperConnect_Resource_ProjectsLocationsAccountConnectors');
