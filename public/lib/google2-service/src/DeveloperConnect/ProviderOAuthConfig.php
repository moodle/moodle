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

namespace Google\Service\DeveloperConnect;

class ProviderOAuthConfig extends \Google\Collection
{
  /**
   * No system provider specified.
   */
  public const SYSTEM_PROVIDER_ID_SYSTEM_PROVIDER_UNSPECIFIED = 'SYSTEM_PROVIDER_UNSPECIFIED';
  /**
   * GitHub provider. Scopes can be found at
   * https://docs.github.com/en/apps/oauth-apps/building-oauth-apps/scopes-for-
   * oauth-apps#available-scopes
   */
  public const SYSTEM_PROVIDER_ID_GITHUB = 'GITHUB';
  /**
   * GitLab provider. Scopes can be found at
   * https://docs.gitlab.com/user/profile/personal_access_tokens/#personal-
   * access-token-scopes
   */
  public const SYSTEM_PROVIDER_ID_GITLAB = 'GITLAB';
  /**
   * Google provider. Recommended scopes:
   * "https://www.googleapis.com/auth/drive.readonly",
   * "https://www.googleapis.com/auth/documents.readonly"
   */
  public const SYSTEM_PROVIDER_ID_GOOGLE = 'GOOGLE';
  /**
   * Sentry provider. Scopes can be found at
   * https://docs.sentry.io/api/permissions/
   */
  public const SYSTEM_PROVIDER_ID_SENTRY = 'SENTRY';
  /**
   * Rovo provider. Must select the "rovo" scope.
   */
  public const SYSTEM_PROVIDER_ID_ROVO = 'ROVO';
  /**
   * New Relic provider. No scopes are allowed.
   */
  public const SYSTEM_PROVIDER_ID_NEW_RELIC = 'NEW_RELIC';
  /**
   * Datastax provider. No scopes are allowed.
   */
  public const SYSTEM_PROVIDER_ID_DATASTAX = 'DATASTAX';
  /**
   * Dynatrace provider.
   */
  public const SYSTEM_PROVIDER_ID_DYNATRACE = 'DYNATRACE';
  protected $collection_key = 'scopes';
  /**
   * Required. User selected scopes to apply to the Oauth config In the event of
   * changing scopes, user records under AccountConnector will be deleted and
   * users will re-auth again.
   *
   * @var string[]
   */
  public $scopes;
  /**
   * Immutable. Developer Connect provided OAuth.
   *
   * @var string
   */
  public $systemProviderId;

  /**
   * Required. User selected scopes to apply to the Oauth config In the event of
   * changing scopes, user records under AccountConnector will be deleted and
   * users will re-auth again.
   *
   * @param string[] $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string[]
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * Immutable. Developer Connect provided OAuth.
   *
   * Accepted values: SYSTEM_PROVIDER_UNSPECIFIED, GITHUB, GITLAB, GOOGLE,
   * SENTRY, ROVO, NEW_RELIC, DATASTAX, DYNATRACE
   *
   * @param self::SYSTEM_PROVIDER_ID_* $systemProviderId
   */
  public function setSystemProviderId($systemProviderId)
  {
    $this->systemProviderId = $systemProviderId;
  }
  /**
   * @return self::SYSTEM_PROVIDER_ID_*
   */
  public function getSystemProviderId()
  {
    return $this->systemProviderId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProviderOAuthConfig::class, 'Google_Service_DeveloperConnect_ProviderOAuthConfig');
