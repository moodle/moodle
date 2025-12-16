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

namespace Google\Service\Dataform;

class GitRemoteSettings extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const TOKEN_STATUS_TOKEN_STATUS_UNSPECIFIED = 'TOKEN_STATUS_UNSPECIFIED';
  /**
   * The token could not be found in Secret Manager (or the Dataform Service
   * Account did not have permission to access it).
   */
  public const TOKEN_STATUS_NOT_FOUND = 'NOT_FOUND';
  /**
   * The token could not be used to authenticate against the Git remote.
   */
  public const TOKEN_STATUS_INVALID = 'INVALID';
  /**
   * The token was used successfully to authenticate against the Git remote.
   */
  public const TOKEN_STATUS_VALID = 'VALID';
  /**
   * Optional. The name of the Secret Manager secret version to use as an
   * authentication token for Git operations. Must be in the format
   * `projects/secrets/versions`.
   *
   * @var string
   */
  public $authenticationTokenSecretVersion;
  /**
   * Required. The Git remote's default branch name.
   *
   * @var string
   */
  public $defaultBranch;
  protected $sshAuthenticationConfigType = SshAuthenticationConfig::class;
  protected $sshAuthenticationConfigDataType = '';
  /**
   * Output only. Deprecated: The field does not contain any token status
   * information.
   *
   * @deprecated
   * @var string
   */
  public $tokenStatus;
  /**
   * Required. The Git remote's URL.
   *
   * @var string
   */
  public $url;

  /**
   * Optional. The name of the Secret Manager secret version to use as an
   * authentication token for Git operations. Must be in the format
   * `projects/secrets/versions`.
   *
   * @param string $authenticationTokenSecretVersion
   */
  public function setAuthenticationTokenSecretVersion($authenticationTokenSecretVersion)
  {
    $this->authenticationTokenSecretVersion = $authenticationTokenSecretVersion;
  }
  /**
   * @return string
   */
  public function getAuthenticationTokenSecretVersion()
  {
    return $this->authenticationTokenSecretVersion;
  }
  /**
   * Required. The Git remote's default branch name.
   *
   * @param string $defaultBranch
   */
  public function setDefaultBranch($defaultBranch)
  {
    $this->defaultBranch = $defaultBranch;
  }
  /**
   * @return string
   */
  public function getDefaultBranch()
  {
    return $this->defaultBranch;
  }
  /**
   * Optional. Authentication fields for remote uris using SSH protocol.
   *
   * @param SshAuthenticationConfig $sshAuthenticationConfig
   */
  public function setSshAuthenticationConfig(SshAuthenticationConfig $sshAuthenticationConfig)
  {
    $this->sshAuthenticationConfig = $sshAuthenticationConfig;
  }
  /**
   * @return SshAuthenticationConfig
   */
  public function getSshAuthenticationConfig()
  {
    return $this->sshAuthenticationConfig;
  }
  /**
   * Output only. Deprecated: The field does not contain any token status
   * information.
   *
   * Accepted values: TOKEN_STATUS_UNSPECIFIED, NOT_FOUND, INVALID, VALID
   *
   * @deprecated
   * @param self::TOKEN_STATUS_* $tokenStatus
   */
  public function setTokenStatus($tokenStatus)
  {
    $this->tokenStatus = $tokenStatus;
  }
  /**
   * @deprecated
   * @return self::TOKEN_STATUS_*
   */
  public function getTokenStatus()
  {
    return $this->tokenStatus;
  }
  /**
   * Required. The Git remote's URL.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GitRemoteSettings::class, 'Google_Service_Dataform_GitRemoteSettings');
