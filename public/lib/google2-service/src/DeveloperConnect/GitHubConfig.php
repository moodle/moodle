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

class GitHubConfig extends \Google\Model
{
  /**
   * GitHub App not specified.
   */
  public const GITHUB_APP_GIT_HUB_APP_UNSPECIFIED = 'GIT_HUB_APP_UNSPECIFIED';
  /**
   * The Developer Connect GitHub Application.
   */
  public const GITHUB_APP_DEVELOPER_CONNECT = 'DEVELOPER_CONNECT';
  /**
   * The Firebase GitHub Application.
   */
  public const GITHUB_APP_FIREBASE = 'FIREBASE';
  /**
   * The Gemini Code Assist Application.
   */
  public const GITHUB_APP_GEMINI_CODE_ASSIST = 'GEMINI_CODE_ASSIST';
  /**
   * Optional. GitHub App installation id.
   *
   * @var string
   */
  public $appInstallationId;
  protected $authorizerCredentialType = OAuthCredential::class;
  protected $authorizerCredentialDataType = '';
  /**
   * Required. Immutable. The GitHub Application that was installed to the
   * GitHub user or organization.
   *
   * @var string
   */
  public $githubApp;
  /**
   * Output only. The URI to navigate to in order to manage the installation
   * associated with this GitHubConfig.
   *
   * @var string
   */
  public $installationUri;

  /**
   * Optional. GitHub App installation id.
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
   * Optional. OAuth credential of the account that authorized the GitHub App.
   * It is recommended to use a robot account instead of a human user account.
   * The OAuth token must be tied to the GitHub App of this config.
   *
   * @param OAuthCredential $authorizerCredential
   */
  public function setAuthorizerCredential(OAuthCredential $authorizerCredential)
  {
    $this->authorizerCredential = $authorizerCredential;
  }
  /**
   * @return OAuthCredential
   */
  public function getAuthorizerCredential()
  {
    return $this->authorizerCredential;
  }
  /**
   * Required. Immutable. The GitHub Application that was installed to the
   * GitHub user or organization.
   *
   * Accepted values: GIT_HUB_APP_UNSPECIFIED, DEVELOPER_CONNECT, FIREBASE,
   * GEMINI_CODE_ASSIST
   *
   * @param self::GITHUB_APP_* $githubApp
   */
  public function setGithubApp($githubApp)
  {
    $this->githubApp = $githubApp;
  }
  /**
   * @return self::GITHUB_APP_*
   */
  public function getGithubApp()
  {
    return $this->githubApp;
  }
  /**
   * Output only. The URI to navigate to in order to manage the installation
   * associated with this GitHubConfig.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GitHubConfig::class, 'Google_Service_DeveloperConnect_GitHubConfig');
