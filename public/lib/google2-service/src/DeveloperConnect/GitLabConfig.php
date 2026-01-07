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

class GitLabConfig extends \Google\Model
{
  protected $authorizerCredentialType = UserCredential::class;
  protected $authorizerCredentialDataType = '';
  protected $readAuthorizerCredentialType = UserCredential::class;
  protected $readAuthorizerCredentialDataType = '';
  /**
   * Required. Immutable. SecretManager resource containing the webhook secret
   * of a GitLab project, formatted as `projects/secrets/versions` or
   * `projects/locations/secrets/versions` (if regional secrets are supported in
   * that location). This is used to validate webhooks.
   *
   * @var string
   */
  public $webhookSecretSecretVersion;

  /**
   * Required. A GitLab personal access token with the minimum `api` scope
   * access and a minimum role of `maintainer`. The GitLab Projects visible to
   * this Personal Access Token will control which Projects Developer Connect
   * has access to.
   *
   * @param UserCredential $authorizerCredential
   */
  public function setAuthorizerCredential(UserCredential $authorizerCredential)
  {
    $this->authorizerCredential = $authorizerCredential;
  }
  /**
   * @return UserCredential
   */
  public function getAuthorizerCredential()
  {
    return $this->authorizerCredential;
  }
  /**
   * Required. A GitLab personal access token with the minimum `read_api` scope
   * access and a minimum role of `reporter`. The GitLab Projects visible to
   * this Personal Access Token will control which Projects Developer Connect
   * has access to.
   *
   * @param UserCredential $readAuthorizerCredential
   */
  public function setReadAuthorizerCredential(UserCredential $readAuthorizerCredential)
  {
    $this->readAuthorizerCredential = $readAuthorizerCredential;
  }
  /**
   * @return UserCredential
   */
  public function getReadAuthorizerCredential()
  {
    return $this->readAuthorizerCredential;
  }
  /**
   * Required. Immutable. SecretManager resource containing the webhook secret
   * of a GitLab project, formatted as `projects/secrets/versions` or
   * `projects/locations/secrets/versions` (if regional secrets are supported in
   * that location). This is used to validate webhooks.
   *
   * @param string $webhookSecretSecretVersion
   */
  public function setWebhookSecretSecretVersion($webhookSecretSecretVersion)
  {
    $this->webhookSecretSecretVersion = $webhookSecretSecretVersion;
  }
  /**
   * @return string
   */
  public function getWebhookSecretSecretVersion()
  {
    return $this->webhookSecretSecretVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GitLabConfig::class, 'Google_Service_DeveloperConnect_GitLabConfig');
