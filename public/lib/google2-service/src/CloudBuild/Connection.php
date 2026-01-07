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
   * Output only. Server assigned timestamp for when the connection was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. If disabled is set to true, functionality is disabled for this
   * connection. Repository based API methods and webhooks processing for
   * repositories in this connection will be disabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  protected $githubConfigType = GitHubConfig::class;
  protected $githubConfigDataType = '';
  protected $githubEnterpriseConfigType = GoogleDevtoolsCloudbuildV2GitHubEnterpriseConfig::class;
  protected $githubEnterpriseConfigDataType = '';
  protected $gitlabConfigType = GoogleDevtoolsCloudbuildV2GitLabConfig::class;
  protected $gitlabConfigDataType = '';
  protected $installationStateType = InstallationState::class;
  protected $installationStateDataType = '';
  /**
   * Immutable. The resource name of the connection, in the format
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
   * Output only. Server assigned timestamp for when the connection was updated.
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
   * Configuration for connections to Bitbucket Cloud.
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
   * Configuration for connections to Bitbucket Data Center.
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
   * Output only. Server assigned timestamp for when the connection was created.
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
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
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
   * @param GoogleDevtoolsCloudbuildV2GitHubEnterpriseConfig $githubEnterpriseConfig
   */
  public function setGithubEnterpriseConfig(GoogleDevtoolsCloudbuildV2GitHubEnterpriseConfig $githubEnterpriseConfig)
  {
    $this->githubEnterpriseConfig = $githubEnterpriseConfig;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV2GitHubEnterpriseConfig
   */
  public function getGithubEnterpriseConfig()
  {
    return $this->githubEnterpriseConfig;
  }
  /**
   * Configuration for connections to gitlab.com or an instance of GitLab
   * Enterprise.
   *
   * @param GoogleDevtoolsCloudbuildV2GitLabConfig $gitlabConfig
   */
  public function setGitlabConfig(GoogleDevtoolsCloudbuildV2GitLabConfig $gitlabConfig)
  {
    $this->gitlabConfig = $gitlabConfig;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV2GitLabConfig
   */
  public function getGitlabConfig()
  {
    return $this->gitlabConfig;
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
   * Immutable. The resource name of the connection, in the format
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
   * Output only. Server assigned timestamp for when the connection was updated.
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
class_alias(Connection::class, 'Google_Service_CloudBuild_Connection');
