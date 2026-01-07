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

namespace Google\Service\Networkconnectivity\Resource;

use Google\Service\Networkconnectivity\GoogleLongrunningOperation;
use Google\Service\Networkconnectivity\ListRegionalEndpointsResponse;
use Google\Service\Networkconnectivity\RegionalEndpoint;

/**
 * The "regionalEndpoints" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkconnectivityService = new Google\Service\Networkconnectivity(...);
 *   $regionalEndpoints = $networkconnectivityService->projects_locations_regionalEndpoints;
 *  </code>
 */
class ProjectsLocationsRegionalEndpoints extends \Google\Service\Resource
{
  /**
   * Creates a new RegionalEndpoint in a given project and location.
   * (regionalEndpoints.create)
   *
   * @param string $parent Required. The parent resource's name of the
   * RegionalEndpoint.
   * @param RegionalEndpoint $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string regionalEndpointId Required. Unique id of the Regional
   * Endpoint to be created. @pattern: ^[-a-z0-9](?:[-a-z0-9]{0,44})[a-z0-9]$
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server knows to ignore the request if it has already been completed. The
   * server guarantees that for at least 60 minutes since the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if the original operation with the same request ID was
   * received, and if so, ignores the second request. This prevents clients from
   * accidentally creating duplicate commitments. The request ID must be a valid
   * UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, RegionalEndpoint $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a single RegionalEndpoint. (regionalEndpoints.delete)
   *
   * @param string $name Required. The name of the RegionalEndpoint to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server knows to ignore the request if it has already been completed. The
   * server guarantees that for at least 60 minutes since the first request. For
   * example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if the original operation with the same request ID was
   * received, and if so, ignores the second request. This prevents clients from
   * accidentally creating duplicate commitments. The request ID must be a valid
   * UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
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
   * Gets details of a single RegionalEndpoint. (regionalEndpoints.get)
   *
   * @param string $name Required. Name of the RegionalEndpoint resource to get.
   * Format: `projects/{project}/locations/{location}/regionalEndpoints/{regional_
   * endpoint}`
   * @param array $optParams Optional parameters.
   * @return RegionalEndpoint
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], RegionalEndpoint::class);
  }
  /**
   * Lists RegionalEndpoints in a given project and location.
   * (regionalEndpoints.listProjectsLocationsRegionalEndpoints)
   *
   * @param string $parent Required. The parent resource's name of the
   * RegionalEndpoint.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that filters the results listed
   * in the response.
   * @opt_param string orderBy Sort the results by a certain order.
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A page token.
   * @return ListRegionalEndpointsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsRegionalEndpoints($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRegionalEndpointsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsRegionalEndpoints::class, 'Google_Service_Networkconnectivity_Resource_ProjectsLocationsRegionalEndpoints');
