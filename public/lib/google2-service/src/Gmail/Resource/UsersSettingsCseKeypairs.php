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

use Google\Service\Gmail\CseKeyPair;
use Google\Service\Gmail\DisableCseKeyPairRequest;
use Google\Service\Gmail\EnableCseKeyPairRequest;
use Google\Service\Gmail\ListCseKeyPairsResponse;
use Google\Service\Gmail\ObliterateCseKeyPairRequest;

/**
 * The "keypairs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $gmailService = new Google\Service\Gmail(...);
 *   $keypairs = $gmailService->users_settings_cse_keypairs;
 *  </code>
 */
class UsersSettingsCseKeypairs extends \Google\Service\Resource
{
  /**
   * Creates and uploads a client-side encryption S/MIME public key certificate
   * chain and private key metadata for the authenticated user. For administrators
   * managing identities and keypairs for users in their organization, requests
   * require authorization with a [service account](https://developers.google.com/
   * identity/protocols/OAuth2ServiceAccount) that has [domain-wide delegation aut
   * hority](https://developers.google.com/identity/protocols/OAuth2ServiceAccount
   * #delegatingauthority) to impersonate users with the
   * `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (keypairs.create)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param CseKeyPair $postBody
   * @param array $optParams Optional parameters.
   * @return CseKeyPair
   * @throws \Google\Service\Exception
   */
  public function create($userId, CseKeyPair $postBody, $optParams = [])
  {
    $params = ['userId' => $userId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], CseKeyPair::class);
  }
  /**
   * Turns off a client-side encryption key pair. The authenticated user can no
   * longer use the key pair to decrypt incoming CSE message texts or sign
   * outgoing CSE mail. To regain access, use the EnableCseKeyPair to turn on the
   * key pair. After 30 days, you can permanently delete the key pair by using the
   * ObliterateCseKeyPair method. For administrators managing identities and
   * keypairs for users in their organization, requests require authorization with
   * a [service account](https://developers.google.com/identity/protocols/OAuth2Se
   * rviceAccount) that has [domain-wide delegation authority](https://developers.
   * google.com/identity/protocols/OAuth2ServiceAccount#delegatingauthority) to
   * impersonate users with the
   * `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (keypairs.disable)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param string $keyPairId The identifier of the key pair to turn off.
   * @param DisableCseKeyPairRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CseKeyPair
   * @throws \Google\Service\Exception
   */
  public function disable($userId, $keyPairId, DisableCseKeyPairRequest $postBody, $optParams = [])
  {
    $params = ['userId' => $userId, 'keyPairId' => $keyPairId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disable', [$params], CseKeyPair::class);
  }
  /**
   * Turns on a client-side encryption key pair that was turned off. The key pair
   * becomes active again for any associated client-side encryption identities.
   * For administrators managing identities and keypairs for users in their
   * organization, requests require authorization with a [service account](https:/
   * /developers.google.com/identity/protocols/OAuth2ServiceAccount) that has
   * [domain-wide delegation authority](https://developers.google.com/identity/pro
   * tocols/OAuth2ServiceAccount#delegatingauthority) to impersonate users with
   * the `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (keypairs.enable)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param string $keyPairId The identifier of the key pair to turn on.
   * @param EnableCseKeyPairRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CseKeyPair
   * @throws \Google\Service\Exception
   */
  public function enable($userId, $keyPairId, EnableCseKeyPairRequest $postBody, $optParams = [])
  {
    $params = ['userId' => $userId, 'keyPairId' => $keyPairId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enable', [$params], CseKeyPair::class);
  }
  /**
   * Retrieves an existing client-side encryption key pair. For administrators
   * managing identities and keypairs for users in their organization, requests
   * require authorization with a [service account](https://developers.google.com/
   * identity/protocols/OAuth2ServiceAccount) that has [domain-wide delegation aut
   * hority](https://developers.google.com/identity/protocols/OAuth2ServiceAccount
   * #delegatingauthority) to impersonate users with the
   * `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (keypairs.get)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param string $keyPairId The identifier of the key pair to retrieve.
   * @param array $optParams Optional parameters.
   * @return CseKeyPair
   * @throws \Google\Service\Exception
   */
  public function get($userId, $keyPairId, $optParams = [])
  {
    $params = ['userId' => $userId, 'keyPairId' => $keyPairId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CseKeyPair::class);
  }
  /**
   * Lists client-side encryption key pairs for an authenticated user. For
   * administrators managing identities and keypairs for users in their
   * organization, requests require authorization with a [service account](https:/
   * /developers.google.com/identity/protocols/OAuth2ServiceAccount) that has
   * [domain-wide delegation authority](https://developers.google.com/identity/pro
   * tocols/OAuth2ServiceAccount#delegatingauthority) to impersonate users with
   * the `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (keypairs.listUsersSettingsCseKeypairs)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The number of key pairs to return. If not provided,
   * the page size will default to 20 entries.
   * @opt_param string pageToken Pagination token indicating which page of key
   * pairs to return. If the token is not supplied, then the API will return the
   * first page of results.
   * @return ListCseKeyPairsResponse
   * @throws \Google\Service\Exception
   */
  public function listUsersSettingsCseKeypairs($userId, $optParams = [])
  {
    $params = ['userId' => $userId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCseKeyPairsResponse::class);
  }
  /**
   * Deletes a client-side encryption key pair permanently and immediately. You
   * can only permanently delete key pairs that have been turned off for more than
   * 30 days. To turn off a key pair, use the DisableCseKeyPair method. Gmail
   * can't restore or decrypt any messages that were encrypted by an obliterated
   * key. Authenticated users and Google Workspace administrators lose access to
   * reading the encrypted messages. For administrators managing identities and
   * keypairs for users in their organization, requests require authorization with
   * a [service account](https://developers.google.com/identity/protocols/OAuth2Se
   * rviceAccount) that has [domain-wide delegation authority](https://developers.
   * google.com/identity/protocols/OAuth2ServiceAccount#delegatingauthority) to
   * impersonate users with the
   * `https://www.googleapis.com/auth/gmail.settings.basic` scope. For users
   * managing their own identities and keypairs, requests require [hardware key
   * encryption](https://support.google.com/a/answer/14153163) turned on and
   * configured. (keypairs.obliterate)
   *
   * @param string $userId The requester's primary email address. To indicate the
   * authenticated user, you can use the special value `me`.
   * @param string $keyPairId The identifier of the key pair to obliterate.
   * @param ObliterateCseKeyPairRequest $postBody
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function obliterate($userId, $keyPairId, ObliterateCseKeyPairRequest $postBody, $optParams = [])
  {
    $params = ['userId' => $userId, 'keyPairId' => $keyPairId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('obliterate', [$params]);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsersSettingsCseKeypairs::class, 'Google_Service_Gmail_Resource_UsersSettingsCseKeypairs');
