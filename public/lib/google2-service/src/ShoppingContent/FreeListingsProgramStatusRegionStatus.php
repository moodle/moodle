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

namespace Google\Service\ShoppingContent;

class FreeListingsProgramStatusRegionStatus extends \Google\Collection
{
  /**
   * State is not known.
   */
  public const ELIGIBILITY_STATUS_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * If the account has no issues and review is completed successfully.
   */
  public const ELIGIBILITY_STATUS_APPROVED = 'APPROVED';
  /**
   * There are one or more issues that needs to be resolved for account to be
   * active for the program. Detailed list of account issues are available in
   * [accountstatuses](https://developers.google.com/shopping-
   * content/reference/rest/v2.1/accountstatuses) API.
   */
  public const ELIGIBILITY_STATUS_DISAPPROVED = 'DISAPPROVED';
  /**
   * If account has issues but offers are servable. Some of the issue can make
   * account DISAPPROVED after a certain deadline.
   */
  public const ELIGIBILITY_STATUS_WARNING = 'WARNING';
  /**
   * Account is under review.
   */
  public const ELIGIBILITY_STATUS_UNDER_REVIEW = 'UNDER_REVIEW';
  /**
   * Account is waiting for review to start.
   */
  public const ELIGIBILITY_STATUS_PENDING_REVIEW = 'PENDING_REVIEW';
  /**
   * Program is currently onboarding. Upload valid offers to complete
   * onboarding.
   */
  public const ELIGIBILITY_STATUS_ONBOARDING = 'ONBOARDING';
  /**
   * Review eligibility state is unknown.
   */
  public const REVIEW_ELIGIBILITY_STATUS_REVIEW_ELIGIBILITY_UNSPECIFIED = 'REVIEW_ELIGIBILITY_UNSPECIFIED';
  /**
   * Account is eligible for review for a specified region code.
   */
  public const REVIEW_ELIGIBILITY_STATUS_ELIGIBLE = 'ELIGIBLE';
  /**
   * Account is not eligible for review for a specified region code.
   */
  public const REVIEW_ELIGIBILITY_STATUS_INELIGIBLE = 'INELIGIBLE';
  /**
   * Requesting a review from Google is not possible.
   */
  public const REVIEW_INELIGIBILITY_REASON_REVIEW_INELIGIBILITY_REASON_UNSPECIFIED = 'REVIEW_INELIGIBILITY_REASON_UNSPECIFIED';
  /**
   * All onboarding issues needs to be fixed.
   */
  public const REVIEW_INELIGIBILITY_REASON_ONBOARDING_ISSUES = 'ONBOARDING_ISSUES';
  /**
   * Not enough offers uploaded for this country.
   */
  public const REVIEW_INELIGIBILITY_REASON_NOT_ENOUGH_OFFERS = 'NOT_ENOUGH_OFFERS';
  /**
   * Cooldown period applies. Wait until cooldown period ends.
   */
  public const REVIEW_INELIGIBILITY_REASON_IN_COOLDOWN_PERIOD = 'IN_COOLDOWN_PERIOD';
  /**
   * Account is already under review.
   */
  public const REVIEW_INELIGIBILITY_REASON_ALREADY_UNDER_REVIEW = 'ALREADY_UNDER_REVIEW';
  /**
   * No issues available to review.
   */
  public const REVIEW_INELIGIBILITY_REASON_NO_REVIEW_REQUIRED = 'NO_REVIEW_REQUIRED';
  /**
   * Account will be automatically reviewed at the end of the grace period.
   */
  public const REVIEW_INELIGIBILITY_REASON_WILL_BE_REVIEWED_AUTOMATICALLY = 'WILL_BE_REVIEWED_AUTOMATICALLY';
  /**
   * Account is retired. Should not appear in MC.
   */
  public const REVIEW_INELIGIBILITY_REASON_IS_RETIRED = 'IS_RETIRED';
  /**
   * Account has already been reviewed. You can't request further reviews.
   */
  public const REVIEW_INELIGIBILITY_REASON_ALREADY_REVIEWED = 'ALREADY_REVIEWED';
  protected $collection_key = 'reviewIssues';
  /**
   * Date by which eligibilityStatus will go from `WARNING` to `DISAPPROVED`.
   * Only visible when your eligibilityStatus is WARNING. In [ISO
   * 8601](https://en.wikipedia.org/wiki/ISO_8601) format: `YYYY-MM-DD`.
   *
   * @var string
   */
  public $disapprovalDate;
  /**
   * Eligibility status of the standard free listing program.
   *
   * @var string
   */
  public $eligibilityStatus;
  /**
   * Issues that must be fixed to be eligible for review.
   *
   * @var string[]
   */
  public $onboardingIssues;
  /**
   * The two-letter [ISO 3166-1
   * alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2) codes for all
   * the regions with the same `eligibilityStatus` and `reviewEligibility`.
   *
   * @var string[]
   */
  public $regionCodes;
  /**
   * If a program is eligible for review in a specific region. Only visible if
   * `eligibilityStatus` is `DISAPPROVED`.
   *
   * @var string
   */
  public $reviewEligibilityStatus;
  /**
   * Review ineligibility reason if account is not eligible for review.
   *
   * @var string
   */
  public $reviewIneligibilityReason;
  /**
   * Reason a program in a specific region isn’t eligible for review. Only
   * visible if `reviewEligibilityStatus` is `INELIGIBLE`.
   *
   * @var string
   */
  public $reviewIneligibilityReasonDescription;
  protected $reviewIneligibilityReasonDetailsType = FreeListingsProgramStatusReviewIneligibilityReasonDetails::class;
  protected $reviewIneligibilityReasonDetailsDataType = '';
  /**
   * Issues evaluated in the review process. Fix all issues before requesting a
   * review.
   *
   * @var string[]
   */
  public $reviewIssues;

