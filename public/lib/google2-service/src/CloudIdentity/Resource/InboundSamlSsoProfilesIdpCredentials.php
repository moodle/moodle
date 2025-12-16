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

namespace Google\Service\CloudIdentity\Resource;

use Google\Service\CloudIdentity\AddIdpCredentialRequest;
use Google\Service\CloudIdentity\IdpCredential;
use Google\Service\CloudIdentity\ListIdpCredentialsResponse;
use Google\Service\CloudIdentity\Operation;

/**
 * The "idpCredentials" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudidentityService = new Google\Service\CloudIdentity(...);
 *   $idpCredentials = $cloudidentityService->inboundSamlSsoProfiles_idpCredentials;
 *  </code>
 */
class InboundSamlSsoProfilesIdpCredentials extends \Google\Service\Resource
{
  /**
   * Adds an IdpCredential. Up to 2 credentials are allowed. When the target
   * customer has enabled [Multi-party approval for sensitive
   * actions](https://support.google.com/a/answer/13790448), the `Operation` in
   * the response will have `"done": false`, it will not have a response, and the
   * metadata will have `"state": "awaiting-multi-party-approval"`.
   * (idpCredentials.add)
   *
   * @param string $parent Required. The InboundSamlSsoProfile that owns the
   * IdpCredential. Format: `inboundSamlSsoProfiles/{sso_profile_id}`
   * @param AddIdpCredentialRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function add($parent, AddIdpCredentialRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('add', [$params], Operation::class);
  }
  /**
   * Deletes an IdpCredential. (idpCredentials.delete)
   *
   * @param string $name Required. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * IdpCredential to delete. Format:
   * `inboundSamlSsoProfiles/{sso_profile_id}/idpCredentials/{idp_credential_id}`
   * @param array $optParams Optional parameters.
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
   * Gets an IdpCredential. (idpCredentials.get)
   *
   * @param string $name Required. The [resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * IdpCredential to retrieve. Format:
   * `inboundSamlSsoProfiles/{sso_profile_id}/idpCredentials/{idp_credential_id}`
   * @param array $optParams Optional parameters.
   * @return IdpCredential
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], IdpCredential::class);
  }
  /**
   * Returns a list of IdpCredentials in an InboundSamlSsoProfile.
   * (idpCredentials.listInboundSamlSsoProfilesIdpCredentials)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * `IdpCredential`s. Format: `inboundSamlSsoProfiles/{sso_profile_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of `IdpCredential`s to return. The
   * service may return fewer than this value.
   * @opt_param string pageToken A page token, received from a previous
   * `ListIdpCredentials` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListIdpCredentials` must match
   * the call that provided the page token.
   * @return ListIdpCredentialsResponse
   * @throws \Google\Service\Exception
   */
  public function listInboundSamlSsoProfilesIdpCredentials($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListIdpCredentialsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InboundSamlSsoProfilesIdpCredentials::class, 'Google_Service_CloudIdentity_Resource_InboundSamlSsoProfilesIdpCredentials');
