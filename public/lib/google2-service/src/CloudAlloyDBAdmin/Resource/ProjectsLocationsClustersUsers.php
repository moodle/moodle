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

namespace Google\Service\CloudAlloyDBAdmin\Resource;

use Google\Service\CloudAlloyDBAdmin\AlloydbEmpty;
use Google\Service\CloudAlloyDBAdmin\ListUsersResponse;
use Google\Service\CloudAlloyDBAdmin\User;

/**
 * The "users" collection of methods.
 * Typical usage is:
 *  <code>
 *   $alloydbService = new Google\Service\CloudAlloyDBAdmin(...);
 *   $users = $alloydbService->projects_locations_clusters_users;
 *  </code>
 */
class ProjectsLocationsClustersUsers extends \Google\Service\Resource
{
  /**
   * Creates a new User in a given project, location, and cluster. (users.create)
   *
   * @param string $parent Required. Value for parent.
   * @param User $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string userId Required. ID of the requesting object.
   * @opt_param bool validateOnly Optional. If set, the backend validates the
   * request, but doesn't actually execute it.
   * @return User
   * @throws \Google\Service\Exception
   */
  public function create($parent, User $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], User::class);
  }
  /**
   * Deletes a single User. (users.delete)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the User.name field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, the backend validates the
   * request, but doesn't actually execute it.
   * @return AlloydbEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], AlloydbEmpty::class);
  }
  /**
   * Gets details of a single User. (users.get)
   *
   * @param string $name Required. The name of the resource. For the required
   * format, see the comment on the User.name field.
   * @param array $optParams Optional parameters.
   * @return User
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], User::class);
  }
  /**
   * Lists Users in a given project and location.
   * (users.listProjectsLocationsClustersUsers)
   *
   * @param string $parent Required. Parent value for ListUsersRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListUsersResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsClustersUsers($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListUsersResponse::class);
  }
  /**
   * Updates the parameters of a single User. (users.patch)
   *
   * @param string $name Output only. Name of the resource in the form of
   * projects/{project}/locations/{location}/cluster/{cluster}/users/{user}.
   * @param User $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. Allow missing fields in the update
   * mask.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the User resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. If set, the backend validates the
   * request, but doesn't actually execute it.
   * @return User
   * @throws \Google\Service\Exception
   */
  public function patch($name, User $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], User::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsClustersUsers::class, 'Google_Service_CloudAlloyDBAdmin_Resource_ProjectsLocationsClustersUsers');
