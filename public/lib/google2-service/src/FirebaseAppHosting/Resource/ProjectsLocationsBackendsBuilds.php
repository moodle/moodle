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

use Google\Service\FirebaseAppHosting\Build;
use Google\Service\FirebaseAppHosting\ListBuildsResponse;
use Google\Service\FirebaseAppHosting\Operation;

/**
 * The "builds" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firebaseapphostingService = new Google\Service\FirebaseAppHosting(...);
 *   $builds = $firebaseapphostingService->projects_locations_backends_builds;
 *  </code>
 */
class ProjectsLocationsBackendsBuilds extends \Google\Service\Resource
{
  /**
   * Creates a new build for a backend. (builds.create)
   *
   * @param string $parent Required. The parent backend in the format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}`.
   * @param Build $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string buildId Required. Desired ID of the build being created.
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
   * @opt_param bool validateOnly Optional. Indicates that the request should be
   * validated and default values populated, without persisting the request or
   * creating any resources.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Build $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single build. (builds.delete)
   *
   * @param string $name Required. Name of the resource in the format: `projects/{
   * project}/locations/{locationId}/backends/{backendId}/builds/{buildId}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. If the client provided etag is out of date,
   * delete will be returned FAILED_PRECONDITION error.
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
   * @opt_param bool validateOnly Optional. Indicates that the request should be
   * validated and default values populated, without persisting the request or
   * deleting any resources.
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
   * Gets information about a build. (builds.get)
   *
   * @param string $name Required. Name of the resource in the format: `projects/{
   * project}/locations/{locationId}/backends/{backendId}/builds/{buildId}`.
   * @param array $optParams Optional parameters.
   * @return Build
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Build::class);
  }
  /**
   * Lists builds in a given project, location, and backend.
   * (builds.listProjectsLocationsBackendsBuilds)
   *
   * @param string $parent Required. The parent backend in the form
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
   * @return ListBuildsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackendsBuilds($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBuildsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackendsBuilds::class, 'Google_Service_FirebaseAppHosting_Resource_ProjectsLocationsBackendsBuilds');
