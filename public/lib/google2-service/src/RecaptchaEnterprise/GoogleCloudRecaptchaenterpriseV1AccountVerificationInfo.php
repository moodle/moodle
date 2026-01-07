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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1AccountVerificationInfo extends \Google\Collection
{
  /**
   * No information about the latest account verification.
   */
  public const LATEST_VERIFICATION_RESULT_RESULT_UNSPECIFIED = 'RESULT_UNSPECIFIED';
  /**
   * The user was successfully verified. This means the account verification
   * challenge was successfully completed.
   */
  public const LATEST_VERIFICATION_RESULT_SUCCESS_USER_VERIFIED = 'SUCCESS_USER_VERIFIED';
  /**
   * The user failed the verification challenge.
   */
  public const LATEST_VERIFICATION_RESULT_ERROR_USER_NOT_VERIFIED = 'ERROR_USER_NOT_VERIFIED';
  /**
   * The site is not properly onboarded to use the account verification feature.
   */
  public const LATEST_VERIFICATION_RESULT_ERROR_SITE_ONBOARDING_INCOMPLETE = 'ERROR_SITE_ONBOARDING_INCOMPLETE';
  /**
   * The recipient is not allowed for account verification. This can occur
   * during integration but should not occur in production.
   */
  public const LATEST_VERIFICATION_RESULT_ERROR_RECIPIENT_NOT_ALLOWED = 'ERROR_RECIPIENT_NOT_ALLOWED';
  /**
   * The recipient has already been sent too many verification codes in a short
   * amount of time.
   */
  public const LATEST_VERIFICATION_RESULT_ERROR_RECIPIENT_ABUSE_LIMIT_EXHAUSTED = 'ERROR_RECIPIENT_ABUSE_LIMIT_EXHAUSTED';
  /**
   * The verification flow could not be completed due to a critical internal
   * error.
   */
  public const LATEST_VERIFICATION_RESULT_ERROR_CRITICAL_INTERNAL = 'ERROR_CRITICAL_INTERNAL';
  /**
   * The client has exceeded their two factor request quota for this period of
   * time.
   */
  public const LATEST_VERIFICATION_RESULT_ERROR_CUSTOMER_QUOTA_EXHAUSTED = 'ERROR_CUSTOMER_QUOTA_EXHAUSTED';
  /**
   * The request cannot be processed at the time because of an incident. This
   * bypass can be restricted to a problematic destination email domain, a
   * customer, or could affect the entire service.
   */
  public const LATEST_VERIFICATION_RESULT_ERROR_VERIFICATION_BYPASSED = 'ERROR_VERIFICATION_BYPASSED';
  /**
   * The request parameters do not match with the token provided and cannot be
   * processed.
   */
  public const LATEST_VERIFICATION_RESULT_ERROR_VERDICT_MISMATCH = 'ERROR_VERDICT_MISMATCH';
  protected $collection_key = 'endpoints';
  protected $endpointsType = GoogleCloudRecaptchaenterpriseV1EndpointVerificationInfo::class;
  protected $endpointsDataType = 'array';
  /**
   * Optional. Language code preference for the verification message, set as a
   * IETF BCP 47 language code.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. Result of the latest account verification challenge.
   *
   * @var string
   */
  public $latestVerificationResult;
  /**
   * Username of the account that is being verified. Deprecated. Customers
   * should now provide the `account_id` field in `event.user_info`.
   *
   * @deprecated
   * @var string
   */
  public $username;

  /**
   * Optional. Endpoints that can be used for identity verification.
   *
   * @param GoogleCloudRecaptchaenterpriseV1EndpointVerificationInfo[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1EndpointVerificationInfo[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Optional. Language code preference for the verification message, set as a
   * IETF BCP 47 language code.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Output only. Result of the latest account verification challenge.
   *
   * Accepted values: RESULT_UNSPECIFIED, SUCCESS_USER_VERIFIED,
   * ERROR_USER_NOT_VERIFIED, ERROR_SITE_ONBOARDING_INCOMPLETE,
   * ERROR_RECIPIENT_NOT_ALLOWED, ERROR_RECIPIENT_ABUSE_LIMIT_EXHAUSTED,
   * ERROR_CRITICAL_INTERNAL, ERROR_CUSTOMER_QUOTA_EXHAUSTED,
   * ERROR_VERIFICATION_BYPASSED, ERROR_VERDICT_MISMATCH
   *
   * @param self::LATEST_VERIFICATION_RESULT_* $latestVerificationResult
   */
  public function setLatestVerificationResult($latestVerificationResult)
  {
    $this->latestVerificationResult = $latestVerificationResult;
  }
  /**
   * @return self::LATEST_VERIFICATION_RESULT_*
   */
  public function getLatestVerificationResult()
  {
    return $this->latestVerificationResult;
  }
  /**
   * Username of the account that is being verified. Deprecated. Customers
   * should now provide the `account_id` field in `event.user_info`.
   *
   * @deprecated
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1AccountVerificationInfo::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1AccountVerificationInfo');
