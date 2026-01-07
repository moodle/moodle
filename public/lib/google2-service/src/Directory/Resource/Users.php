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

use Google\Service\Directory\Channel;
use Google\Service\Directory\DirectoryUsersCreateGuestRequest;
use Google\Service\Directory\User;
use Google\Service\Directory\UserMakeAdmin;
use Google\Service\Directory\UserUndelete;
use Google\Service\Directory\Users as UsersModel;

/**
 * The "users" collection of methods.
 * Typical usage is:
 *  <code>
 *   $adminService = new Google\Service\Directory(...);
 *   $users = $adminService->users;
 *  </code>
 */
class Users extends \Google\Service\Resource
{
  /**
   * Create a guest user with access to a [subset of Workspace
   * capabilities](https://support.google.com/a/answer/16558545?hl=en). This
   * feature is currently in Alpha. Please reach out to support if you are
   * interested in trying this feature. (users.createGuest)
   *
   * @param DirectoryUsersCreateGuestRequest $postBody
   * @param array $optParams Optional parameters.
   * @return User
   * @throws \Google\Service\Exception
   */
  public function createGuest(DirectoryUsersCreateGuestRequest $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('createGuest', [$params], User::class);
  }
  /**
   * Deletes a user. (users.delete)
   *
   * @param string $userKey Identifies the user in the API request. The value can
   * be the user's primary email address, alias email address, or unique user ID.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($userKey, $optParams = [])
  {
    $params = ['userKey' => $userKey];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Retrieves a user. (users.get)
   *
   * @param string $userKey Identifies the user in the API request. The value can
   * be the user's primary email address, alias email address, or unique user ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customFieldMask A comma-separated list of schema names. All
   * fields from these schemas are fetched. This should only be set when
   * `projection=custom`.
   * @opt_param string projection What subset of fields to fetch for this user.
   * @opt_param string viewType Whether to fetch the administrator-only or domain-
   * wide public view of the user. For more information, see [Retrieve a user as a
   * non-administrator](https://developers.google.com/workspace/admin/directory/v1
   * /guides/manage-users#retrieve_users_non_admin).
   * @return User
   * @throws \Google\Service\Exception
   */
  public function get($userKey, $optParams = [])
  {
    $params = ['userKey' => $userKey];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], User::class);
  }
  /**
   * Creates a user. Mutate calls immediately following user creation might
   * sometimes fail as the user isn't fully created due to propagation delay in
   * our backends. Check the error details for the "User creation is not complete"
   * message to see if this is the case. Retrying the calls after some time can
   * help in this case. If `resolveConflictAccount` is set to `true`, a `202`
   * response code means that a conflicting unmanaged account exists and was
   * invited to join the organization. A `409` response code means that a
   * conflicting account exists so the user wasn't created based on the [handling
   * unmanaged user accounts](https://support.google.com/a/answer/11112794) option
   * selected. (users.insert)
   *
   * @param User $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool resolveConflictAccount Optional. If set to `true`, the option
   * selected for [handling unmanaged user
   * accounts](https://support.google.com/a/answer/11112794) will apply. Default:
   * `false`
   * @return User
   * @throws \Google\Service\Exception
   */
  public function insert(User $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], User::class);
  }
  /**
   * Retrieves a paginated list of either deleted users or all users in a domain.
   * (users.listUsers)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customFieldMask A comma-separated list of schema names. All
   * fields from these schemas are fetched. This should only be set when
   * `projection=custom`.
   * @opt_param string customer The unique ID for the customer's Google Workspace
   * account. In case of a multi-domain account, to fetch all users for a
   * customer, use this field instead of `domain`. You can also use the
   * `my_customer` alias to represent your account's `customerId`. The
   * `customerId` is also returned as part of the [Users](https://developers.googl
   * e.com/workspace/admin/directory/v1/reference/users) resource. You must
   * provide either the `customer` or the `domain` parameter.
   * @opt_param string domain The domain name. Use this field to get users from
   * only one domain. To return all domains for a customer account, use the
   * `customer` query parameter instead. Either the `customer` or the `domain`
   * parameter must be provided.
   * @opt_param string event Event on which subscription is intended (if
   * subscribing)
   * @opt_param int maxResults Maximum number of results to return.
   * @opt_param string orderBy Property to use for sorting results.
   * @opt_param string pageToken Token to specify next page in the list. The page
   * token is only valid for three days.
   * @opt_param string projection What subset of fields to fetch for this user.
   * @opt_param string query Query string for searching user fields. For more
   * information on constructing user queries, see [Search for Users](https://deve
   * lopers.google.com/workspace/admin/directory/v1/guides/search-users).
   * @opt_param string showDeleted If set to `true`, retrieves the list of deleted
   * users. (Default: `false`)
   * @opt_param string sortOrder Whether to return results in ascending or
   * descending order, ignoring case.
   * @opt_param string viewType Whether to fetch the administrator-only or domain-
   * wide public view of the user. For more information, see [Retrieve a user as a
   * non-administrator](https://developers.google.com/workspace/admin/directory/v1
   * /guides/manage-users#retrieve_users_non_admin).
   * @return UsersModel
   * @throws \Google\Service\Exception
   */
  public function listUsers($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], UsersModel::class);
  }
  /**
   * Makes a user a super administrator. (users.makeAdmin)
   *
   * @param string $userKey Identifies the user in the API request. The value can
   * be the user's primary email address, alias email address, or unique user ID.
   * @param UserMakeAdmin $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function makeAdmin($userKey, UserMakeAdmin $postBody, $optParams = [])
  {
    $params = ['userKey' => $userKey, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('makeAdmin', [$params]);
  }
  /**
   * Updates a user using patch semantics. The update method should be used
   * instead, because it also supports patch semantics and has better performance.
   * If you're mapping an external identity to a Google identity, use the [`update
   * `](https://developers.google.com/workspace/admin/directory/v1/reference/users
   * /update) method instead of the `patch` method. This method is unable to clear
   * fields that contain repeated objects (`addresses`, `phones`, etc). Use the
   * update method instead. (users.patch)
   *
   * @param string $userKey Identifies the user in the API request. The value can
   * be the user's primary email address, alias email address, or unique user ID.
   * @param User $postBody
   * @param array $optParams Optional parameters.
   * @return User
   * @throws \Google\Service\Exception
   */
  public function patch($userKey, User $postBody, $optParams = [])
  {
    $params = ['userKey' => $userKey, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], User::class);
  }
  /**
   * Signs a user out of all web and device sessions and reset their sign-in
   * cookies. User will have to sign in by authenticating again. (users.signOut)
   *
   * @param string $userKey Identifies the target user in the API request. The
   * value can be the user's primary email address, alias email address, or unique
   * user ID.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function signOut($userKey, $optParams = [])
  {
    $params = ['userKey' => $userKey];
    $params = array_merge($params, $optParams);
    return $this->call('signOut', [$params]);
  }
  /**
   * Undeletes a deleted user. (users.undelete)
   *
   * @param string $userKey The immutable id of the user
   * @param UserUndelete $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function undelete($userKey, UserUndelete $postBody, $optParams = [])
  {
    $params = ['userKey' => $userKey, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('undelete', [$params]);
  }
  /**
   * Updates a user. This method supports patch semantics, meaning that you only
   * need to include the fields you wish to update. Fields that are not present in
   * the request will be preserved, and fields set to `null` will be cleared. For
   * repeating fields that contain arrays, individual items in the array can't be
   * patched piecemeal; they must be supplied in the request body with the desired
   * values for all items. See the [user accounts guide](https://developers.google
   * .com/workspace/admin/directory/v1/guides/manage-users#update_user) for more
   * information. (users.update)
   *
   * @param string $userKey Identifies the user in the API request. The value can
   * be the user's primary email address, alias email address, or unique user ID.
   * @param User $postBody
   * @param array $optParams Optional parameters.
   * @return User
   * @throws \Google\Service\Exception
   */
  public function update($userKey, User $postBody, $optParams = [])
  {
    $params = ['userKey' => $userKey, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], User::class);
  }
  /**
   * Watches for changes in users list. (users.watch)
   *
   * @param Channel $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customFieldMask Comma-separated list of schema names. All
   * fields from these schemas are fetched. This should only be set when
   * projection=custom.
   * @opt_param string customer Immutable ID of the Google Workspace account. In
   * case of multi-domain, to fetch all users for a customer, fill this field
   * instead of domain.
   * @opt_param string domain Name of the domain. Fill this field to get users
   * from only this domain. To return all users in a multi-domain fill customer
   * field instead."
   * @opt_param string event Events to watch for.
   * @opt_param int maxResults Maximum number of results to return.
   * @opt_param string orderBy Column to use for sorting results
   * @opt_param string pageToken Token to specify next page in the list
   * @opt_param string projection What subset of fields to fetch for this user.
   * @opt_param string query Query string search. Contains one or more search
   * clauses, each with a field, operator, and value. For complete documentation,
   * go to [Search for users](https://developers.google.com/workspace/admin/direct
   * ory/v1/guides/search-users).
   * @opt_param string showDeleted If set to true, retrieves the list of deleted
   * users. (Default: false)
   * @opt_param string sortOrder Whether to return results in ascending or
   * descending order.
   * @opt_param string viewType Whether to fetch the administrator-only or domain-
   * wide public view of the user. For more information, see [Retrieve a user as a
   * non-administrator](https://developers.google.com/workspace/admin/directory/v1
   * /guides/manage-users#retrieve_users_non_admin).
   * @return Channel
   * @throws \Google\Service\Exception
   */
  public function watch(Channel $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('watch', [$params], Channel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Users::class, 'Google_Service_Directory_Resource_Users');
