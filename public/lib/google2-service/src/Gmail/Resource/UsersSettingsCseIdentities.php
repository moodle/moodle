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

namespace Google\Service\Gmail\Resource;

use Google\Service\Gmail\CseIdentity;
use Google\Service\Gmail\ListCseIdentitiesResponse;

/**
 * The "identities" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gmailService = new Google\Service\Gmail(...);
 *   $identities = $gmailService->users_settings_cse_identities;
 *  </code>
 */
class UsersSettingsCseIdentities extends \Google\Service\Resource
{
  /**
   * Creates and configures a client-side encryption identity that's authorized to
   * send mail from the user account. Google publishes the S/MIME certificate to a
   * shared domain-wide directory so that people within a Google Workspace
   * organization can encrypt and send mail to the identity. For administrators
   * managing identities and keypairs for users in their organization, requests
   * require authorization with a [service account](https://developers.google.com/
   * identity/protocols/OAuth2ServiceAccount) that has [domain-wide delegation aut
   * hority](https://developers.google.com/identity/protocols/OAuth2ServiceAccount
   * #delegatingauthority) to impersonate users with the
   * `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (identities.create)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param CseIdentity $postBody
   * @param array $optParams Optional parameters.
   * @return CseIdentity
   * @throws \Google\Service\Exception
   */
  public function create($userId, CseIdentity $postBody, $optParams = [])
  {
    $params = ['userId' => $userId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CseIdentity::class);
  }
  /**
   * Deletes a client-side encryption identity. The authenticated user can no
   * longer use the identity to send encrypted messages. You cannot restore the
   * identity after you delete it. Instead, use the CreateCseIdentity method to
   * create another identity with the same configuration. For administrators
   * managing identities and keypairs for users in their organization, requests
   * require authorization with a [service account](https://developers.google.com/
   * identity/protocols/OAuth2ServiceAccount) that has [domain-wide delegation aut
   * hority](https://developers.google.com/identity/protocols/OAuth2ServiceAccount
   * #delegatingauthority) to impersonate users with the
   * `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (identities.delete)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param string $cseEmailAddress The primary email address associated with the
   * client-side encryption identity configuration that's removed.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function delete($userId, $cseEmailAddress, $optParams = [])
  {
    $params = ['userId' => $userId, 'cseEmailAddress' => $cseEmailAddress];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params]);
  }
  /**
   * Retrieves a client-side encryption identity configuration. For administrators
   * managing identities and keypairs for users in their organization, requests
   * require authorization with a [service account](https://developers.google.com/
   * identity/protocols/OAuth2ServiceAccount) that has [domain-wide delegation aut
   * hority](https://developers.google.com/identity/protocols/OAuth2ServiceAccount
   * #delegatingauthority) to impersonate users with the
   * `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (identities.get)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param string $cseEmailAddress The primary email address associated with the
   * client-side encryption identity configuration that's retrieved.
   * @param array $optParams Optional parameters.
   * @return CseIdentity
   * @throws \Google\Service\Exception
   */
  public function get($userId, $cseEmailAddress, $optParams = [])
  {
    $params = ['userId' => $userId, 'cseEmailAddress' => $cseEmailAddress];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CseIdentity::class);
  }
  /**
   * Lists the client-side encrypted identities for an authenticated user. For
   * administrators managing identities and keypairs for users in their
   * organization, requests require authorization with a [service account](https:/
   * /developers.google.com/identity/protocols/OAuth2ServiceAccount) that has
   * [domain-wide delegation authority](https://developers.google.com/identity/pro
   * tocols/OAuth2ServiceAccount#delegatingauthority) to impersonate users with
   * the `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (identities.listUsersSettingsCseIdentities)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The number of identities to return. If not provided,
   * the page size will default to 20 entries.
   * @opt_param string pageToken Pagination token indicating which page of
   * identities to return. If the token is not supplied, then the API will return
   * the first page of results.
   * @return ListCseIdentitiesResponse
   * @throws \Google\Service\Exception
   */
  public function listUsersSettingsCseIdentities($userId, $optParams = [])
  {
    $params = ['userId' => $userId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCseIdentitiesResponse::class);
  }
  /**
   * Associates a different key pair with an existing client-side encryption
   * identity. The updated key pair must validate against Google's [S/MIME
   * certificate profiles](https://support.google.com/a/answer/7300887). For
   * administrators managing identities and keypairs for users in their
   * organization, requests require authorization with a [service account](https:/
   * /developers.google.com/identity/protocols/OAuth2ServiceAccount) that has
   * [domain-wide delegation authority](https://developers.google.com/identity/pro
   * tocols/OAuth2ServiceAccount#delegatingauthority) to impersonate users with
   * the `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (identities.patch)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param string $emailAddress The email address of the client-side encryption
   * identity to update.
   * @param CseIdentity $postBody
   * @param array $optParams Optional parameters.
   * @return CseIdentity
   * @throws \Google\Service\Exception
   */
  public function patch($userId, $emailAddress, CseIdentity $postBody, $optParams = [])
  {
    $params = ['userId' => $userId, 'emailAddress' => $emailAddress, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], CseIdentity::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsersSettingsCseIdentities::class, 'Google_Service_Gmail_Resource_UsersSettingsCseIdentities');