  /**
   * Date by which eligibilityStatus will go from `WARNING` to `DISAPPROVED`.
   * Only visible when your eligibilityStatus is WARNING. In [ISO
   * 8601](https://en.wikipedia.org/wiki/ISO_8601) format: `YYYY-MM-DD`.
   *
   * @param string $disapprovalDate
   */
  public function setDisapprovalDate($disapprovalDate)
  {
    $this->disapprovalDate = $disapprovalDate;
  }
  /**
   * @return string
   */
  public function getDisapprovalDate()
  {
    return $this->disapprovalDate;
  }
  /**
   * Eligibility status of the standard free listing program.
   *
   * Accepted values: STATE_UNSPECIFIED, APPROVED, DISAPPROVED, WARNING,
   * UNDER_REVIEW, PENDING_REVIEW, ONBOARDING
   *
   * @param self::ELIGIBILITY_STATUS_* $eligibilityStatus
   */
  public function setEligibilityStatus($eligibilityStatus)
  {
    $this->eligibilityStatus = $eligibilityStatus;
  }
  /**
   * @return self::ELIGIBILITY_STATUS_*
   */
  public function getEligibilityStatus()
  {
    return $this->eligibilityStatus;
  }
  /**
   * Issues that must be fixed to be eligible for review.
   *
   * @param string[] $onboardingIssues
   */
  public function setOnboardingIssues($onboardingIssues)
  {
    $this->onboardingIssues = $onboardingIssues;
  }
  /**
   * @return string[]
   */
  public function getOnboardingIssues()
  {
    return $this->onboardingIssues;
  }
  /**
   * The two-letter [ISO 3166-1
   * alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2) codes for all
   * the regions with the same `eligibilityStatus` and `reviewEligibility`.
   *
   * @param string[] $regionCodes
   */
  public function setRegionCodes($regionCodes)
  {
    $this->regionCodes = $regionCodes;
  }
  /**
   * @return string[]
   */
  public function getRegionCodes()
  {
    return $this->regionCodes;
  }
  /**
   * If a program is eligible for review in a specific region. Only visible if
   * `eligibilityStatus` is `DISAPPROVED`.
   *
   * Accepted values: REVIEW_ELIGIBILITY_UNSPECIFIED, ELIGIBLE, INELIGIBLE
   *
   * @param self::REVIEW_ELIGIBILITY_STATUS_* $reviewEligibilityStatus
   */
  public function setReviewEligibilityStatus($reviewEligibilityStatus)
  {
    $this->reviewEligibilityStatus = $reviewEligibilityStatus;
  }
  /**
   * @return self::REVIEW_ELIGIBILITY_STATUS_*
   */
  public function getReviewEligibilityStatus()
  {
    return $this->reviewEligibilityStatus;
  }
  /**
   * Review ineligibility reason if account is not eligible for review.
   *
   * Accepted values: REVIEW_INELIGIBILITY_REASON_UNSPECIFIED,
   * ONBOARDING_ISSUES, NOT_ENOUGH_OFFERS, IN_COOLDOWN_PERIOD,
   * ALREADY_UNDER_REVIEW, NO_REVIEW_REQUIRED, WILL_BE_REVIEWED_AUTOMATICALLY,
   * IS_RETIRED, ALREADY_REVIEWED
   *
   * @param self::REVIEW_INELIGIBILITY_REASON_* $reviewIneligibilityReason
   */
  public function setReviewIneligibilityReason($reviewIneligibilityReason)
  {
    $this->reviewIneligibilityReason = $reviewIneligibilityReason;
  }
  /**
   * @return self::REVIEW_INELIGIBILITY_REASON_*
   */
  public function getReviewIneligibilityReason()
  {
    return $this->reviewIneligibilityReason;
  }
  /**
   * Reason a program in a specific region isn’t eligible for review. Only
   * visible if `reviewEligibilityStatus` is `INELIGIBLE`.
   *
   * @param string $reviewIneligibilityReasonDescription
   */
  public function setReviewIneligibilityReasonDescription($reviewIneligibilityReasonDescription)
  {
    $this->reviewIneligibilityReasonDescription = $reviewIneligibilityReasonDescription;
  }
  /**
   * @return string
   */
  public function getReviewIneligibilityReasonDescription()
  {
    return $this->reviewIneligibilityReasonDescription;
  }
  /**
   * Additional information for ineligibility. If `reviewIneligibilityReason` is
   * `IN_COOLDOWN_PERIOD`, a timestamp for the end of the cooldown period is
   * provided.
   *
   * @param FreeListingsProgramStatusReviewIneligibilityReasonDetails $reviewIneligibilityReasonDetails
   */
  public function setReviewIneligibilityReasonDetails(FreeListingsProgramStatusReviewIneligibilityReasonDetails $reviewIneligibilityReasonDetails)
  {
    $this->reviewIneligibilityReasonDetails = $reviewIneligibilityReasonDetails;
  }
  /**
   * @return FreeListingsProgramStatusReviewIneligibilityReasonDetails
   */
  public function getReviewIneligibilityReasonDetails()
  {
    return $this->reviewIneligibilityReasonDetails;
  }
  /**
   * Issues evaluated in the review process. Fix all issues before requesting a
   * review.
   *
   * @param string[] $reviewIssues
   */
  public function setReviewIssues($reviewIssues)
  {
    $this->reviewIssues = $reviewIssues;
  }
  /**
   * @return string[]
   */
  public function getReviewIssues()
  {
    return $this->reviewIssues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FreeListingsProgramStatusRegionStatus::class, 'Google_Service_ShoppingContent_FreeListingsProgramStatusRegionStatus');
