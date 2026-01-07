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

namespace Google\Service\SQLAdmin\Resource;

use Google\Service\SQLAdmin\Operation;
use Google\Service\SQLAdmin\User;
use Google\Service\SQLAdmin\UsersListResponse;

/**
 * The "users" collection of methods.
 * Typical usage is:
 *  <code>
 *   $sqladminService = new Google\Service\SQLAdmin(...);
 *   $users = $sqladminService->users;
 *  </code>
 */
class Users extends \Google\Service\Resource
{
  /**
   * Deletes a user from a Cloud SQL instance. (users.delete)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Database instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string host Host of the user in the instance.
   * @opt_param string name Name of the user in the instance.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Retrieves a resource containing information about a user. (users.get)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Database instance ID. This does not include the
   * project ID.
   * @param string $name User of the instance.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string host Host of a user of the instance.
   * @return User
   * @throws \Google\Service\Exception
   */
  public function get($project, $instance, $name, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], User::class);
  }
  /**
   * Creates a new user in a Cloud SQL instance. (users.insert)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Database instance ID. This does not include the
   * project ID.
   * @param User $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function insert($project, $instance, User $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Operation::class);
  }
  /**
   * Lists users in the specified Cloud SQL instance. (users.listUsers)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Database instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   * @return UsersListResponse
   * @throws \Google\Service\Exception
   */
  public function listUsers($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], UsersListResponse::class);
  }
  /**
   * Updates an existing user in a Cloud SQL instance. (users.update)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Database instance ID. This does not include the
   * project ID.
   * @param User $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string databaseRoles Optional. List of database roles to grant to
   * the user. body.database_roles will be ignored for update request.
   * @opt_param string host Optional. Host of the user in the instance.
   * @opt_param string name Name of the user in the instance.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function update($project, $instance, User $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Users::class, 'Google_Service_SQLAdmin_Resource_Users');
