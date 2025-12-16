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

namespace Google\Service\IDS\Resource;

use Google\Service\IDS\Endpoint;
use Google\Service\IDS\ListEndpointsResponse;
use Google\Service\IDS\Operation;

/**
 * The "endpoints" collection of methods.
 * Typical usage is:
 *  <code>
 *   $idsService = new Google\Service\IDS(...);
 *   $endpoints = $idsService->projects_locations_endpoints;
 *  </code>
 */
class ProjectsLocationsEndpoints extends \Google\Service\Resource
{
  /**
   * Creates a new Endpoint in a given project and location. (endpoints.create)
   *
   * @param string $parent Required. The endpoint's parent.
   * @param Endpoint $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string endpointId Required. The endpoint identifier. This will be
   * part of the endpoint's resource name. This value must start with a lowercase
   * letter followed by up to 62 lowercase letters, numbers, or hyphens, and
   * cannot end with a hyphen. Values that do not match this pattern will trigger
   * an INVALID_ARGUMENT error.
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Endpoint $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Endpoint. (endpoints.delete)
   *
   * @param string $name Required. The name of the endpoint to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes after the first request.
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
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
   * Gets details of a single Endpoint. (endpoints.get)
   *
   * @param string $name Required. The name of the endpoint to retrieve. Format:
   * projects/{project}/locations/{location}/endpoints/{endpoint}
   * @param array $optParams Optional parameters.
   * @return Endpoint
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Endpoint::class);
  }
  /**
   * Lists Endpoints in a given project and location.
   * (endpoints.listProjectsLocationsEndpoints)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * endpoints.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter expression, following the
   * syntax outlined in https://google.aip.dev/160.
   * @opt_param string orderBy Optional. One or more fields to compare and use to
   * sort the output. See https://google.aip.dev/132#ordering.
   * @opt_param int pageSize Optional. The maximum number of endpoints to return.
   * The service may return fewer than this value.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListEndpoints` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListEndpoints` must match the
   * call that provided the page token.
   * @return ListEndpointsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEndpoints($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListEndpointsResponse::class);
  }
  /**
   * Updates the parameters of a single Endpoint. (endpoints.patch)
   *
   * @param string $name Output only. The name of the endpoint.
   * @param Endpoint $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Field mask is used to specify the fields to be
   * overwritten in the Endpoint resource by the update. The fields specified in
   * the update_mask are relative to the resource, not the full request. A field
   * will be overwritten if it is in the mask. If the user does not provide a mask
   * then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Endpoint $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEndpoints::class, 'Google_Service_IDS_Resource_ProjectsLocationsEndpoints');
