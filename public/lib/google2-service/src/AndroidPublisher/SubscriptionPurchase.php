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

namespace Google\Service\AndroidPublisher;

class SubscriptionPurchase extends \Google\Model
{
  /**
   * The acknowledgement state of the subscription product. Possible values are:
   * 0. Yet to be acknowledged 1. Acknowledged
   *
   * @var int
   */
  public $acknowledgementState;
  /**
   * Whether the subscription will automatically be renewed when it reaches its
   * current expiry time.
   *
   * @var bool
   */
  public $autoRenewing;
  /**
   * Time at which the subscription will be automatically resumed, in
   * milliseconds since the Epoch. Only present if the user has requested to
   * pause the subscription.
   *
   * @var string
   */
  public $autoResumeTimeMillis;
  /**
   * The reason why a subscription was canceled or is not auto-renewing.
   * Possible values are: 0. User canceled the subscription 1. Subscription was
   * canceled by the system, for example because of a billing problem 2.
   * Subscription was replaced with a new subscription 3. Subscription was
   * canceled by the developer
   *
   * @var int
   */
  public $cancelReason;
  protected $cancelSurveyResultType = SubscriptionCancelSurveyResult::class;
  protected $cancelSurveyResultDataType = '';
  /**
   * ISO 3166-1 alpha-2 billing country/region code of the user at the time the
   * subscription was granted.
   *
   * @var string
   */
  public $countryCode;
  /**
   * A developer-specified string that contains supplemental information about
   * an order.
   *
   * @var string
   */
  public $developerPayload;
  /**
   * The email address of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * Time at which the subscription will expire, in milliseconds since the
   * Epoch.
   *
   * @var string
   */
  public $expiryTimeMillis;
  /**
   * User account identifier in the third-party service. Only present if account
   * linking happened as part of the subscription purchase flow.
   *
   * @var string
   */
  public $externalAccountId;
  /**
   * The family name of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @var string
   */
  public $familyName;
  /**
   * The given name of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @var string
   */
  public $givenName;
  protected $introductoryPriceInfoType = IntroductoryPriceInfo::class;
  protected $introductoryPriceInfoDataType = '';
  /**
   * This kind represents a subscriptionPurchase object in the androidpublisher
   * service.
   *
   * @var string
   */
  public $kind;
  /**
   * The purchase token of the originating purchase if this subscription is one
   * of the following: 0. Re-signup of a canceled but non-lapsed subscription 1.
   * Upgrade/downgrade from a previous subscription For example, suppose a user
   * originally signs up and you receive purchase token X, then the user cancels
   * and goes through the resignup flow (before their subscription lapses) and
   * you receive purchase token Y, and finally the user upgrades their
   * subscription and you receive purchase token Z. If you call this API with
   * purchase token Z, this field will be set to Y. If you call this API with
   * purchase token Y, this field will be set to X. If you call this API with
   * purchase token X, this field will not be set.
   *
   * @var string
   */
  public $linkedPurchaseToken;
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * account in your app. Present for the following purchases: * If account
   * linking happened as part of the subscription purchase flow. * It was
   * specified using https://developer.android.com/reference/com/android/billing
   * client/api/BillingFlowParams.Builder#setobfuscatedaccountid when the
   * purchase was made.
   *
   * @var string
   */
  public $obfuscatedExternalAccountId;
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * profile in your app. Only present if specified using https://developer.andr
   * oid.com/reference/com/android/billingclient/api/BillingFlowParams.Builder#s
   * etobfuscatedprofileid when the purchase was made.
   *
   * @var string
   */
  public $obfuscatedExternalProfileId;
  /**
   * The order id of the latest recurring order associated with the purchase of
   * the subscription. If the subscription was canceled because payment was
   * declined, this will be the order id from the payment declined order.
   *
   * @var string
   */
  public $orderId;
  /**
   * The payment state of the subscription. Possible values are: 0. Payment
   * pending 1. Payment received 2. Free trial 3. Pending deferred
   * upgrade/downgrade Not present for canceled, expired subscriptions.
   *
   * @var int
   */
  public $paymentState;
  /**
   * Price of the subscription, For tax exclusive countries, the price doesn't
   * include tax. For tax inclusive countries, the price includes tax. Price is
   * expressed in micro-units, where 1,000,000 micro-units represents one unit
   * of the currency. For example, if the subscription price is €1.99,
   * price_amount_micros is 1990000.
   *
   * @var string
   */
  public $priceAmountMicros;
  protected $priceChangeType = SubscriptionPriceChange::class;
  protected $priceChangeDataType = '';
  /**
   * ISO 4217 currency code for the subscription price. For example, if the
   * price is specified in British pounds sterling, price_currency_code is
   * "GBP".
   *
   * @var string
   */
  public $priceCurrencyCode;
  /**
   * The Google profile id of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @var string
   */
  public $profileId;
  /**
   * The profile name of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @var string
   */
  public $profileName;
  /**
   * The promotion code applied on this purchase. This field is only set if a
   * vanity code promotion is applied when the subscription was purchased.
   *
   * @var string
   */
  public $promotionCode;
  /**
   * The type of promotion applied on this purchase. This field is only set if a
   * promotion is applied when the subscription was purchased. Possible values
   * are: 0. One time code 1. Vanity code
   *
   * @var int
   */
  public $promotionType;
  /**
   * The type of purchase of the subscription. This field is only set if this
   * purchase was not made using the standard in-app billing flow. Possible
   * values are: 0. Test (i.e. purchased from a license testing account) 1.
   * Promo (i.e. purchased using a promo code)
   *
   * @var int
   */
  public $purchaseType;
  /**
   * Time at which the subscription was granted, in milliseconds since the
   * Epoch.
   *
   * @var string
   */
  public $startTimeMillis;
  /**
   * The time at which the subscription was canceled by the user, in
   * milliseconds since the epoch. Only present if cancelReason is 0.
   *
   * @var string
   */
  public $userCancellationTimeMillis;

