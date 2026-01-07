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

use Google\Service\FirebaseAppHosting\ListRolloutsResponse;
use Google\Service\FirebaseAppHosting\Operation;
use Google\Service\FirebaseAppHosting\Rollout;

/**
 * The "rollouts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseapphostingService = new Google\Service\FirebaseAppHosting(...);
 *   $rollouts = $firebaseapphostingService->projects_locations_backends_rollouts;
 *  </code>
 */
class ProjectsLocationsBackendsRollouts extends \Google\Service\Resource
{
  /**
   * Creates a new rollout for a backend. (rollouts.create)
   *
   * @param string $parent Required. The parent backend in the format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}`.
   * @param Rollout $postBody
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
   * @opt_param string rolloutId Optional. Desired ID of the rollout being
   * created.
   * @opt_param bool validateOnly Optional. Indicates that the request should be
   * validated and default values populated, without persisting the request or
   * creating any resources.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Rollout $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Gets information about a rollout. (rollouts.get)
   *
   * @param string $name Required. Name of the resource in the format: `projects/{
   * project}/locations/{locationId}/backends/{backendId}/rollouts/{rolloutId}`.
   * @param array $optParams Optional parameters.
   * @return Rollout
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Rollout::class);
  }
  /**
   * Lists rollouts for a backend.
   * (rollouts.listProjectsLocationsBackendsRollouts)
   *
   * @param string $parent Required. The parent backend in the format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter to narrow down results to a
   * preferred subset. Learn more about filtering in Google's [AIP 160
   * standard](https://google.aip.dev/160).
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * Supported fields are `name` and `createTime`. To specify descending order,
   * append a `desc` suffix.
   * @opt_param int pageSize Optional. The maximum number of results to return. If
   * not set, the service selects a default.
   * @opt_param string pageToken Optional. A page token received from the
   * nextPageToken field in the response. Send that page token to receive the
   * subsequent page.
   * @opt_param bool showDeleted Optional. If true, the request returns soft-
   * deleted resources that haven't been fully-deleted yet.
   * @return ListRolloutsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackendsRollouts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListRolloutsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackendsRollouts::class, 'Google_Service_FirebaseAppHosting_Resource_ProjectsLocationsBackendsRollouts');
