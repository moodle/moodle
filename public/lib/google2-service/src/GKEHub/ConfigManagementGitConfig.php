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

namespace Google\Service\GKEHub;

class ConfigManagementGitConfig extends \Google\Model
{
  /**
   * Optional. The Google Cloud Service Account Email used for auth when
   * secret_type is gcpServiceAccount.
   *
   * @var string
   */
  public $gcpServiceAccountEmail;
  /**
   * Optional. URL for the HTTPS proxy to be used when communicating with the
   * Git repo.
   *
   * @var string
   */
  public $httpsProxy;
  /**
   * Optional. The path within the Git repository that represents the top level
   * of the repo to sync. Default: the root directory of the repository.
   *
   * @var string
   */
  public $policyDir;
  /**
   * Required. Type of secret configured for access to the Git repo. Must be one
   * of ssh, cookiefile, gcenode, token, gcpserviceaccount, githubapp or none.
   * The validation of this is case-sensitive.
   *
   * @var string
   */
  public $secretType;
  /**
   * Optional. The branch of the repository to sync from. Default: master.
   *
   * @var string
   */
  public $syncBranch;
  /**
   * Required. The URL of the Git repository to use as the source of truth.
   *
   * @var string
   */
  public $syncRepo;
  /**
   * Optional. Git revision (tag or hash) to check out. Default HEAD.
   *
   * @var string
   */
  public $syncRev;
  /**
   * Optional. Period in seconds between consecutive syncs. Default: 15.
   *
   * @var string
   */
  public $syncWaitSecs;

  /**
   * Optional. The Google Cloud Service Account Email used for auth when
   * secret_type is gcpServiceAccount.
   *
   * @param string $gcpServiceAccountEmail
   */
  public function setGcpServiceAccountEmail($gcpServiceAccountEmail)
  {
    $this->gcpServiceAccountEmail = $gcpServiceAccountEmail;
  }
  /**
   * @return string
   */
  public function getGcpServiceAccountEmail()
  {
    return $this->gcpServiceAccountEmail;
  }
  /**
   * Optional. URL for the HTTPS proxy to be used when communicating with the
   * Git repo.
   *
   * @param string $httpsProxy
   */
  public function setHttpsProxy($httpsProxy)
  {
    $this->httpsProxy = $httpsProxy;
  }
  /**
   * @return string
   */
  public function getHttpsProxy()
  {
    return $this->httpsProxy;
  }
  /**
   * Optional. The path within the Git repository that represents the top level
   * of the repo to sync. Default: the root directory of the repository.
   *
   * @param string $policyDir
   */
  public function setPolicyDir($policyDir)
  {
    $this->policyDir = $policyDir;
  }
  /**
   * @return string
   */
  public function getPolicyDir()
  {
    return $this->policyDir;
  }
  /**
   * Required. Type of secret configured for access to the Git repo. Must be one
   * of ssh, cookiefile, gcenode, token, gcpserviceaccount, githubapp or none.
   * The validation of this is case-sensitive.
   *
   * @param string $secretType
   */
  public function setSecretType($secretType)
  {
    $this->secretType = $secretType;
  }
  /**
   * @return string
   */
  public function getSecretType()
  {
    return $this->secretType;
  }
  /**
   * Optional. The branch of the repository to sync from. Default: master.
   *
   * @param string $syncBranch
   */
  public function setSyncBranch($syncBranch)
  {
    $this->syncBranch = $syncBranch;
  }
  /**
   * @return string
   */
  public function getSyncBranch()
  {
    return $this->syncBranch;
  }
  /**
   * Required. The URL of the Git repository to use as the source of truth.
   *
   * @param string $syncRepo
   */
  public function setSyncRepo($syncRepo)
  {
    $this->syncRepo = $syncRepo;
  }
  /**
   * @return string
   */
  public function getSyncRepo()
  {
    return $this->syncRepo;
  }
  /**
   * Optional. Git revision (tag or hash) to check out. Default HEAD.
   *
   * @param string $syncRev
   */
  public function setSyncRev($syncRev)
  {
    $this->syncRev = $syncRev;
  }
  /**
   * @return string
   */
  public function getSyncRev()
  {
    return $this->syncRev;
  }
  /**
   * Optional. Period in seconds between consecutive syncs. Default: 15.
   *
   * @param string $syncWaitSecs
   */
  public function setSyncWaitSecs($syncWaitSecs)
  {
    $this->syncWaitSecs = $syncWaitSecs;
  }
  /**
   * @return string
   */
  public function getSyncWaitSecs()
  {
    return $this->syncWaitSecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementGitConfig::class, 'Google_Service_GKEHub_ConfigManagementGitConfig');
