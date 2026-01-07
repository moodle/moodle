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

namespace Google\Service\ChecksService;

class GoogleChecksRepoScanV1alphaScmMetadata extends \Google\Model
{
  /**
   * Required. Branch name.
   *
   * @var string
   */
  public $branch;
  protected $pullRequestType = GoogleChecksRepoScanV1alphaPullRequest::class;
  protected $pullRequestDataType = '';
  /**
   * Required. Git remote URL.
   *
   * @var string
   */
  public $remoteUri;
  /**
   * Required. Revision ID, e.g. Git commit hash.
   *
   * @var string
   */
  public $revisionId;

  /**
   * Required. Branch name.
   *
   * @param string $branch
   */
  public function setBranch($branch)
  {
    $this->branch = $branch;
  }
  /**
   * @return string
   */
  public function getBranch()
  {
    return $this->branch;
  }
  /**
   * Optional. Contains info about the associated pull request. This is only
   * populated for pull request scans.
   *
   * @param GoogleChecksRepoScanV1alphaPullRequest $pullRequest
   */
  public function setPullRequest(GoogleChecksRepoScanV1alphaPullRequest $pullRequest)
  {
    $this->pullRequest = $pullRequest;
  }
  /**
   * @return GoogleChecksRepoScanV1alphaPullRequest
   */
  public function getPullRequest()
  {
    return $this->pullRequest;
  }
  /**
   * Required. Git remote URL.
   *
   * @param string $remoteUri
   */
  public function setRemoteUri($remoteUri)
  {
    $this->remoteUri = $remoteUri;
  }
  /**
   * @return string
   */
  public function getRemoteUri()
  {
    return $this->remoteUri;
  }
  /**
   * Required. Revision ID, e.g. Git commit hash.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksRepoScanV1alphaScmMetadata::class, 'Google_Service_ChecksService_GoogleChecksRepoScanV1alphaScmMetadata');
