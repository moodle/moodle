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

namespace Google\Service\SecureSourceManager;

class BranchRule extends \Google\Collection
{
  protected $collection_key = 'requiredStatusChecks';
  /**
   * Optional. Determines if allow stale reviews or approvals before merging to
   * the branch.
   *
   * @var bool
   */
  public $allowStaleReviews;
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user. See https://google.aip.dev/128#annotations for more details such
   * as format and size limitations.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. Create timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Determines if the branch rule is disabled or not.
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
  /**
   * Optional. The pattern of the branch that can match to this BranchRule.
   * Specified as regex. .* for all branches. Examples: main, (main|release.*).
   * Current MVP phase only support `.*` for wildcard.
   *
   * @var string
   */
  public $includePattern;
  /**
   * Optional. The minimum number of approvals required for the branch rule to
   * be matched.
   *
   * @var int
   */
  public $minimumApprovalsCount;
  /**
   * Optional. The minimum number of reviews required for the branch rule to be
   * matched.
   *
   * @var int
   */
  public $minimumReviewsCount;
  /**
   * Optional. A unique identifier for a BranchRule. The name should be of the
   * format: `projects/{project}/locations/{location}/repositories/{repository}/
   * branchRules/{branch_rule}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Determines if require comments resolved before merging to the
   * branch.
   *
   * @var bool
   */
  public $requireCommentsResolved;
  /**
   * Optional. Determines if require linear history before merging to the
   * branch.
   *
   * @var bool
   */
  public $requireLinearHistory;
  /**
   * Optional. Determines if the branch rule requires a pull request or not.
   *
   * @var bool
   */
  public $requirePullRequest;
  protected $requiredStatusChecksType = Check::class;
  protected $requiredStatusChecksDataType = 'array';
  /**
   * Output only. Unique identifier of the repository.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Update timestamp.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Determines if allow stale reviews or approvals before merging to
   * the branch.
   *
   * @param bool $allowStaleReviews
   */
  public function setAllowStaleReviews($allowStaleReviews)
  {
    $this->allowStaleReviews = $allowStaleReviews;
  }
  /**
   * @return bool
   */
  public function getAllowStaleReviews()
  {
    return $this->allowStaleReviews;
  }
  /**
   * Optional. User annotations. These attributes can only be set and used by
   * the user. See https://google.aip.dev/128#annotations for more details such
   * as format and size limitations.
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
   * Output only. Create timestamp.
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
   * Optional. Determines if the branch rule is disabled or not.
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
   * Optional. The pattern of the branch that can match to this BranchRule.
   * Specified as regex. .* for all branches. Examples: main, (main|release.*).
   * Current MVP phase only support `.*` for wildcard.
   *
   * @param string $includePattern
   */
  public function setIncludePattern($includePattern)
  {
    $this->includePattern = $includePattern;
  }
  /**
   * @return string
   */
  public function getIncludePattern()
  {
    return $this->includePattern;
  }
  /**
   * Optional. The minimum number of approvals required for the branch rule to
   * be matched.
   *
   * @param int $minimumApprovalsCount
   */
  public function setMinimumApprovalsCount($minimumApprovalsCount)
  {
    $this->minimumApprovalsCount = $minimumApprovalsCount;
  }
  /**
   * @return int
   */
  public function getMinimumApprovalsCount()
  {
    return $this->minimumApprovalsCount;
  }
  /**
   * Optional. The minimum number of reviews required for the branch rule to be
   * matched.
   *
   * @param int $minimumReviewsCount
   */
  public function setMinimumReviewsCount($minimumReviewsCount)
  {
    $this->minimumReviewsCount = $minimumReviewsCount;
  }
  /**
   * @return int
   */
  public function getMinimumReviewsCount()
  {
    return $this->minimumReviewsCount;
  }
  /**
   * Optional. A unique identifier for a BranchRule. The name should be of the
   * format: `projects/{project}/locations/{location}/repositories/{repository}/
   * branchRules/{branch_rule}`
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
   * Optional. Determines if require comments resolved before merging to the
   * branch.
   *
   * @param bool $requireCommentsResolved
   */
  public function setRequireCommentsResolved($requireCommentsResolved)
  {
    $this->requireCommentsResolved = $requireCommentsResolved;
  }
  /**
   * @return bool
   */
  public function getRequireCommentsResolved()
  {
    return $this->requireCommentsResolved;
  }
  /**
   * Optional. Determines if require linear history before merging to the
   * branch.
   *
   * @param bool $requireLinearHistory
   */
  public function setRequireLinearHistory($requireLinearHistory)
  {
    $this->requireLinearHistory = $requireLinearHistory;
  }
  /**
   * @return bool
   */
  public function getRequireLinearHistory()
  {
    return $this->requireLinearHistory;
  }
  /**
   * Optional. Determines if the branch rule requires a pull request or not.
   *
   * @param bool $requirePullRequest
   */
  public function setRequirePullRequest($requirePullRequest)
  {
    $this->requirePullRequest = $requirePullRequest;
  }
  /**
   * @return bool
   */
  public function getRequirePullRequest()
  {
    return $this->requirePullRequest;
  }
  /**
   * Optional. List of required status checks before merging to the branch.
   *
   * @param Check[] $requiredStatusChecks
   */
  public function setRequiredStatusChecks($requiredStatusChecks)
  {
    $this->requiredStatusChecks = $requiredStatusChecks;
  }
  /**
   * @return Check[]
   */
  public function getRequiredStatusChecks()
  {
    return $this->requiredStatusChecks;
  }
  /**
   * Output only. Unique identifier of the repository.
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
   * Output only. Update timestamp.
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
class_alias(BranchRule::class, 'Google_Service_SecureSourceManager_BranchRule');
