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

namespace Google\Service\DisplayVideo;

class AdPolicy extends \Google\Collection
{
  /**
   * Unknown or not specified.
   */
  public const AD_POLICY_APPROVAL_STATUS_AD_POLICY_APPROVAL_STATUS_UNKNOWN = 'AD_POLICY_APPROVAL_STATUS_UNKNOWN';
  /**
   * Will not serve.
   */
  public const AD_POLICY_APPROVAL_STATUS_DISAPPROVED = 'DISAPPROVED';
  /**
   * Will serve with restrictions.
   */
  public const AD_POLICY_APPROVAL_STATUS_APPROVED_LIMITED = 'APPROVED_LIMITED';
  /**
   * Will serve without restrictions.
   */
  public const AD_POLICY_APPROVAL_STATUS_APPROVED = 'APPROVED';
  /**
   * Will not serve in targeted countries, but may serve for users who are
   * searching for information about the targeted countries.
   */
  public const AD_POLICY_APPROVAL_STATUS_AREA_OF_INTEREST_ONLY = 'AREA_OF_INTEREST_ONLY';
  /**
   * Unknown or not specified.
   */
  public const AD_POLICY_REVIEW_STATUS_AD_POLICY_REVIEW_STATUS_UNKNOWN = 'AD_POLICY_REVIEW_STATUS_UNKNOWN';
  /**
   * Currently under review.
   */
  public const AD_POLICY_REVIEW_STATUS_REVIEW_IN_PROGRESS = 'REVIEW_IN_PROGRESS';
  /**
   * Primary review complete. Other reviews may still be in progress.
   */
  public const AD_POLICY_REVIEW_STATUS_REVIEWED = 'REVIEWED';
  /**
   * Resubmitted for approval or a policy decision has been appealed.
   */
  public const AD_POLICY_REVIEW_STATUS_UNDER_APPEAL = 'UNDER_APPEAL';
  /**
   * Deemed eligible and may be serving. Further review could still follow.
   */
  public const AD_POLICY_REVIEW_STATUS_ELIGIBLE_MAY_SERVE = 'ELIGIBLE_MAY_SERVE';
  protected $collection_key = 'adPolicyTopicEntry';
  /**
   * The policy approval status of an ad, indicating the approval decision.
   *
   * @var string
   */
  public $adPolicyApprovalStatus;
  /**
   * The policy review status of an ad, indicating where in the review process
   * the ad is currently.
   *
   * @var string
   */
  public $adPolicyReviewStatus;
  protected $adPolicyTopicEntryType = AdPolicyTopicEntry::class;
  protected $adPolicyTopicEntryDataType = 'array';

  /**
   * The policy approval status of an ad, indicating the approval decision.
   *
   * Accepted values: AD_POLICY_APPROVAL_STATUS_UNKNOWN, DISAPPROVED,
   * APPROVED_LIMITED, APPROVED, AREA_OF_INTEREST_ONLY
   *
   * @param self::AD_POLICY_APPROVAL_STATUS_* $adPolicyApprovalStatus
   */
  public function setAdPolicyApprovalStatus($adPolicyApprovalStatus)
  {
    $this->adPolicyApprovalStatus = $adPolicyApprovalStatus;
  }
  /**
   * @return self::AD_POLICY_APPROVAL_STATUS_*
   */
  public function getAdPolicyApprovalStatus()
  {
    return $this->adPolicyApprovalStatus;
  }
  /**
   * The policy review status of an ad, indicating where in the review process
   * the ad is currently.
   *
   * Accepted values: AD_POLICY_REVIEW_STATUS_UNKNOWN, REVIEW_IN_PROGRESS,
   * REVIEWED, UNDER_APPEAL, ELIGIBLE_MAY_SERVE
   *
   * @param self::AD_POLICY_REVIEW_STATUS_* $adPolicyReviewStatus
   */
  public function setAdPolicyReviewStatus($adPolicyReviewStatus)
  {
    $this->adPolicyReviewStatus = $adPolicyReviewStatus;
  }
  /**
   * @return self::AD_POLICY_REVIEW_STATUS_*
   */
  public function getAdPolicyReviewStatus()
  {
    return $this->adPolicyReviewStatus;
  }
  /**
   * The entries for each policy topic identified as relating to the ad. Each
   * entry includes the topic, restriction level, and guidance on how to fix
   * policy issues.
   *
   * @param AdPolicyTopicEntry[] $adPolicyTopicEntry
   */
  public function setAdPolicyTopicEntry($adPolicyTopicEntry)
  {
    $this->adPolicyTopicEntry = $adPolicyTopicEntry;
  }
  /**
   * @return AdPolicyTopicEntry[]
   */
  public function getAdPolicyTopicEntry()
  {
    return $this->adPolicyTopicEntry;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdPolicy::class, 'Google_Service_DisplayVideo_AdPolicy');
