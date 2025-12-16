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

class GoogleCloudRecaptchaenterpriseV1Event extends \Google\Collection
{
  /**
   * Default, unspecified setting. `fraud_prevention_assessment` is returned if
   * `transaction_data` is present in `Event` and Fraud Prevention is enabled in
   * the Google Cloud console.
   */
  public const FRAUD_PREVENTION_FRAUD_PREVENTION_UNSPECIFIED = 'FRAUD_PREVENTION_UNSPECIFIED';
  /**
   * Enable Fraud Prevention for this assessment, if Fraud Prevention is enabled
   * in the Google Cloud console.
   */
  public const FRAUD_PREVENTION_ENABLED = 'ENABLED';
  /**
   * Disable Fraud Prevention for this assessment, regardless of the Google
   * Cloud console settings.
   */
  public const FRAUD_PREVENTION_DISABLED = 'DISABLED';
  protected $collection_key = 'headers';
  /**
   * Optional. The expected action for this type of event. This should be the
   * same action provided at token generation time on client-side platforms
   * already integrated with recaptcha enterprise.
   *
   * @var string
   */
  public $expectedAction;
  /**
   * Optional. Flag for a reCAPTCHA express request for an assessment without a
   * token. If enabled, `site_key` must reference an Express site key.
   *
   * @var bool
   */
  public $express;
  /**
   * Optional. Flag for enabling firewall policy config assessment. If this flag
   * is enabled, the firewall policy is evaluated and a suggested firewall
   * action is returned in the response.
   *
   * @var bool
   */
  public $firewallPolicyEvaluation;
  /**
   * Optional. The Fraud Prevention setting for this assessment.
   *
   * @var string
   */
  public $fraudPrevention;
  /**
   * Optional. Deprecated: use `user_info.account_id` instead. Unique stable
   * hashed user identifier for the request. The identifier must be hashed using
   * hmac-sha256 with stable secret.
   *
   * @deprecated
   * @var string
   */
  public $hashedAccountId;
  /**
   * Optional. HTTP header information about the request.
   *
   * @var string[]
   */
  public $headers;
  /**
   * Optional. JA3 fingerprint for SSL clients. To learn how to compute this
   * fingerprint, please refer to https://github.com/salesforce/ja3.
   *
   * @var string
   */
  public $ja3;
  /**
   * Optional. JA4 fingerprint for SSL clients. To learn how to compute this
   * fingerprint, please refer to https://github.com/FoxIO-LLC/ja4.
   *
   * @var string
   */
  public $ja4;
  /**
   * Optional. The URI resource the user requested that triggered an assessment.
   *
   * @var string
   */
  public $requestedUri;
  /**
   * Optional. The site key that was used to invoke reCAPTCHA Enterprise on your
   * site and generate the token.
   *
   * @var string
   */
  public $siteKey;
  /**
   * Optional. The user response token provided by the reCAPTCHA Enterprise
   * client-side integration on your site.
   *
   * @var string
   */
  public $token;
  protected $transactionDataType = GoogleCloudRecaptchaenterpriseV1TransactionData::class;
  protected $transactionDataDataType = '';
  /**
   * Optional. The user agent present in the request from the user's device
   * related to this event.
   *
   * @var string
   */
  public $userAgent;
  protected $userInfoType = GoogleCloudRecaptchaenterpriseV1UserInfo::class;
  protected $userInfoDataType = '';
  /**
   * Optional. The IP address in the request from the user's device related to
   * this event.
   *
   * @var string
   */
  public $userIpAddress;
  /**
   * Optional. Flag for running Web Application Firewall (WAF) token assessment.
   * If enabled, the token must be specified, and have been created by a WAF-
   * enabled key.
   *
   * @var bool
   */
  public $wafTokenAssessment;

