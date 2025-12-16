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

class BitbucketCloudConfig extends \Google\Model
{
  protected $authorizerCredentialType = UserCredential::class;
  protected $authorizerCredentialDataType = '';
  protected $readAuthorizerCredentialType = UserCredential::class;
  protected $readAuthorizerCredentialDataType = '';
  /**
   * Required. Immutable. SecretManager resource containing the webhook secret
   * used to verify webhook events, formatted as `projects/secrets/versions` or
   * `projects/locations/secrets/versions` (if regional secrets are supported in
   * that location). This is used to validate and create webhooks.
   *
   * @var string
   */
  public $webhookSecretSecretVersion;
  /**
   * Required. The Bitbucket Cloud Workspace ID to be connected to Google Cloud
   * Platform.
   *
   * @var string
   */
  public $workspace;

  /**
   * Required. An access token with the minimum `repository`, `pullrequest` and
   * `webhook` scope access. It can either be a workspace, project or repository
   * access token. This is needed to create webhooks. It's recommended to use a
   * system account to generate these credentials.
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
   * Required. An access token with the minimum `repository` access. It can
   * either be a workspace, project or repository access token. It's recommended
   * to use a system account to generate the credentials.
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
   * used to verify webhook events, formatted as `projects/secrets/versions` or
   * `projects/locations/secrets/versions` (if regional secrets are supported in
   * that location). This is used to validate and create webhooks.
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
  /**
   * Required. The Bitbucket Cloud Workspace ID to be connected to Google Cloud
   * Platform.
   *
   * @param string $workspace
   */
  public function setWorkspace($workspace)
  {
    $this->workspace = $workspace;
  }
  /**
   * @return string
   */
  public function getWorkspace()
  {
    return $this->workspace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BitbucketCloudConfig::class, 'Google_Service_DeveloperConnect_BitbucketCloudConfig');
