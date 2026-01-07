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

namespace Google\Service\CloudBuild;

class BitbucketDataCenterConfig extends \Google\Model
{
  protected $authorizerCredentialType = UserCredential::class;
  protected $authorizerCredentialDataType = '';
  /**
   * Required. The URI of the Bitbucket Data Center instance or cluster this
   * connection is for.
   *
   * @var string
   */
  public $hostUri;
  protected $readAuthorizerCredentialType = UserCredential::class;
  protected $readAuthorizerCredentialDataType = '';
  /**
   * Output only. Version of the Bitbucket Data Center running on the
   * `host_uri`.
   *
   * @var string
   */
  public $serverVersion;
  protected $serviceDirectoryConfigType = GoogleDevtoolsCloudbuildV2ServiceDirectoryConfig::class;
  protected $serviceDirectoryConfigDataType = '';
  /**
   * Optional. SSL certificate to use for requests to the Bitbucket Data Center.
   *
   * @var string
   */
  public $sslCa;
  /**
   * Required. Immutable. SecretManager resource containing the webhook secret
   * used to verify webhook events, formatted as `projects/secrets/versions`.
   *
   * @var string
   */
  public $webhookSecretSecretVersion;

  /**
   * Required. A http access token with the `REPO_ADMIN` scope access.
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
   * Required. The URI of the Bitbucket Data Center instance or cluster this
   * connection is for.
   *
   * @param string $hostUri
   */
  public function setHostUri($hostUri)
  {
    $this->hostUri = $hostUri;
  }
  /**
   * @return string
   */
  public function getHostUri()
  {
    return $this->hostUri;
  }
  /**
   * Required. A http access token with the `REPO_READ` access.
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
   * Output only. Version of the Bitbucket Data Center running on the
   * `host_uri`.
   *
   * @param string $serverVersion
   */
  public function setServerVersion($serverVersion)
  {
    $this->serverVersion = $serverVersion;
  }
  /**
   * @return string
   */
  public function getServerVersion()
  {
    return $this->serverVersion;
  }
  /**
   * Optional. Configuration for using Service Directory to privately connect to
   * a Bitbucket Data Center. This should only be set if the Bitbucket Data
   * Center is hosted on-premises and not reachable by public internet. If this
   * field is left empty, calls to the Bitbucket Data Center will be made over
   * the public internet.
   *
   * @param GoogleDevtoolsCloudbuildV2ServiceDirectoryConfig $serviceDirectoryConfig
   */
  public function setServiceDirectoryConfig(GoogleDevtoolsCloudbuildV2ServiceDirectoryConfig $serviceDirectoryConfig)
  {
    $this->serviceDirectoryConfig = $serviceDirectoryConfig;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV2ServiceDirectoryConfig
   */
  public function getServiceDirectoryConfig()
  {
    return $this->serviceDirectoryConfig;
  }
  /**
   * Optional. SSL certificate to use for requests to the Bitbucket Data Center.
   *
   * @param string $sslCa
   */
  public function setSslCa($sslCa)
  {
    $this->sslCa = $sslCa;
  }
  /**
   * @return string
   */
  public function getSslCa()
  {
    return $this->sslCa;
  }
  /**
   * Required. Immutable. SecretManager resource containing the webhook secret
   * used to verify webhook events, formatted as `projects/secrets/versions`.
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
class_alias(BitbucketDataCenterConfig::class, 'Google_Service_CloudBuild_BitbucketDataCenterConfig');
