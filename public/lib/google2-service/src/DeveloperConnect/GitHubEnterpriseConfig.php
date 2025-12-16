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

class GitHubEnterpriseConfig extends \Google\Model
{
  /**
   * Optional. ID of the GitHub App created from the manifest.
   *
   * @var string
   */
  public $appId;
  /**
   * Optional. ID of the installation of the GitHub App.
   *
   * @var string
   */
  public $appInstallationId;
  /**
   * Output only. The URL-friendly name of the GitHub App.
   *
   * @var string
   */
  public $appSlug;
  /**
   * Required. The URI of the GitHub Enterprise host this connection is for.
   *
   * @var string
   */
  public $hostUri;
  /**
   * Output only. The URI to navigate to in order to manage the installation
   * associated with this GitHubEnterpriseConfig.
   *
   * @var string
   */
  public $installationUri;
  /**
   * Optional. SecretManager resource containing the private key of the GitHub
   * App, formatted as `projects/secrets/versions` or
   * `projects/locations/secrets/versions` (if regional secrets are supported in
   * that location).
   *
   * @var string
   */
  public $privateKeySecretVersion;
  /**
   * Output only. GitHub Enterprise version installed at the host_uri.
   *
   * @var string
   */
  public $serverVersion;
  protected $serviceDirectoryConfigType = ServiceDirectoryConfig::class;
  protected $serviceDirectoryConfigDataType = '';
  /**
   * Optional. SSL certificate to use for requests to GitHub Enterprise.
   *
   * @var string
   */
  public $sslCaCertificate;
  /**
   * Optional. SecretManager resource containing the webhook secret of the
   * GitHub App, formatted as `projects/secrets/versions` or
   * `projects/locations/secrets/versions` (if regional secrets are supported in
   * that location).
   *
   * @var string
   */
  public $webhookSecretSecretVersion;

  /**
   * Optional. ID of the GitHub App created from the manifest.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * Optional. ID of the installation of the GitHub App.
   *
   * @param string $appInstallationId
   */
  public function setAppInstallationId($appInstallationId)
  {
    $this->appInstallationId = $appInstallationId;
  }
  /**
   * @return string
   */
  public function getAppInstallationId()
  {
    return $this->appInstallationId;
  }
  /**
   * Output only. The URL-friendly name of the GitHub App.
   *
   * @param string $appSlug
   */
  public function setAppSlug($appSlug)
  {
    $this->appSlug = $appSlug;
  }
  /**
   * @return string
   */
  public function getAppSlug()
  {
    return $this->appSlug;
  }
  /**
   * Required. The URI of the GitHub Enterprise host this connection is for.
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
   * Output only. The URI to navigate to in order to manage the installation
   * associated with this GitHubEnterpriseConfig.
   *
   * @param string $installationUri
   */
  public function setInstallationUri($installationUri)
  {
    $this->installationUri = $installationUri;
  }
  /**
   * @return string
   */
  public function getInstallationUri()
  {
    return $this->installationUri;
  }
  /**
   * Optional. SecretManager resource containing the private key of the GitHub
   * App, formatted as `projects/secrets/versions` or
   * `projects/locations/secrets/versions` (if regional secrets are supported in
   * that location).
   *
   * @param string $privateKeySecretVersion
   */
  public function setPrivateKeySecretVersion($privateKeySecretVersion)
  {
    $this->privateKeySecretVersion = $privateKeySecretVersion;
  }
  /**
   * @return string
   */
  public function getPrivateKeySecretVersion()
  {
    return $this->privateKeySecretVersion;
  }
  /**
   * Output only. GitHub Enterprise version installed at the host_uri.
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
   * a GitHub Enterprise server. This should only be set if the GitHub
   * Enterprise server is hosted on-premises and not reachable by public
   * internet. If this field is left empty, calls to the GitHub Enterprise
   * server will be made over the public internet.
   *
   * @param ServiceDirectoryConfig $serviceDirectoryConfig
   */
  public function setServiceDirectoryConfig(ServiceDirectoryConfig $serviceDirectoryConfig)
  {
    $this->serviceDirectoryConfig = $serviceDirectoryConfig;
  }
  /**
   * @return ServiceDirectoryConfig
   */
  public function getServiceDirectoryConfig()
  {
    return $this->serviceDirectoryConfig;
  }
  /**
   * Optional. SSL certificate to use for requests to GitHub Enterprise.
   *
   * @param string $sslCaCertificate
   */
  public function setSslCaCertificate($sslCaCertificate)
  {
    $this->sslCaCertificate = $sslCaCertificate;
  }
  /**
   * @return string
   */
  public function getSslCaCertificate()
  {
    return $this->sslCaCertificate;
  }
  /**
   * Optional. SecretManager resource containing the webhook secret of the
   * GitHub App, formatted as `projects/secrets/versions` or
   * `projects/locations/secrets/versions` (if regional secrets are supported in
   * that location).
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
class_alias(GitHubEnterpriseConfig::class, 'Google_Service_DeveloperConnect_GitHubEnterpriseConfig');
