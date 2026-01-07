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

class GenerateIdTokenRequest extends \Google\Collection
{
  protected $collection_key = 'delegates';
  /**
   * Required. The audience for the token, such as the API or account that this
   * token grants access to.
   *
   * @var string
   */
  public $audience;
  /**
   * The sequence of service accounts in a delegation chain. Each service
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
   * Include the service account email in the token. If set to `true`, the token
   * will contain `email` and `email_verified` claims.
   *
   * @var bool
   */
  public $includeEmail;
  /**
   * Include the organization number of the service account in the token. If set
   * to `true`, the token will contain a `google.organization_number` claim. The
   * value of the claim will be `null` if the service account isn't associated
   * with an organization.
   *
   * @var bool
   */
  public $organizationNumberIncluded;

  /**
   * Required. The audience for the token, such as the API or account that this
   * token grants access to.
   *
   * @param string $audience
   */
  public function setAudience($audience)
  {
    $this->audience = $audience;
  }
  /**
   * @return string
   */
  public function getAudience()
  {
    return $this->audience;
  }
  /**
   * The sequence of service accounts in a delegation chain. Each service
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
   * Include the service account email in the token. If set to `true`, the token
   * will contain `email` and `email_verified` claims.
   *
   * @param bool $includeEmail
   */
  public function setIncludeEmail($includeEmail)
  {
    $this->includeEmail = $includeEmail;
  }
  /**
   * @return bool
   */
  public function getIncludeEmail()
  {
    return $this->includeEmail;
  }
  /**
   * Include the organization number of the service account in the token. If set
   * to `true`, the token will contain a `google.organization_number` claim. The
   * value of the claim will be `null` if the service account isn't associated
   * with an organization.
   *
   * @param bool $organizationNumberIncluded
   */
  public function setOrganizationNumberIncluded($organizationNumberIncluded)
  {
    $this->organizationNumberIncluded = $organizationNumberIncluded;
  }
  /**
   * @return bool
   */
  public function getOrganizationNumberIncluded()
  {
    return $this->organizationNumberIncluded;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateIdTokenRequest::class, 'Google_Service_IAMCredentials_GenerateIdTokenRequest');
