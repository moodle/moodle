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

class ReviewStatusInfo extends \Google\Collection
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const APPROVAL_STATUS_APPROVAL_STATUS_UNSPECIFIED = 'APPROVAL_STATUS_UNSPECIFIED';
  /**
   * The creative is still under review and not servable.
   */
  public const APPROVAL_STATUS_APPROVAL_STATUS_PENDING_NOT_SERVABLE = 'APPROVAL_STATUS_PENDING_NOT_SERVABLE';
  /**
   * The creative has passed creative & landing page review and is servable, but
   * is awaiting additional content & policy review.
   */
  public const APPROVAL_STATUS_APPROVAL_STATUS_PENDING_SERVABLE = 'APPROVAL_STATUS_PENDING_SERVABLE';
  /**
   * Both creative & landing page review and content & policy review are
   * approved. The creative is servable.
   */
  public const APPROVAL_STATUS_APPROVAL_STATUS_APPROVED_SERVABLE = 'APPROVAL_STATUS_APPROVED_SERVABLE';
  /**
   * There is an issue with the creative that must be fixed before it can serve.
   */
  public const APPROVAL_STATUS_APPROVAL_STATUS_REJECTED_NOT_SERVABLE = 'APPROVAL_STATUS_REJECTED_NOT_SERVABLE';
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const CONTENT_AND_POLICY_REVIEW_STATUS_REVIEW_STATUS_UNSPECIFIED = 'REVIEW_STATUS_UNSPECIFIED';
  /**
   * The creative is approved.
   */
  public const CONTENT_AND_POLICY_REVIEW_STATUS_REVIEW_STATUS_APPROVED = 'REVIEW_STATUS_APPROVED';
  /**
   * The creative is rejected.
   */
  public const CONTENT_AND_POLICY_REVIEW_STATUS_REVIEW_STATUS_REJECTED = 'REVIEW_STATUS_REJECTED';
  /**
   * The creative is pending review.
   */
  public const CONTENT_AND_POLICY_REVIEW_STATUS_REVIEW_STATUS_PENDING = 'REVIEW_STATUS_PENDING';
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const CREATIVE_AND_LANDING_PAGE_REVIEW_STATUS_REVIEW_STATUS_UNSPECIFIED = 'REVIEW_STATUS_UNSPECIFIED';
  /**
   * The creative is approved.
   */
  public const CREATIVE_AND_LANDING_PAGE_REVIEW_STATUS_REVIEW_STATUS_APPROVED = 'REVIEW_STATUS_APPROVED';
  /**
   * The creative is rejected.
   */
  public const CREATIVE_AND_LANDING_PAGE_REVIEW_STATUS_REVIEW_STATUS_REJECTED = 'REVIEW_STATUS_REJECTED';
  /**
   * The creative is pending review.
   */
  public const CREATIVE_AND_LANDING_PAGE_REVIEW_STATUS_REVIEW_STATUS_PENDING = 'REVIEW_STATUS_PENDING';
  protected $collection_key = 'exchangeReviewStatuses';
  /**
   * Represents the basic approval needed for a creative to begin serving.
   * Summary of creative_and_landing_page_review_status and
   * content_and_policy_review_status.
   *
   * @var string
   */
  public $approvalStatus;
  /**
   * Content and policy review status for the creative.
   *
   * @var string
   */
  public $contentAndPolicyReviewStatus;
  /**
   * Creative and landing page review status for the creative.
   *
   * @var string
   */
  public $creativeAndLandingPageReviewStatus;
  protected $exchangeReviewStatusesType = ExchangeReviewStatus::class;
  protected $exchangeReviewStatusesDataType = 'array';

  /**
   * Represents the basic approval needed for a creative to begin serving.
   * Summary of creative_and_landing_page_review_status and
   * content_and_policy_review_status.
   *
   * Accepted values: APPROVAL_STATUS_UNSPECIFIED,
   * APPROVAL_STATUS_PENDING_NOT_SERVABLE, APPROVAL_STATUS_PENDING_SERVABLE,
   * APPROVAL_STATUS_APPROVED_SERVABLE, APPROVAL_STATUS_REJECTED_NOT_SERVABLE
   *
   * @param self::APPROVAL_STATUS_* $approvalStatus
   */
  public function setApprovalStatus($approvalStatus)
  {
    $this->approvalStatus = $approvalStatus;
  }
  /**
   * @return self::APPROVAL_STATUS_*
   */
  public function getApprovalStatus()
  {
    return $this->approvalStatus;
  }
  /**
   * Content and policy review status for the creative.
   *
   * Accepted values: REVIEW_STATUS_UNSPECIFIED, REVIEW_STATUS_APPROVED,
   * REVIEW_STATUS_REJECTED, REVIEW_STATUS_PENDING
   *
   * @param self::CONTENT_AND_POLICY_REVIEW_STATUS_* $contentAndPolicyReviewStatus
   */
  public function setContentAndPolicyReviewStatus($contentAndPolicyReviewStatus)
  {
    $this->contentAndPolicyReviewStatus = $contentAndPolicyReviewStatus;
  }
  /**
   * @return self::CONTENT_AND_POLICY_REVIEW_STATUS_*
   */
  public function getContentAndPolicyReviewStatus()
  {
    return $this->contentAndPolicyReviewStatus;
  }
  /**
   * Creative and landing page review status for the creative.
   *
   * Accepted values: REVIEW_STATUS_UNSPECIFIED, REVIEW_STATUS_APPROVED,
   * REVIEW_STATUS_REJECTED, REVIEW_STATUS_PENDING
   *
   * @param self::CREATIVE_AND_LANDING_PAGE_REVIEW_STATUS_* $creativeAndLandingPageReviewStatus
   */
  public function setCreativeAndLandingPageReviewStatus($creativeAndLandingPageReviewStatus)
  {
    $this->creativeAndLandingPageReviewStatus = $creativeAndLandingPageReviewStatus;
  }
  /**
   * @return self::CREATIVE_AND_LANDING_PAGE_REVIEW_STATUS_*
   */
  public function getCreativeAndLandingPageReviewStatus()
  {
    return $this->creativeAndLandingPageReviewStatus;
  }
  /**
   * Exchange review statuses for the creative.
   *
   * @param ExchangeReviewStatus[] $exchangeReviewStatuses
   */
  public function setExchangeReviewStatuses($exchangeReviewStatuses)
  {
    $this->exchangeReviewStatuses = $exchangeReviewStatuses;
  }
  /**
   * @return ExchangeReviewStatus[]
   */
  public function getExchangeReviewStatuses()
  {
    return $this->exchangeReviewStatuses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReviewStatusInfo::class, 'Google_Service_DisplayVideo_ReviewStatusInfo');
