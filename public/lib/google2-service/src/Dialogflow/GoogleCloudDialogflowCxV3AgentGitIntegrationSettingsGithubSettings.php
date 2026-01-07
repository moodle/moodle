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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3AgentGitIntegrationSettingsGithubSettings extends \Google\Collection
{
  protected $collection_key = 'branches';
  /**
   * The access token used to authenticate the access to the GitHub repository.
   *
   * @var string
   */
  public $accessToken;
  /**
   * A list of branches configured to be used from Dialogflow.
   *
   * @var string[]
   */
  public $branches;
  /**
   * The unique repository display name for the GitHub repository.
   *
   * @var string
   */
  public $displayName;
  /**
   * The GitHub repository URI related to the agent.
   *
   * @var string
   */
  public $repositoryUri;
  /**
   * The branch of the GitHub repository tracked for this agent.
   *
   * @var string
   */
  public $trackingBranch;

  /**
   * The access token used to authenticate the access to the GitHub repository.
   *
   * @param string $accessToken
   */
  public function setAccessToken($accessToken)
  {
    $this->accessToken = $accessToken;
  }
  /**
   * @return string
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }
  /**
   * A list of branches configured to be used from Dialogflow.
   *
   * @param string[] $branches
   */
  public function setBranches($branches)
  {
    $this->branches = $branches;
  }
  /**
   * @return string[]
   */
  public function getBranches()
  {
    return $this->branches;
  }
  /**
   * The unique repository display name for the GitHub repository.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The GitHub repository URI related to the agent.
   *
   * @param string $repositoryUri
   */
  public function setRepositoryUri($repositoryUri)
  {
    $this->repositoryUri = $repositoryUri;
  }
  /**
   * @return string
   */
  public function getRepositoryUri()
  {
    return $this->repositoryUri;
  }
  /**
   * The branch of the GitHub repository tracked for this agent.
   *
   * @param string $trackingBranch
   */
  public function setTrackingBranch($trackingBranch)
  {
    $this->trackingBranch = $trackingBranch;
  }
  /**
   * @return string
   */
  public function getTrackingBranch()
  {
    return $this->trackingBranch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3AgentGitIntegrationSettingsGithubSettings::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3AgentGitIntegrationSettingsGithubSettings');
