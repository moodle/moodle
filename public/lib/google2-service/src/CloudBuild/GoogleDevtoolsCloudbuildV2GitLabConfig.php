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

class GoogleDevtoolsCloudbuildV2GitLabConfig extends \Google\Model
{
  protected $authorizerCredentialType = UserCredential::class;
  protected $authorizerCredentialDataType = '';
  /**
   * Optional. The URI of the GitLab Enterprise host this connection is for. If
   * not specified, the default value is https://gitlab.com.
   *
   * @var string
   */
  public $hostUri;
  protected $readAuthorizerCredentialType = UserCredential::class;
  protected $readAuthorizerCredentialDataType = '';
  /**
   * Output only. Version of the GitLab Enterprise server running on the
   * `host_uri`.
   *
   * @var string
   */
  public $serverVersion;
  protected $serviceDirectoryConfigType = GoogleDevtoolsCloudbuildV2ServiceDirectoryConfig::class;
  protected $serviceDirectoryConfigDataType = '';
  /**
   * Optional. SSL certificate to use for requests to GitLab Enterprise.
   *
   * @var string
   */
  public $sslCa;
  /**
   * Required. Immutable. SecretManager resource containing the webhook secret
   * of a GitLab Enterprise project, formatted as `projects/secrets/versions`.
   *
   * @var string
   */
  public $webhookSecretSecretVersion;

  /**
   * Required. A GitLab personal access token with the `api` scope access.
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
   * Optional. The URI of the GitLab Enterprise host this connection is for. If
   * not specified, the default value is https://gitlab.com.
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
   * Required. A GitLab personal access token with the minimum `read_api` scope
   * access.
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
   * Output only. Version of the GitLab Enterprise server running on the
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
   * a GitLab Enterprise server. This should only be set if the GitLab
   * Enterprise server is hosted on-premises and not reachable by public
   * internet. If this field is left empty, calls to the GitLab Enterprise
   * server will be made over the public internet.
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
   * Optional. SSL certificate to use for requests to GitLab Enterprise.
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
   * of a GitLab Enterprise project, formatted as `projects/secrets/versions`.
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
class_alias(GoogleDevtoolsCloudbuildV2GitLabConfig::class, 'Google_Service_CloudBuild_GoogleDevtoolsCloudbuildV2GitLabConfig');