  /**
   * The acknowledgement state of the subscription product. Possible values are:
   * 0. Yet to be acknowledged 1. Acknowledged
   *
   * @param int $acknowledgementState
   */
  public function setAcknowledgementState($acknowledgementState)
  {
    $this->acknowledgementState = $acknowledgementState;
  }
  /**
   * @return int
   */
  public function getAcknowledgementState()
  {
    return $this->acknowledgementState;
  }
  /**
   * Whether the subscription will automatically be renewed when it reaches its
   * current expiry time.
   *
   * @param bool $autoRenewing
   */
  public function setAutoRenewing($autoRenewing)
  {
    $this->autoRenewing = $autoRenewing;
  }
  /**
   * @return bool
   */
  public function getAutoRenewing()
  {
    return $this->autoRenewing;
  }
  /**
   * Time at which the subscription will be automatically resumed, in
   * milliseconds since the Epoch. Only present if the user has requested to
   * pause the subscription.
   *
   * @param string $autoResumeTimeMillis
   */
  public function setAutoResumeTimeMillis($autoResumeTimeMillis)
  {
    $this->autoResumeTimeMillis = $autoResumeTimeMillis;
  }
  /**
   * @return string
   */
  public function getAutoResumeTimeMillis()
  {
    return $this->autoResumeTimeMillis;
  }
  /**
   * The reason why a subscription was canceled or is not auto-renewing.
   * Possible values are: 0. User canceled the subscription 1. Subscription was
   * canceled by the system, for example because of a billing problem 2.
   * Subscription was replaced with a new subscription 3. Subscription was
   * canceled by the developer
   *
   * @param int $cancelReason
   */
  public function setCancelReason($cancelReason)
  {
    $this->cancelReason = $cancelReason;
  }
  /**
   * @return int
   */
  public function getCancelReason()
  {
    return $this->cancelReason;
  }
  /**
   * Information provided by the user when they complete the subscription
   * cancellation flow (cancellation reason survey).
   *
   * @param SubscriptionCancelSurveyResult $cancelSurveyResult
   */
  public function setCancelSurveyResult(SubscriptionCancelSurveyResult $cancelSurveyResult)
  {
    $this->cancelSurveyResult = $cancelSurveyResult;
  }
  /**
   * @return SubscriptionCancelSurveyResult
   */
  public function getCancelSurveyResult()
  {
    return $this->cancelSurveyResult;
  }
  /**
   * ISO 3166-1 alpha-2 billing country/region code of the user at the time the
   * subscription was granted.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * A developer-specified string that contains supplemental information about
   * an order.
   *
   * @param string $developerPayload
   */
  public function setDeveloperPayload($developerPayload)
  {
    $this->developerPayload = $developerPayload;
  }
  /**
   * @return string
   */
  public function getDeveloperPayload()
  {
    return $this->developerPayload;
  }
  /**
   * The email address of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * Time at which the subscription will expire, in milliseconds since the
   * Epoch.
   *
   * @param string $expiryTimeMillis
   */
  public function setExpiryTimeMillis($expiryTimeMillis)
  {
    $this->expiryTimeMillis = $expiryTimeMillis;
  }
  /**
   * @return string
   */
  public function getExpiryTimeMillis()
  {
    return $this->expiryTimeMillis;
  }
  /**
   * User account identifier in the third-party service. Only present if account
   * linking happened as part of the subscription purchase flow.
   *
   * @param string $externalAccountId
   */
  public function setExternalAccountId($externalAccountId)
  {
    $this->externalAccountId = $externalAccountId;
  }
  /**
   * @return string
   */
  public function getExternalAccountId()
  {
    return $this->externalAccountId;
  }
  /**
   * The family name of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @param string $familyName
   */
  public function setFamilyName($familyName)
  {
    $this->familyName = $familyName;
  }
  /**
   * @return string
   */
  public function getFamilyName()
  {
    return $this->familyName;
  }
  /**
   * The given name of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @param string $givenName
   */
  public function setGivenName($givenName)
  {
    $this->givenName = $givenName;
  }
  /**
   * @return string
   */
  public function getGivenName()
  {
    return $this->givenName;
  }
  /**
   * Introductory price information of the subscription. This is only present
   * when the subscription was purchased with an introductory price. This field
   * does not indicate the subscription is currently in introductory price
   * period.
   *
   * @param IntroductoryPriceInfo $introductoryPriceInfo
   */
  public function setIntroductoryPriceInfo(IntroductoryPriceInfo $introductoryPriceInfo)
  {
    $this->introductoryPriceInfo = $introductoryPriceInfo;
  }
  /**
   * @return IntroductoryPriceInfo
   */
  public function getIntroductoryPriceInfo()
  {
    return $this->introductoryPriceInfo;
  }
  /**
   * This kind represents a subscriptionPurchase object in the androidpublisher
   * service.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The purchase token of the originating purchase if this subscription is one
   * of the following: 0. Re-signup of a canceled but non-lapsed subscription 1.
   * Upgrade/downgrade from a previous subscription For example, suppose a user
   * originally signs up and you receive purchase token X, then the user cancels
   * and goes through the resignup flow (before their subscription lapses) and
   * you receive purchase token Y, and finally the user upgrades their
   * subscription and you receive purchase token Z. If you call this API with
   * purchase token Z, this field will be set to Y. If you call this API with
   * purchase token Y, this field will be set to X. If you call this API with
   * purchase token X, this field will not be set.
   *
   * @param string $linkedPurchaseToken
   */
  public function setLinkedPurchaseToken($linkedPurchaseToken)
  {
    $this->linkedPurchaseToken = $linkedPurchaseToken;
  }
  /**
   * @return string
   */
  public function getLinkedPurchaseToken()
  {
    return $this->linkedPurchaseToken;
  }
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * account in your app. Present for the following purchases: * If account
   * linking happened as part of the subscription purchase flow. * It was
   * specified using https://developer.android.com/reference/com/android/billing
   * client/api/BillingFlowParams.Builder#setobfuscatedaccountid when the
   * purchase was made.
   *
   * @param string $obfuscatedExternalAccountId
   */
  public function setObfuscatedExternalAccountId($obfuscatedExternalAccountId)
  {
    $this->obfuscatedExternalAccountId = $obfuscatedExternalAccountId;
  }
  /**
   * @return string
   */
  public function getObfuscatedExternalAccountId()
  {
    return $this->obfuscatedExternalAccountId;
  }
  /**
   * An obfuscated version of the id that is uniquely associated with the user's
   * profile in your app. Only present if specified using https://developer.andr
   * oid.com/reference/com/android/billingclient/api/BillingFlowParams.Builder#s
   * etobfuscatedprofileid when the purchase was made.
   *
   * @param string $obfuscatedExternalProfileId
   */
  public function setObfuscatedExternalProfileId($obfuscatedExternalProfileId)
  {
    $this->obfuscatedExternalProfileId = $obfuscatedExternalProfileId;
  }
  /**
   * @return string
   */
  public function getObfuscatedExternalProfileId()
  {
    return $this->obfuscatedExternalProfileId;
  }
  /**
   * The order id of the latest recurring order associated with the purchase of
   * the subscription. If the subscription was canceled because payment was
   * declined, this will be the order id from the payment declined order.
   *
   * @param string $orderId
   */
  public function setOrderId($orderId)
  {
    $this->orderId = $orderId;
  }
  /**
   * @return string
   */
  public function getOrderId()
  {
    return $this->orderId;
  }
  /**
   * The payment state of the subscription. Possible values are: 0. Payment
   * pending 1. Payment received 2. Free trial 3. Pending deferred
   * upgrade/downgrade Not present for canceled, expired subscriptions.
   *
   * @param int $paymentState
   */
  public function setPaymentState($paymentState)
  {
    $this->paymentState = $paymentState;
  }
  /**
   * @return int
   */
  public function getPaymentState()
  {
    return $this->paymentState;
  }
  /**
   * Price of the subscription, For tax exclusive countries, the price doesn't
   * include tax. For tax inclusive countries, the price includes tax. Price is
   * expressed in micro-units, where 1,000,000 micro-units represents one unit
   * of the currency. For example, if the subscription price is €1.99,
   * price_amount_micros is 1990000.
   *
   * @param string $priceAmountMicros
   */
  public function setPriceAmountMicros($priceAmountMicros)
  {
    $this->priceAmountMicros = $priceAmountMicros;
  }
  /**
   * @return string
   */
  public function getPriceAmountMicros()
  {
    return $this->priceAmountMicros;
  }
  /**
   * The latest price change information available. This is present only when
   * there is an upcoming price change for the subscription yet to be applied.
   * Once the subscription renews with the new price or the subscription is
   * canceled, no price change information will be returned.
   *
   * @param SubscriptionPriceChange $priceChange
   */
  public function setPriceChange(SubscriptionPriceChange $priceChange)
  {
    $this->priceChange = $priceChange;
  }
  /**
   * @return SubscriptionPriceChange
   */
  public function getPriceChange()
  {
    return $this->priceChange;
  }
  /**
   * ISO 4217 currency code for the subscription price. For example, if the
   * price is specified in British pounds sterling, price_currency_code is
   * "GBP".
   *
   * @param string $priceCurrencyCode
   */
  public function setPriceCurrencyCode($priceCurrencyCode)
  {
    $this->priceCurrencyCode = $priceCurrencyCode;
  }
  /**
   * @return string
   */
  public function getPriceCurrencyCode()
  {
    return $this->priceCurrencyCode;
  }
  /**
   * The Google profile id of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @param string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  /**
   * @return string
   */
  public function getProfileId()
  {
    return $this->profileId;
  }
  /**
   * The profile name of the user when the subscription was purchased. Only
   * present for purchases made with 'Subscribe with Google'.
   *
   * @param string $profileName
   */
  public function setProfileName($profileName)
  {
    $this->profileName = $profileName;
  }
  /**
   * @return string
   */
  public function getProfileName()
  {
    return $this->profileName;
  }
  /**
   * The promotion code applied on this purchase. This field is only set if a
   * vanity code promotion is applied when the subscription was purchased.
   *
   * @param string $promotionCode
   */
  public function setPromotionCode($promotionCode)
  {
    $this->promotionCode = $promotionCode;
  }
  /**
   * @return string
   */
  public function getPromotionCode()
  {
    return $this->promotionCode;
  }
  /**
   * The type of promotion applied on this purchase. This field is only set if a
   * promotion is applied when the subscription was purchased. Possible values
   * are: 0. One time code 1. Vanity code
   *
   * @param int $promotionType
   */
  public function setPromotionType($promotionType)
  {
    $this->promotionType = $promotionType;
  }
  /**
   * @return int
   */
  public function getPromotionType()
  {
    return $this->promotionType;
  }
  /**
   * The type of purchase of the subscription. This field is only set if this
   * purchase was not made using the standard in-app billing flow. Possible
   * values are: 0. Test (i.e. purchased from a license testing account) 1.
   * Promo (i.e. purchased using a promo code)
   *
   * @param int $purchaseType
   */
  public function setPurchaseType($purchaseType)
  {
    $this->purchaseType = $purchaseType;
  }
  /**
   * @return int
   */
  public function getPurchaseType()
  {
    return $this->purchaseType;
  }
  /**
   * Time at which the subscription was granted, in milliseconds since the
   * Epoch.
   *
   * @param string $startTimeMillis
   */
  public function setStartTimeMillis($startTimeMillis)
  {
    $this->startTimeMillis = $startTimeMillis;
  }
  /**
   * @return string
   */
  public function getStartTimeMillis()
  {
    return $this->startTimeMillis;
  }
  /**
   * The time at which the subscription was canceled by the user, in
   * milliseconds since the epoch. Only present if cancelReason is 0.
   *
   * @param string $userCancellationTimeMillis
   */
  public function setUserCancellationTimeMillis($userCancellationTimeMillis)
  {
    $this->userCancellationTimeMillis = $userCancellationTimeMillis;
  }
  /**
   * @return string
   */
  public function getUserCancellationTimeMillis()
  {
    return $this->userCancellationTimeMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionPurchase::class, 'Google_Service_AndroidPublisher_SubscriptionPurchase');
