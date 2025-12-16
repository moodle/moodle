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

use Google\Service\Networkconnectivity\Destination;
use Google\Service\Networkconnectivity\GoogleLongrunningOperation;
use Google\Service\Networkconnectivity\ListDestinationsResponse;

/**
 * The "destinations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkconnectivityService = new Google\Service\Networkconnectivity(...);
 *   $destinations = $networkconnectivityService->projects_locations_multicloudDataTransferConfigs_destinations;
 *  </code>
 */
class ProjectsLocationsMulticloudDataTransferConfigsDestinations extends \Google\Service\Resource
{
  /**
   * Creates a `Destination` resource in a specified project and location.
   * (destinations.create)
   *
   * @param string $parent Required. The name of the parent resource.
   * @param Destination $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string destinationId Required. The ID to use for the `Destination`
   * resource, which becomes the final component of the `Destination` resource
   * name.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server can ignore the request if it has already been completed. The server
   * waits for at least 60 minutes since the first request. For example, consider
   * a situation where you make an initial request and the request times out. If
   * you make the request again with the same request ID, the server can check if
   * original operation with the same request ID was received, and if so, can
   * ignore the second request. This prevents clients from accidentally creating
   * duplicate `Destination` resources. The request ID must be a valid UUID with
   * the exception that zero UUID (00000000-0000-0000-0000-000000000000) isn't
   * supported.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Destination $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a `Destination` resource. (destinations.delete)
   *
   * @param string $name Required. The name of the `Destination` resource to
   * delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The etag is computed by the server, and
   * might be sent with update and delete requests so that the client has an up-
   * to-date value before proceeding.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server can ignore the request if it has already been completed. The server
   * waits for at least 60 minutes since the first request. For example, consider
   * a situation where you make an initial request and the request times out. If
   * you make the request again with the same request ID, the server can check if
   * original operation with the same request ID was received, and if so, can
   * ignore the second request. The request ID must be a valid UUID with the
   * exception that zero UUID (00000000-0000-0000-0000-000000000000) isn't
   * supported.
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
   * Gets the details of a `Destination` resource. (destinations.get)
   *
   * @param string $name Required. The name of the `Destination` resource to get.
   * @param array $optParams Optional parameters.
   * @return Destination
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Destination::class);
  }
  /**
   * Lists the `Destination` resources in a specified project and location.
   * (destinations.listProjectsLocationsMulticloudDataTransferConfigsDestinations)
   *
   * @param string $parent Required. The name of the parent resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the results
   * listed in the response.
   * @opt_param string orderBy Optional. The sort order of the results.
   * @opt_param int pageSize Optional. The maximum number of results listed per
   * page.
   * @opt_param string pageToken Optional. The page token.
   * @opt_param bool returnPartialSuccess Optional. If `true`, allow partial
   * responses for multi-regional aggregated list requests.
   * @return ListDestinationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMulticloudDataTransferConfigsDestinations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDestinationsResponse::class);
  }
  /**
   * Updates a `Destination` resource in a specified project and location.
   * (destinations.patch)
   *
   * @param string $name Identifier. The name of the `Destination` resource.
   * Format: `projects/{project}/locations/{location}/multicloudDataTransferConfig
   * s/{multicloud_data_transfer_config}/destinations/{destination}`.
   * @param Destination $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server can ignore the request if it has already been completed. The server
   * waits for at least 60 minutes since the first request. For example, consider
   * a situation where you make an initial request and the request times out. If
   * you make the request again with the same request ID, the server can check if
   * original operation with the same request ID was received, and if so, can
   * ignore the second request. The request ID must be a valid UUID with the
   * exception that zero UUID (00000000-0000-0000-0000-000000000000) isn't
   * supported.
   * @opt_param string updateMask Optional. `FieldMask is used to specify the
   * fields to be overwritten in the `Destination` resource by the update. The
   * fields specified in `update_mask` are relative to the resource, not the full
   * request. A field is overwritten if it is in the mask. If you don't specify a
   * mask, all fields are overwritten.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Destination $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMulticloudDataTransferConfigsDestinations::class, 'Google_Service_Networkconnectivity_Resource_ProjectsLocationsMulticloudDataTransferConfigsDestinations');
