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

namespace Google\Service\Firestore\Resource;

use Google\Service\Firestore\FirestoreEmpty;
use Google\Service\Firestore\GoogleFirestoreAdminV1DisableUserCredsRequest;
use Google\Service\Firestore\GoogleFirestoreAdminV1EnableUserCredsRequest;
use Google\Service\Firestore\GoogleFirestoreAdminV1ListUserCredsResponse;
use Google\Service\Firestore\GoogleFirestoreAdminV1ResetUserPasswordRequest;
use Google\Service\Firestore\GoogleFirestoreAdminV1UserCreds;

/**
 * The "userCreds" collection of methods.
 * Typical usage is:
 *  <code>
 *   $firestoreService = new Google\Service\Firestore(...);
 *   $userCreds = $firestoreService->projects_databases_userCreds;
 *  </code>
 */
class ProjectsDatabasesUserCreds extends \Google\Service\Resource
{
  /**
   * Create a user creds. (userCreds.create)
   *
   * @param string $parent Required. A parent name of the form
   * `projects/{project_id}/databases/{database_id}`
   * @param GoogleFirestoreAdminV1UserCreds $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string userCredsId Required. The ID to use for the user creds,
   * which will become the final component of the user creds's resource name. This
   * value should be 4-63 characters. Valid characters are /a-z-/ with first
   * character a letter and the last a letter or a number. Must not be UUID-like
   * /[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}/.
   * @return GoogleFirestoreAdminV1UserCreds
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleFirestoreAdminV1UserCreds $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleFirestoreAdminV1UserCreds::class);
  }
  /**
   * Deletes a user creds. (userCreds.delete)
   *
   * @param string $name Required. A name of the form
   * `projects/{project_id}/databases/{database_id}/userCreds/{user_creds_id}`
   * @param array $optParams Optional parameters.
   * @return FirestoreEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], FirestoreEmpty::class);
  }
  /**
   * Disables a user creds. No-op if the user creds are already disabled.
   * (userCreds.disable)
   *
   * @param string $name Required. A name of the form
   * `projects/{project_id}/databases/{database_id}/userCreds/{user_creds_id}`
   * @param GoogleFirestoreAdminV1DisableUserCredsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1UserCreds
   * @throws \Google\Service\Exception
   */
  public function disable($name, GoogleFirestoreAdminV1DisableUserCredsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], GoogleFirestoreAdminV1UserCreds::class);
  }
  /**
   * Enables a user creds. No-op if the user creds are already enabled.
   * (userCreds.enable)
   *
   * @param string $name Required. A name of the form
   * `projects/{project_id}/databases/{database_id}/userCreds/{user_creds_id}`
   * @param GoogleFirestoreAdminV1EnableUserCredsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1UserCreds
   * @throws \Google\Service\Exception
   */
  public function enable($name, GoogleFirestoreAdminV1EnableUserCredsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enable', [$params], GoogleFirestoreAdminV1UserCreds::class);
  }
  /**
   * Gets a user creds resource. Note that the returned resource does not contain
   * the secret value itself. (userCreds.get)
   *
   * @param string $name Required. A name of the form
   * `projects/{project_id}/databases/{database_id}/userCreds/{user_creds_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1UserCreds
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleFirestoreAdminV1UserCreds::class);
  }
  /**
   * List all user creds in the database. Note that the returned resource does not
   * contain the secret value itself. (userCreds.listProjectsDatabasesUserCreds)
   *
   * @param string $parent Required. A parent database name of the form
   * `projects/{project_id}/databases/{database_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1ListUserCredsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsDatabasesUserCreds($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleFirestoreAdminV1ListUserCredsResponse::class);
  }
  /**
   * Resets the password of a user creds. (userCreds.resetPassword)
   *
   * @param string $name Required. A name of the form
   * `projects/{project_id}/databases/{database_id}/userCreds/{user_creds_id}`
   * @param GoogleFirestoreAdminV1ResetUserPasswordRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleFirestoreAdminV1UserCreds
   * @throws \Google\Service\Exception
   */
  public function resetPassword($name, GoogleFirestoreAdminV1ResetUserPasswordRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resetPassword', [$params], GoogleFirestoreAdminV1UserCreds::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsDatabasesUserCreds::class, 'Google_Service_Firestore_Resource_ProjectsDatabasesUserCreds');
