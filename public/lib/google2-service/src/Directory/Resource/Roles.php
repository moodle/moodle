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

namespace Google\Service\Directory\Resource;

use Google\Service\Directory\Role;
use Google\Service\Directory\Roles as RolesModel;

/**
 * The "roles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $adminService = new Google\Service\Directory(...);
 *   $roles = $adminService->roles;
 *  </code>
 */
class Roles extends \Google\Service\Resource
{
  /**
   * Deletes a role. (roles.delete)
   *
   * @param string $customer Immutable ID of the Google Workspace account.
   * @param string $roleId Immutable ID of the role.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($customer, $roleId, $optParams = [])
  {
    $params = ['customer' => $customer, 'roleId' => $roleId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Retrieves a role. (roles.get)
   *
   * @param string $customer The unique ID for the customer's Google Workspace
   * account. In case of a multi-domain account, to fetch all groups for a
   * customer, use this field instead of `domain`. You can also use the
   * `my_customer` alias to represent your account's `customerId`. The
   * `customerId` is also returned as part of the [Users](https://developers.googl
   * e.com/workspace/admin/directory/v1/reference/users) resource. You must
   * provide either the `customer` or the `domain` parameter.
   * @param string $roleId Immutable ID of the role.
   * @param array $optParams Optional parameters.
   * @return Role
   * @throws \Google\Service\Exception
   */
  public function get($customer, $roleId, $optParams = [])
  {
    $params = ['customer' => $customer, 'roleId' => $roleId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Role::class);
  }
  /**
   * Creates a role. (roles.insert)
   *
   * @param string $customer Immutable ID of the Google Workspace account.
   * @param Role $postBody
   * @param array $optParams Optional parameters.
   * @return Role
   * @throws \Google\Service\Exception
   */
  public function insert($customer, Role $postBody, $optParams = [])
  {
    $params = ['customer' => $customer, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Role::class);
  }
  /**
   * Retrieves a paginated list of all the roles in a domain. (roles.listRoles)
   *
   * @param string $customer The unique ID for the customer's Google Workspace
   * account. In case of a multi-domain account, to fetch all groups for a
   * customer, use this field instead of `domain`. You can also use the
   * `my_customer` alias to represent your account's `customerId`. The
   * `customerId` is also returned as part of the [Users](https://developers.googl
   * e.com/workspace/admin/directory/v1/reference/users) resource. You must
   * provide either the `customer` or the `domain` parameter.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int maxResults Maximum number of results to return.
   * @opt_param string pageToken Token to specify the next page in the list.
   * @return RolesModel
   * @throws \Google\Service\Exception
   */
  public function listRoles($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], RolesModel::class);
  }
  /**
   * Patches a role. (roles.patch)
   *
   * @param string $customer Immutable ID of the Google Workspace account.
   * @param string $roleId Immutable ID of the role.
   * @param Role $postBody
   * @param array $optParams Optional parameters.
   * @return Role
   * @throws \Google\Service\Exception
   */
  public function patch($customer, $roleId, Role $postBody, $optParams = [])
  {
    $params = ['customer' => $customer, 'roleId' => $roleId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Role::class);
  }
  /**
   * Updates a role. (roles.update)
   *
   * @param string $customer Immutable ID of the Google Workspace account.
   * @param string $roleId Immutable ID of the role.
   * @param Role $postBody
   * @param array $optParams Optional parameters.
   * @return Role
   * @throws \Google\Service\Exception
   */
  public function update($customer, $roleId, Role $postBody, $optParams = [])
  {
    $params = ['customer' => $customer, 'roleId' => $roleId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Role::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Roles::class, 'Google_Service_Directory_Resource_Roles');
