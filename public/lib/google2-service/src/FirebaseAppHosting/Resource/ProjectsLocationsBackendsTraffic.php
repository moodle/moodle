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

namespace Google\Service\FirebaseAppHosting\Resource;

use Google\Service\FirebaseAppHosting\Operation;
use Google\Service\FirebaseAppHosting\Traffic;

/**
 * The "traffic" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseapphostingService = new Google\Service\FirebaseAppHosting(...);
 *   $traffic = $firebaseapphostingService->projects_locations_backends_traffic;
 *  </code>
 */
class ProjectsLocationsBackendsTraffic extends \Google\Service\Resource
{
  /**
   * Gets information about a backend's traffic. (traffic.get)
   *
   * @param string $name Required. Name of the resource in the format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}/traffic`.
   * @param array $optParams Optional parameters.
   * @return Traffic
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Traffic::class);
  }
  /**
   * Updates a backend's traffic. (traffic.patch)
   *
   * @param string $name Identifier. The resource name of the backend's traffic.
   * Format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}/traffic`.
   * @param Traffic $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and t he request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the traffic resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. Indicates that the request should be
   * validated, without persisting the request or updating any resources.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Traffic $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackendsTraffic::class, 'Google_Service_FirebaseAppHosting_Resource_ProjectsLocationsBackendsTraffic');
