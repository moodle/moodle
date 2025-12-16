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

class CheckoutSettings extends \Google\Model
{
  /**
   * Default enrollment state when enrollment state is not specified.
   */
  public const EFFECTIVE_ENROLLMENT_STATE_CHECKOUT_ON_MERCHANT_ENROLLMENT_STATE_UNSPECIFIED = 'CHECKOUT_ON_MERCHANT_ENROLLMENT_STATE_UNSPECIFIED';
  /**
   * Merchant has not enrolled into the feature.
   */
  public const EFFECTIVE_ENROLLMENT_STATE_INACTIVE = 'INACTIVE';
  /**
   * Merchant has enrolled into the feature by providing either an account level
   * URL or checkout URLs as part of their feed.
   */
  public const EFFECTIVE_ENROLLMENT_STATE_ENROLLED = 'ENROLLED';
  /**
   * Merchant has previously enrolled but opted out of the feature.
   */
  public const EFFECTIVE_ENROLLMENT_STATE_OPT_OUT = 'OPT_OUT';
  /**
   * Default review state when review state is not specified.
   */
  public const EFFECTIVE_REVIEW_STATE_CHECKOUT_ON_MERCHANT_REVIEW_STATE_UNSPECIFIED = 'CHECKOUT_ON_MERCHANT_REVIEW_STATE_UNSPECIFIED';
  /**
   * Merchant provided URLs are being reviewed for data quality issues.
   */
  public const EFFECTIVE_REVIEW_STATE_IN_REVIEW = 'IN_REVIEW';
  /**
   * Merchant account has been approved. Indicates the data quality checks have
   * passed.
   */
  public const EFFECTIVE_REVIEW_STATE_APPROVED = 'APPROVED';
  /**
   * Merchant account has been disapproved due to data quality issues.
   */
  public const EFFECTIVE_REVIEW_STATE_DISAPPROVED = 'DISAPPROVED';
  /**
   * Default enrollment state when enrollment state is not specified.
   */
  public const ENROLLMENT_STATE_CHECKOUT_ON_MERCHANT_ENROLLMENT_STATE_UNSPECIFIED = 'CHECKOUT_ON_MERCHANT_ENROLLMENT_STATE_UNSPECIFIED';
  /**
   * Merchant has not enrolled into the feature.
   */
  public const ENROLLMENT_STATE_INACTIVE = 'INACTIVE';
  /**
   * Merchant has enrolled into the feature by providing either an account level
   * URL or checkout URLs as part of their feed.
   */
  public const ENROLLMENT_STATE_ENROLLED = 'ENROLLED';
  /**
   * Merchant has previously enrolled but opted out of the feature.
   */
  public const ENROLLMENT_STATE_OPT_OUT = 'OPT_OUT';
  /**
   * Default review state when review state is not specified.
   */
  public const REVIEW_STATE_CHECKOUT_ON_MERCHANT_REVIEW_STATE_UNSPECIFIED = 'CHECKOUT_ON_MERCHANT_REVIEW_STATE_UNSPECIFIED';
  /**
   * Merchant provided URLs are being reviewed for data quality issues.
   */
  public const REVIEW_STATE_IN_REVIEW = 'IN_REVIEW';
  /**
   * Merchant account has been approved. Indicates the data quality checks have
   * passed.
   */
  public const REVIEW_STATE_APPROVED = 'APPROVED';
  /**
   * Merchant account has been disapproved due to data quality issues.
   */
  public const REVIEW_STATE_DISAPPROVED = 'DISAPPROVED';
  /**
   * Output only. The effective value of enrollment state for a given merchant
   * ID. If account level settings are present then this value will be a copy of
   * the account level settings. Otherwise, it will have the value of the parent
   * account.
   *
   * @var string
   */
  public $effectiveEnrollmentState;
  /**
   * Output only. The effective value of review state for a given merchant ID.
   * If account level settings are present then this value will be a copy of the
   * account level settings. Otherwise, it will have the value of the parent
   * account.
   *
   * @var string
   */
  public $effectiveReviewState;
  protected $effectiveUriSettingsType = UrlSettings::class;
  protected $effectiveUriSettingsDataType = '';
  /**
   * Output only. Reflects the merchant enrollment state in `Checkout` feature.
   *
   * @var string
   */
  public $enrollmentState;
  /**
   * Required. The ID of the account.
   *
   * @var string
   */
  public $merchantId;
  /**
   * Output only. Reflects the merchant review state in `Checkout` feature. This
   * is set based on the data quality reviews of the URL provided by the
   * merchant. A merchant with enrollment state as `ENROLLED` can be in the
   * following review states: `IN_REVIEW`, `APPROVED` or `DISAPPROVED`. A
   * merchant must be in an enrollment_state of `ENROLLED` before a review can
   * begin for the merchant.
   *
   * @var string
   */
  public $reviewState;
  protected $uriSettingsType = UrlSettings::class;
  protected $uriSettingsDataType = '';

