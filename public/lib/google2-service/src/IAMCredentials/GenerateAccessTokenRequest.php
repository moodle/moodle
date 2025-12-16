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

namespace Google\Service\IAMCredentials;

class GenerateAccessTokenRequest extends \Google\Collection
{
  protected $collection_key = 'scope';
  /**
   * The sequence of service accounts in a delegation chain. This field is
   * required for [delegated
   * requests](https://cloud.google.com/iam/help/credentials/delegated-request).
   * For [direct requests](https://cloud.google.com/iam/help/credentials/direct-
   * request), which are more common, do not specify this field. Each service
   * account must be granted the `roles/iam.serviceAccountTokenCreator` role on
   * its next service account in the chain. The last service account in the
   * chain must be granted the `roles/iam.serviceAccountTokenCreator` role on
   * the service account that is specified in the `name` field of the request.
   * The delegates must have the following format:
   * `projects/-/serviceAccounts/{ACCOUNT_EMAIL_OR_UNIQUEID}`. The `-` wildcard
   * character is required; replacing it with a project ID is invalid.
   *
   * @var string[]
   */
  public $delegates;
  /**
   * The desired lifetime duration of the access token in seconds. By default,
   * the maximum allowed value is 1 hour. To set a lifetime of up to 12 hours,
   * you can add the service account as an allowed value in an Organization
   * Policy that enforces the
   * `constraints/iam.allowServiceAccountCredentialLifetimeExtension`
   * constraint. See detailed instructions at
   * https://cloud.google.com/iam/help/credentials/lifetime If a value is not
   * specified, the token's lifetime will be set to a default value of 1 hour.
   *
   * @var string
   */
  public $lifetime;
  /**
   * Required. Code to identify the scopes to be included in the OAuth 2.0
   * access token. See
   * https://developers.google.com/identity/protocols/googlescopes for more
   * information. At least one value required.
   *
   * @var string[]
   */
  public $scope;

  /**
   * The sequence of service accounts in a delegation chain. This field is
   * required for [delegated
   * requests](https://cloud.google.com/iam/help/credentials/delegated-request).
   * For [direct requests](https://cloud.google.com/iam/help/credentials/direct-
   * request), which are more common, do not specify this field. Each service
   * account must be granted the `roles/iam.serviceAccountTokenCreator` role on
   * its next service account in the chain. The last service account in the
   * chain must be granted the `roles/iam.serviceAccountTokenCreator` role on
   * the service account that is specified in the `name` field of the request.
   * The delegates must have the following format:
   * `projects/-/serviceAccounts/{ACCOUNT_EMAIL_OR_UNIQUEID}`. The `-` wildcard
   * character is required; replacing it with a project ID is invalid.
   *
   * @param string[] $delegates
   */
  public function setDelegates($delegates)
  {
    $this->delegates = $delegates;
  }
  /**
   * @return string[]
   */
  public function getDelegates()
  {
    return $this->delegates;
  }
  /**
   * The desired lifetime duration of the access token in seconds. By default,
   * the maximum allowed value is 1 hour. To set a lifetime of up to 12 hours,
   * you can add the service account as an allowed value in an Organization
   * Policy that enforces the
   * `constraints/iam.allowServiceAccountCredentialLifetimeExtension`
   * constraint. See detailed instructions at
   * https://cloud.google.com/iam/help/credentials/lifetime If a value is not
   * specified, the token's lifetime will be set to a default value of 1 hour.
   *
   * @param string $lifetime
   */
  public function setLifetime($lifetime)
  {
    $this->lifetime = $lifetime;
  }
  /**
   * @return string
   */
  public function getLifetime()
  {
    return $this->lifetime;
  }
  /**
   * Required. Code to identify the scopes to be included in the OAuth 2.0
   * access token. See
   * https://developers.google.com/identity/protocols/googlescopes for more
   * information. At least one value required.
   *
   * @param string[] $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string[]
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateAccessTokenRequest::class, 'Google_Service_IAMCredentials_GenerateAccessTokenRequest');
