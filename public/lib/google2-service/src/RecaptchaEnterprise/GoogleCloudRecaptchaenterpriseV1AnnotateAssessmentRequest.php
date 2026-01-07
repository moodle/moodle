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

class GoogleCloudRecaptchaenterpriseV1AnnotateAssessmentRequest extends \Google\Collection
{
  /**
   * Default unspecified type.
   */
  public const ANNOTATION_ANNOTATION_UNSPECIFIED = 'ANNOTATION_UNSPECIFIED';
  /**
   * Provides information that the event turned out to be legitimate.
   */
  public const ANNOTATION_LEGITIMATE = 'LEGITIMATE';
  /**
   * Provides information that the event turned out to be fraudulent.
   */
  public const ANNOTATION_FRAUDULENT = 'FRAUDULENT';
  /**
   * Provides information that the event was related to a login event in which
   * the user typed the correct password. Deprecated, prefer indicating
   * CORRECT_PASSWORD through the reasons field instead.
   *
   * @deprecated
   */
  public const ANNOTATION_PASSWORD_CORRECT = 'PASSWORD_CORRECT';
  /**
   * Provides information that the event was related to a login event in which
   * the user typed the incorrect password. Deprecated, prefer indicating
   * INCORRECT_PASSWORD through the reasons field instead.
   *
   * @deprecated
   */
  public const ANNOTATION_PASSWORD_INCORRECT = 'PASSWORD_INCORRECT';
  protected $collection_key = 'reasons';
  /**
   * Optional. A stable account identifier to apply to the assessment. This is
   * an alternative to setting `account_id` in `CreateAssessment`, for example
   * when a stable account identifier is not yet known in the initial request.
   *
   * @var string
   */
  public $accountId;
  /**
   * Optional. The annotation that is assigned to the Event. This field can be
   * left empty to provide reasons that apply to an event without concluding
   * whether the event is legitimate or fraudulent.
   *
   * @var string
   */
  public $annotation;
  /**
   * Optional. A stable hashed account identifier to apply to the assessment.
   * This is an alternative to setting `hashed_account_id` in
   * `CreateAssessment`, for example when a stable account identifier is not yet
   * known in the initial request.
   *
   * @var string
   */
  public $hashedAccountId;
  protected $phoneAuthenticationEventType = GoogleCloudRecaptchaenterpriseV1PhoneAuthenticationEvent::class;
  protected $phoneAuthenticationEventDataType = '';
  /**
   * Optional. Reasons for the annotation that are assigned to the event.
   *
   * @var string[]
   */
  public $reasons;
  protected $transactionEventType = GoogleCloudRecaptchaenterpriseV1TransactionEvent::class;
  protected $transactionEventDataType = '';

  /**
   * Optional. A stable account identifier to apply to the assessment. This is
   * an alternative to setting `account_id` in `CreateAssessment`, for example
   * when a stable account identifier is not yet known in the initial request.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Optional. The annotation that is assigned to the Event. This field can be
   * left empty to provide reasons that apply to an event without concluding
   * whether the event is legitimate or fraudulent.
   *
   * Accepted values: ANNOTATION_UNSPECIFIED, LEGITIMATE, FRAUDULENT,
   * PASSWORD_CORRECT, PASSWORD_INCORRECT
   *
   * @param self::ANNOTATION_* $annotation
   */
  public function setAnnotation($annotation)
  {
    $this->annotation = $annotation;
  }
  /**
   * @return self::ANNOTATION_*
   */
  public function getAnnotation()
  {
    return $this->annotation;
  }
  /**
   * Optional. A stable hashed account identifier to apply to the assessment.
   * This is an alternative to setting `hashed_account_id` in
   * `CreateAssessment`, for example when a stable account identifier is not yet
   * known in the initial request.
   *
   * @param string $hashedAccountId
   */
  public function setHashedAccountId($hashedAccountId)
  {
    $this->hashedAccountId = $hashedAccountId;
  }
  /**
   * @return string
   */
  public function getHashedAccountId()
  {
    return $this->hashedAccountId;
  }
  /**
   * Optional. If using an external multi-factor authentication provider,
   * provide phone authentication details for fraud detection purposes.
   *
   * @param GoogleCloudRecaptchaenterpriseV1PhoneAuthenticationEvent $phoneAuthenticationEvent
   */
  public function setPhoneAuthenticationEvent(GoogleCloudRecaptchaenterpriseV1PhoneAuthenticationEvent $phoneAuthenticationEvent)
  {
    $this->phoneAuthenticationEvent = $phoneAuthenticationEvent;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1PhoneAuthenticationEvent
   */
  public function getPhoneAuthenticationEvent()
  {
    return $this->phoneAuthenticationEvent;
  }
  /**
   * Optional. Reasons for the annotation that are assigned to the event.
   *
   * @param string[] $reasons
   */
  public function setReasons($reasons)
  {
    $this->reasons = $reasons;
  }
  /**
   * @return string[]
   */
  public function getReasons()
  {
    return $this->reasons;
  }
  /**
   * Optional. If the assessment is part of a payment transaction, provide
   * details on payment lifecycle events that occur in the transaction.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TransactionEvent $transactionEvent
   */
  public function setTransactionEvent(GoogleCloudRecaptchaenterpriseV1TransactionEvent $transactionEvent)
  {
    $this->transactionEvent = $transactionEvent;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TransactionEvent
   */
  public function getTransactionEvent()
  {
    return $this->transactionEvent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1AnnotateAssessmentRequest::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1AnnotateAssessmentRequest');