  /**
   * Output only. The effective value of enrollment state for a given merchant
   * ID. If account level settings are present then this value will be a copy of
   * the account level settings. Otherwise, it will have the value of the parent
   * account.
   *
   * Accepted values: CHECKOUT_ON_MERCHANT_ENROLLMENT_STATE_UNSPECIFIED,
   * INACTIVE, ENROLLED, OPT_OUT
   *
   * @param self::EFFECTIVE_ENROLLMENT_STATE_* $effectiveEnrollmentState
   */
  public function setEffectiveEnrollmentState($effectiveEnrollmentState)
  {
    $this->effectiveEnrollmentState = $effectiveEnrollmentState;
  }
  /**
   * @return self::EFFECTIVE_ENROLLMENT_STATE_*
   */
  public function getEffectiveEnrollmentState()
  {
    return $this->effectiveEnrollmentState;
  }
  /**
   * Output only. The effective value of review state for a given merchant ID.
   * If account level settings are present then this value will be a copy of the
   * account level settings. Otherwise, it will have the value of the parent
   * account.
   *
   * Accepted values: CHECKOUT_ON_MERCHANT_REVIEW_STATE_UNSPECIFIED, IN_REVIEW,
   * APPROVED, DISAPPROVED
   *
   * @param self::EFFECTIVE_REVIEW_STATE_* $effectiveReviewState
   */
  public function setEffectiveReviewState($effectiveReviewState)
  {
    $this->effectiveReviewState = $effectiveReviewState;
  }
  /**
   * @return self::EFFECTIVE_REVIEW_STATE_*
   */
  public function getEffectiveReviewState()
  {
    return $this->effectiveReviewState;
  }
  /**
   * The effective value of `url_settings` for a given merchant ID. If account
   * level settings are present then this value will be a copy of the account
   * level settings. Otherwise, it will have the value of the parent account.
   *
   * @param UrlSettings $effectiveUriSettings
   */
  public function setEffectiveUriSettings(UrlSettings $effectiveUriSettings)
  {
    $this->effectiveUriSettings = $effectiveUriSettings;
  }
  /**
   * @return UrlSettings
   */
  public function getEffectiveUriSettings()
  {
    return $this->effectiveUriSettings;
  }
  /**
   * Output only. Reflects the merchant enrollment state in `Checkout` feature.
   *
   * Accepted values: CHECKOUT_ON_MERCHANT_ENROLLMENT_STATE_UNSPECIFIED,
   * INACTIVE, ENROLLED, OPT_OUT
   *
   * @param self::ENROLLMENT_STATE_* $enrollmentState
   */
  public function setEnrollmentState($enrollmentState)
  {
    $this->enrollmentState = $enrollmentState;
  }
  /**
   * @return self::ENROLLMENT_STATE_*
   */
  public function getEnrollmentState()
  {
    return $this->enrollmentState;
  }
  /**
   * Required. The ID of the account.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * Output only. Reflects the merchant review state in `Checkout` feature. This
   * is set based on the data quality reviews of the URL provided by the
   * merchant. A merchant with enrollment state as `ENROLLED` can be in the
   * following review states: `IN_REVIEW`, `APPROVED` or `DISAPPROVED`. A
   * merchant must be in an enrollment_state of `ENROLLED` before a review can
   * begin for the merchant.
   *
   * Accepted values: CHECKOUT_ON_MERCHANT_REVIEW_STATE_UNSPECIFIED, IN_REVIEW,
   * APPROVED, DISAPPROVED
   *
   * @param self::REVIEW_STATE_* $reviewState
   */
  public function setReviewState($reviewState)
  {
    $this->reviewState = $reviewState;
  }
  /**
   * @return self::REVIEW_STATE_*
   */
  public function getReviewState()
  {
    return $this->reviewState;
  }
  /**
   * URL settings for cart or checkout URL.
   *
   * @param UrlSettings $uriSettings
   */
  public function setUriSettings(UrlSettings $uriSettings)
  {
    $this->uriSettings = $uriSettings;
  }
  /**
   * @return UrlSettings
   */
  public function getUriSettings()
  {
    return $this->uriSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckoutSettings::class, 'Google_Service_ShoppingContent_CheckoutSettings');
