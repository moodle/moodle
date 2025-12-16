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

class Connection extends \Google\Model
{
  /**
   * Optional. Allows clients to store small amounts of arbitrary data.
   *
   * @var string[]
   */
  public $annotations;
  protected $bitbucketCloudConfigType = BitbucketCloudConfig::class;
  protected $bitbucketCloudConfigDataType = '';
  protected $bitbucketDataCenterConfigType = BitbucketDataCenterConfig::class;
  protected $bitbucketDataCenterConfigDataType = '';
  /**
   * Output only. [Output only] Create timestamp
   *
   * @var string
   */
  public $createTime;
  protected $cryptoKeyConfigType = CryptoKeyConfig::class;
  protected $cryptoKeyConfigDataType = '';
  /**
   * Output only. [Output only] Delete timestamp
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. If disabled is set to true, functionality is disabled for this
   * connection. Repository based API methods and webhooks processing for
   * repositories in this connection will be disabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  protected $gitProxyConfigType = GitProxyConfig::class;
  protected $gitProxyConfigDataType = '';
  protected $githubConfigType = GitHubConfig::class;
  protected $githubConfigDataType = '';
  protected $githubEnterpriseConfigType = GitHubEnterpriseConfig::class;
  protected $githubEnterpriseConfigDataType = '';
  protected $gitlabConfigType = GitLabConfig::class;
  protected $gitlabConfigDataType = '';
  protected $gitlabEnterpriseConfigType = GitLabEnterpriseConfig::class;
  protected $gitlabEnterpriseConfigDataType = '';
  protected $installationStateType = InstallationState::class;
  protected $installationStateDataType = '';
  /**
   * Optional. Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the connection, in the format
   * `projects/{project}/locations/{location}/connections/{connection_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Set to true when the connection is being set up or updated in
   * the background.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. A system-assigned unique identifier for the Connection.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. [Output only] Update timestamp
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Allows clients to store small amounts of arbitrary data.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Configuration for connections to an instance of Bitbucket Clouds.
   *
   * @param BitbucketCloudConfig $bitbucketCloudConfig
   */
  public function setBitbucketCloudConfig(BitbucketCloudConfig $bitbucketCloudConfig)
  {
    $this->bitbucketCloudConfig = $bitbucketCloudConfig;
  }
  /**
   * @return BitbucketCloudConfig
   */
  public function getBitbucketCloudConfig()
  {
    return $this->bitbucketCloudConfig;
  }
  /**
   * Configuration for connections to an instance of Bitbucket Data Center.
   *
   * @param BitbucketDataCenterConfig $bitbucketDataCenterConfig
   */
  public function setBitbucketDataCenterConfig(BitbucketDataCenterConfig $bitbucketDataCenterConfig)
  {
    $this->bitbucketDataCenterConfig = $bitbucketDataCenterConfig;
  }
  /**
   * @return BitbucketDataCenterConfig
   */
  public function getBitbucketDataCenterConfig()
  {
    return $this->bitbucketDataCenterConfig;
  }
  /**
   * Output only. [Output only] Create timestamp
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The crypto key configuration. This field is used by the Customer-
   * Managed Encryption Keys (CMEK) feature.
   *
   * @param CryptoKeyConfig $cryptoKeyConfig
   */
  public function setCryptoKeyConfig(CryptoKeyConfig $cryptoKeyConfig)
  {
    $this->cryptoKeyConfig = $cryptoKeyConfig;
  }
  /**
   * @return CryptoKeyConfig
   */
  public function getCryptoKeyConfig()
  {
    return $this->cryptoKeyConfig;
  }
  /**
   * Output only. [Output only] Delete timestamp
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Optional. If disabled is set to true, functionality is disabled for this
   * connection. Repository based API methods and webhooks processing for
   * repositories in this connection will be disabled.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Configuration for the git proxy feature. Enabling the git proxy
   * allows clients to perform git operations on the repositories linked in the
   * connection.
   *
   * @param GitProxyConfig $gitProxyConfig
   */
  public function setGitProxyConfig(GitProxyConfig $gitProxyConfig)
  {
    $this->gitProxyConfig = $gitProxyConfig;
  }
  /**
   * @return GitProxyConfig
   */
  public function getGitProxyConfig()
  {
    return $this->gitProxyConfig;
  }
  /**
   * Configuration for connections to github.com.
   *
   * @param GitHubConfig $githubConfig
   */
  public function setGithubConfig(GitHubConfig $githubConfig)
  {
    $this->githubConfig = $githubConfig;
  }
  /**
   * @return GitHubConfig
   */
  public function getGithubConfig()
  {
    return $this->githubConfig;
  }
  /**
   * Configuration for connections to an instance of GitHub Enterprise.
   *
   * @param GitHubEnterpriseConfig $githubEnterpriseConfig
   */
  public function setGithubEnterpriseConfig(GitHubEnterpriseConfig $githubEnterpriseConfig)
  {
    $this->githubEnterpriseConfig = $githubEnterpriseConfig;
  }
  /**
   * @return GitHubEnterpriseConfig
   */
  public function getGithubEnterpriseConfig()
  {
    return $this->githubEnterpriseConfig;
  }
  /**
   * Configuration for connections to gitlab.com.
   *
   * @param GitLabConfig $gitlabConfig
   */
  public function setGitlabConfig(GitLabConfig $gitlabConfig)
  {
    $this->gitlabConfig = $gitlabConfig;
  }
  /**
   * @return GitLabConfig
   */
  public function getGitlabConfig()
  {
    return $this->gitlabConfig;
  }
  /**
   * Configuration for connections to an instance of GitLab Enterprise.
   *
   * @param GitLabEnterpriseConfig $gitlabEnterpriseConfig
   */
  public function setGitlabEnterpriseConfig(GitLabEnterpriseConfig $gitlabEnterpriseConfig)
  {
    $this->gitlabEnterpriseConfig = $gitlabEnterpriseConfig;
  }
  /**
   * @return GitLabEnterpriseConfig
   */
  public function getGitlabEnterpriseConfig()
  {
    return $this->gitlabEnterpriseConfig;
  }
  /**
   * Output only. Installation state of the Connection.
   *
   * @param InstallationState $installationState
   */
  public function setInstallationState(InstallationState $installationState)
  {
    $this->installationState = $installationState;
  }
  /**
   * @return InstallationState
   */
  public function getInstallationState()
  {
    return $this->installationState;
  }
  /**
   * Optional. Labels as key value pairs
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. The resource name of the connection, in the format
   * `projects/{project}/locations/{location}/connections/{connection_id}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Set to true when the connection is being set up or updated in
   * the background.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. A system-assigned unique identifier for the Connection.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. [Output only] Update timestamp
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Connection::class, 'Google_Service_DeveloperConnect_Connection');