  /**
   * Optional. The expected action for this type of event. This should be the
   * same action provided at token generation time on client-side platforms
   * already integrated with recaptcha enterprise.
   *
   * @param string $expectedAction
   */
  public function setExpectedAction($expectedAction)
  {
    $this->expectedAction = $expectedAction;
  }
  /**
   * @return string
   */
  public function getExpectedAction()
  {
    return $this->expectedAction;
  }
  /**
   * Optional. Flag for a reCAPTCHA express request for an assessment without a
   * token. If enabled, `site_key` must reference an Express site key.
   *
   * @param bool $express
   */
  public function setExpress($express)
  {
    $this->express = $express;
  }
  /**
   * @return bool
   */
  public function getExpress()
  {
    return $this->express;
  }
  /**
   * Optional. Flag for enabling firewall policy config assessment. If this flag
   * is enabled, the firewall policy is evaluated and a suggested firewall
   * action is returned in the response.
   *
   * @param bool $firewallPolicyEvaluation
   */
  public function setFirewallPolicyEvaluation($firewallPolicyEvaluation)
  {
    $this->firewallPolicyEvaluation = $firewallPolicyEvaluation;
  }
  /**
   * @return bool
   */
  public function getFirewallPolicyEvaluation()
  {
    return $this->firewallPolicyEvaluation;
  }
  /**
   * Optional. The Fraud Prevention setting for this assessment.
   *
   * Accepted values: FRAUD_PREVENTION_UNSPECIFIED, ENABLED, DISABLED
   *
   * @param self::FRAUD_PREVENTION_* $fraudPrevention
   */
  public function setFraudPrevention($fraudPrevention)
  {
    $this->fraudPrevention = $fraudPrevention;
  }
  /**
   * @return self::FRAUD_PREVENTION_*
   */
  public function getFraudPrevention()
  {
    return $this->fraudPrevention;
  }
  /**
   * Optional. Deprecated: use `user_info.account_id` instead. Unique stable
   * hashed user identifier for the request. The identifier must be hashed using
   * hmac-sha256 with stable secret.
   *
   * @deprecated
   * @param string $hashedAccountId
   */
  public function setHashedAccountId($hashedAccountId)
  {
    $this->hashedAccountId = $hashedAccountId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getHashedAccountId()
  {
    return $this->hashedAccountId;
  }
  /**
   * Optional. HTTP header information about the request.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Optional. JA3 fingerprint for SSL clients. To learn how to compute this
   * fingerprint, please refer to https://github.com/salesforce/ja3.
   *
   * @param string $ja3
   */
  public function setJa3($ja3)
  {
    $this->ja3 = $ja3;
  }
  /**
   * @return string
   */
  public function getJa3()
  {
    return $this->ja3;
  }
  /**
   * Optional. JA4 fingerprint for SSL clients. To learn how to compute this
   * fingerprint, please refer to https://github.com/FoxIO-LLC/ja4.
   *
   * @param string $ja4
   */
  public function setJa4($ja4)
  {
    $this->ja4 = $ja4;
  }
  /**
   * @return string
   */
  public function getJa4()
  {
    return $this->ja4;
  }
  /**
   * Optional. The URI resource the user requested that triggered an assessment.
   *
   * @param string $requestedUri
   */
  public function setRequestedUri($requestedUri)
  {
    $this->requestedUri = $requestedUri;
  }
  /**
   * @return string
   */
  public function getRequestedUri()
  {
    return $this->requestedUri;
  }
  /**
   * Optional. The site key that was used to invoke reCAPTCHA Enterprise on your
   * site and generate the token.
   *
   * @param string $siteKey
   */
  public function setSiteKey($siteKey)
  {
    $this->siteKey = $siteKey;
  }
  /**
   * @return string
   */
  public function getSiteKey()
  {
    return $this->siteKey;
  }
  /**
   * Optional. The user response token provided by the reCAPTCHA Enterprise
   * client-side integration on your site.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * Optional. Data describing a payment transaction to be assessed. Sending
   * this data enables reCAPTCHA Enterprise Fraud Prevention and the
   * FraudPreventionAssessment component in the response.
   *
   * @param GoogleCloudRecaptchaenterpriseV1TransactionData $transactionData
   */
  public function setTransactionData(GoogleCloudRecaptchaenterpriseV1TransactionData $transactionData)
  {
    $this->transactionData = $transactionData;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1TransactionData
   */
  public function getTransactionData()
  {
    return $this->transactionData;
  }
  /**
   * Optional. The user agent present in the request from the user's device
   * related to this event.
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
  /**
   * Optional. Information about the user that generates this event, when they
   * can be identified. They are often identified through the use of an account
   * for logged-in requests or login/registration requests, or by providing user
   * identifiers for guest actions like checkout.
   *
   * @param GoogleCloudRecaptchaenterpriseV1UserInfo $userInfo
   */
  public function setUserInfo(GoogleCloudRecaptchaenterpriseV1UserInfo $userInfo)
  {
    $this->userInfo = $userInfo;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1UserInfo
   */
  public function getUserInfo()
  {
    return $this->userInfo;
  }
  /**
   * Optional. The IP address in the request from the user's device related to
   * this event.
   *
   * @param string $userIpAddress
   */
  public function setUserIpAddress($userIpAddress)
  {
    $this->userIpAddress = $userIpAddress;
  }
  /**
   * @return string
   */
  public function getUserIpAddress()
  {
    return $this->userIpAddress;
  }
  /**
   * Optional. Flag for running Web Application Firewall (WAF) token assessment.
   * If enabled, the token must be specified, and have been created by a WAF-
   * enabled key.
   *
   * @param bool $wafTokenAssessment
   */
  public function setWafTokenAssessment($wafTokenAssessment)
  {
    $this->wafTokenAssessment = $wafTokenAssessment;
  }
  /**
   * @return bool
   */
  public function getWafTokenAssessment()
  {
    return $this->wafTokenAssessment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1Event::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1Event');
