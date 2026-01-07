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

namespace Google\Service\NetworkServices\Resource;

use Google\Service\NetworkServices\LbRouteExtension;
use Google\Service\NetworkServices\ListLbRouteExtensionsResponse;
use Google\Service\NetworkServices\Operation;

/**
 * The "lbRouteExtensions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkservicesService = new Google\Service\NetworkServices(...);
 *   $lbRouteExtensions = $networkservicesService->projects_locations_lbRouteExtensions;
 *  </code>
 */
class ProjectsLocationsLbRouteExtensions extends \Google\Service\Resource
{
  /**
   * Creates a new `LbRouteExtension` resource in a given project and location.
   * (lbRouteExtensions.create)
   *
   * @param string $parent Required. The parent resource of the `LbRouteExtension`
   * resource. Must be in the format `projects/{project}/locations/{location}`.
   * @param LbRouteExtension $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string lbRouteExtensionId Required. User-provided ID of the
   * `LbRouteExtension` resource to be created.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server can ignore the request if it has already been completed. The
   * server guarantees that for 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server
   * ignores the second request This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the exception
   * that zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, LbRouteExtension $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes the specified `LbRouteExtension` resource. (lbRouteExtensions.delete)
   *
   * @param string $name Required. The name of the `LbRouteExtension` resource to
   * delete. Must be in the format `projects/{project}/locations/{location}/lbRout
   * eExtensions/{lb_route_extension}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server can ignore the request if it has already been completed. The
   * server guarantees that for 60 minutes after the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server
   * ignores the second request This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the exception
   * that zero UUID is not supported (00000000-0000-0000-0000-000000000000).
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
   * Gets details of the specified `LbRouteExtension` resource.
   * (lbRouteExtensions.get)
   *
   * @param string $name Required. A name of the `LbRouteExtension` resource to
   * get. Must be in the format `projects/{project}/locations/{location}/lbRouteEx
   * tensions/{lb_route_extension}`.
   * @param array $optParams Optional parameters.
   * @return LbRouteExtension
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], LbRouteExtension::class);
  }
  /**
   * Lists `LbRouteExtension` resources in a given project and location.
   * (lbRouteExtensions.listProjectsLocationsLbRouteExtensions)
   *
   * @param string $parent Required. The project and location from which the
   * `LbRouteExtension` resources are listed. These values are specified in the
   * following format: `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint about how to order the results.
   * @opt_param int pageSize Optional. Requested page size. The server might
   * return fewer items than requested. If unspecified, the server picks an
   * appropriate default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * that the server returns.
   * @return ListLbRouteExtensionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsLbRouteExtensions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListLbRouteExtensionsResponse::class);
  }
  /**
   * Updates the parameters of the specified `LbRouteExtension` resource.
   * (lbRouteExtensions.patch)
   *
   * @param string $name Required. Identifier. Name of the `LbRouteExtension`
   * resource in the following format: `projects/{project}/locations/{location}/lb
   * RouteExtensions/{lb_route_extension}`.
   * @param LbRouteExtension $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server can ignore the request if it has already been completed. The
   * server guarantees that for 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server
   * ignores the second request This prevents clients from accidentally creating
   * duplicate commitments. The request ID must be a valid UUID with the exception
   * that zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Used to specify the fields to be
   * overwritten in the `LbRouteExtension` resource by the update. The fields
   * specified in the `update_mask` are relative to the resource, not the full
   * request. A field is overwritten if it is in the mask. If the user does not
   * specify a mask, then all fields are overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, LbRouteExtension $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsLbRouteExtensions::class, 'Google_Service_NetworkServices_Resource_ProjectsLocationsLbRouteExtensions');
