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

use Google\Service\Directory\RoleAssignment;
use Google\Service\Directory\RoleAssignments as RoleAssignmentsModel;

/**
 * The "roleAssignments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $adminService = new Google\Service\Directory(...);
 *   $roleAssignments = $adminService->roleAssignments;
 *  </code>
 */
class RoleAssignments extends \Google\Service\Resource
{
  /**
   * Deletes a role assignment. (roleAssignments.delete)
   *
   * @param string $customer Immutable ID of the Google Workspace account.
   * @param string $roleAssignmentId Immutable ID of the role assignment.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($customer, $roleAssignmentId, $optParams = [])
  {
    $params = ['customer' => $customer, 'roleAssignmentId' => $roleAssignmentId];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Retrieves a role assignment. (roleAssignments.get)
   *
   * @param string $customer The unique ID for the customer's Google Workspace
   * account. In case of a multi-domain account, to fetch all groups for a
   * customer, use this field instead of `domain`. You can also use the
   * `my_customer` alias to represent your account's `customerId`. The
   * `customerId` is also returned as part of the [Users](https://developers.googl
   * e.com/workspace/admin/directory/v1/reference/users) resource. You must
   * provide either the `customer` or the `domain` parameter.
   * @param string $roleAssignmentId Immutable ID of the role assignment.
   * @param array $optParams Optional parameters.
   * @return RoleAssignment
   * @throws \Google\Service\Exception
   */
  public function get($customer, $roleAssignmentId, $optParams = [])
  {
    $params = ['customer' => $customer, 'roleAssignmentId' => $roleAssignmentId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], RoleAssignment::class);
  }
  /**
   * Creates a role assignment. (roleAssignments.insert)
   *
   * @param string $customer Immutable ID of the Google Workspace account.
   * @param RoleAssignment $postBody
   * @param array $optParams Optional parameters.
   * @return RoleAssignment
   * @throws \Google\Service\Exception
   */
  public function insert($customer, RoleAssignment $postBody, $optParams = [])
  {
    $params = ['customer' => $customer, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], RoleAssignment::class);
  }
  /**
   * Retrieves a paginated list of all roleAssignments.
   * (roleAssignments.listRoleAssignments)
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
   * @opt_param bool includeIndirectRoleAssignments When set to `true`, fetches
   * indirect role assignments (i.e. role assignment via a group) as well as
   * direct ones. Defaults to `false`. You must specify `user_key` or the indirect
   * role assignments will not be included.
   * @opt_param int maxResults Maximum number of results to return.
   * @opt_param string pageToken Token to specify the next page in the list.
   * @opt_param string roleId Immutable ID of a role. If included in the request,
   * returns only role assignments containing this role ID.
   * @opt_param string userKey The primary email address, alias email address, or
   * unique user or group ID. If included in the request, returns role assignments
   * only for this user or group.
   * @return RoleAssignmentsModel
   * @throws \Google\Service\Exception
   */
  public function listRoleAssignments($customer, $optParams = [])
  {
    $params = ['customer' => $customer];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], RoleAssignmentsModel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RoleAssignments::class, 'Google_Service_Directory_Resource_RoleAssignments');
