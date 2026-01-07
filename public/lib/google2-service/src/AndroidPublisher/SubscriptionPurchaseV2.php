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

class SubscriptionPurchaseV2 extends \Google\Collection
{
  /**
   * Unspecified acknowledgement state.
   */
  public const ACKNOWLEDGEMENT_STATE_ACKNOWLEDGEMENT_STATE_UNSPECIFIED = 'ACKNOWLEDGEMENT_STATE_UNSPECIFIED';
  /**
   * The subscription is not acknowledged yet.
   */
  public const ACKNOWLEDGEMENT_STATE_ACKNOWLEDGEMENT_STATE_PENDING = 'ACKNOWLEDGEMENT_STATE_PENDING';
  /**
   * The subscription is acknowledged.
   */
  public const ACKNOWLEDGEMENT_STATE_ACKNOWLEDGEMENT_STATE_ACKNOWLEDGED = 'ACKNOWLEDGEMENT_STATE_ACKNOWLEDGED';
  /**
   * Unspecified subscription state.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_UNSPECIFIED = 'SUBSCRIPTION_STATE_UNSPECIFIED';
  /**
   * Subscription was created but awaiting payment during signup. In this state,
   * all items are awaiting payment.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_PENDING = 'SUBSCRIPTION_STATE_PENDING';
  /**
   * Subscription is active. - (1) If the subscription is an auto renewing plan,
   * at least one item is auto_renew_enabled and not expired. - (2) If the
   * subscription is a prepaid plan, at least one item is not expired.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_ACTIVE = 'SUBSCRIPTION_STATE_ACTIVE';
  /**
   * Subscription is paused. The state is only available when the subscription
   * is an auto renewing plan. In this state, all items are in paused state.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_PAUSED = 'SUBSCRIPTION_STATE_PAUSED';
  /**
   * Subscription is in grace period. The state is only available when the
   * subscription is an auto renewing plan. In this state, all items are in
   * grace period.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_IN_GRACE_PERIOD = 'SUBSCRIPTION_STATE_IN_GRACE_PERIOD';
  /**
   * Subscription is on hold (suspended). The state is only available when the
   * subscription is an auto renewing plan. In this state, all items are on
   * hold.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_ON_HOLD = 'SUBSCRIPTION_STATE_ON_HOLD';
  /**
   * Subscription is canceled but not expired yet. The state is only available
   * when the subscription is an auto renewing plan. All items have
   * auto_renew_enabled set to false.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_CANCELED = 'SUBSCRIPTION_STATE_CANCELED';
  /**
   * Subscription is expired. All items have expiry_time in the past.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_EXPIRED = 'SUBSCRIPTION_STATE_EXPIRED';
  /**
   * Pending transaction for subscription is canceled. If this pending purchase
   * was for an existing subscription, use linked_purchase_token to get the
   * current state of that subscription.
   */
  public const SUBSCRIPTION_STATE_SUBSCRIPTION_STATE_PENDING_PURCHASE_CANCELED = 'SUBSCRIPTION_STATE_PENDING_PURCHASE_CANCELED';
  protected $collection_key = 'lineItems';
  /**
   * The acknowledgement state of the subscription.
   *
   * @var string
   */
  public $acknowledgementState;
  protected $canceledStateContextType = CanceledStateContext::class;
  protected $canceledStateContextDataType = '';
  protected $externalAccountIdentifiersType = ExternalAccountIdentifiers::class;
  protected $externalAccountIdentifiersDataType = '';
  /**
   * This kind represents a SubscriptionPurchaseV2 object in the
   * androidpublisher service.
   *
   * @var string
   */
  public $kind;
  /**
   * Deprecated: Use line_items.latest_successful_order_id instead. The order id
   * of the latest order associated with the purchase of the subscription. For
   * autoRenewing subscription, this is the order id of signup order if it is
   * not renewed yet, or the last recurring order id (success, pending, or
   * declined order). For prepaid subscription, this is the order id associated
   * with the queried purchase token.
   *
   * @deprecated
   * @var string
   */
  public $latestOrderId;
  protected $lineItemsType = SubscriptionPurchaseLineItem::class;
  protected $lineItemsDataType = 'array';
  /**
   * The purchase token of the old subscription if this subscription is one of
   * the following: * Re-signup of a canceled but non-lapsed subscription *
   * Upgrade/downgrade from a previous subscription. * Convert from prepaid to
   * auto renewing subscription. * Convert from an auto renewing subscription to
   * prepaid. * Topup a prepaid subscription.
   *
   * @var string
   */
  public $linkedPurchaseToken;
  protected $outOfAppPurchaseContextType = OutOfAppPurchaseContext::class;
  protected $outOfAppPurchaseContextDataType = '';
  protected $pausedStateContextType = PausedStateContext::class;
  protected $pausedStateContextDataType = '';
  /**
   * ISO 3166-1 alpha-2 billing country/region code of the user at the time the
   * subscription was granted.
   *
   * @var string
   */
  public $regionCode;
  /**
   * Time at which the subscription was granted. Not set for pending
   * subscriptions (subscription was created but awaiting payment during
   * signup).
   *
   * @var string
   */
  public $startTime;
  protected $subscribeWithGoogleInfoType = SubscribeWithGoogleInfo::class;
  protected $subscribeWithGoogleInfoDataType = '';
  /**
   * The current state of the subscription.
   *
   * @var string
   */
  public $subscriptionState;
  protected $testPurchaseType = TestPurchase::class;
  protected $testPurchaseDataType = '';

  /**
   * The acknowledgement state of the subscription.
   *
   * Accepted values: ACKNOWLEDGEMENT_STATE_UNSPECIFIED,
   * ACKNOWLEDGEMENT_STATE_PENDING, ACKNOWLEDGEMENT_STATE_ACKNOWLEDGED
   *
   * @param self::ACKNOWLEDGEMENT_STATE_* $acknowledgementState
   */
  public function setAcknowledgementState($acknowledgementState)
  {
    $this->acknowledgementState = $acknowledgementState;
  }
  /**
   * @return self::ACKNOWLEDGEMENT_STATE_*
   */
  public function getAcknowledgementState()
  {
    return $this->acknowledgementState;
  }
  /**
   * Additional context around canceled subscriptions. Only present if the
   * subscription currently has subscription_state SUBSCRIPTION_STATE_CANCELED
   * or SUBSCRIPTION_STATE_EXPIRED.
   *
   * @param CanceledStateContext $canceledStateContext
   */
  public function setCanceledStateContext(CanceledStateContext $canceledStateContext)
  {
    $this->canceledStateContext = $canceledStateContext;
  }
  /**
   * @return CanceledStateContext
   */
  public function getCanceledStateContext()
  {
    return $this->canceledStateContext;
  }
  /**
   * User account identifier in the third-party service.
   *
   * @param ExternalAccountIdentifiers $externalAccountIdentifiers
   */
  public function setExternalAccountIdentifiers(ExternalAccountIdentifiers $externalAccountIdentifiers)
  {
    $this->externalAccountIdentifiers = $externalAccountIdentifiers;
  }
  /**
   * @return ExternalAccountIdentifiers
   */
  public function getExternalAccountIdentifiers()
  {
    return $this->externalAccountIdentifiers;
  }
  /**
   * This kind represents a SubscriptionPurchaseV2 object in the
   * androidpublisher service.
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
   * Deprecated: Use line_items.latest_successful_order_id instead. The order id
   * of the latest order associated with the purchase of the subscription. For
   * autoRenewing subscription, this is the order id of signup order if it is
   * not renewed yet, or the last recurring order id (success, pending, or
   * declined order). For prepaid subscription, this is the order id associated
   * with the queried purchase token.
   *
   * @deprecated
   * @param string $latestOrderId
   */
  public function setLatestOrderId($latestOrderId)
  {
    $this->latestOrderId = $latestOrderId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getLatestOrderId()
  {
    return $this->latestOrderId;
  }
  /**
   * Item-level info for a subscription purchase. The items in the same purchase
   * should be either all with AutoRenewingPlan or all with PrepaidPlan.
   *
   * @param SubscriptionPurchaseLineItem[] $lineItems
   */
  public function setLineItems($lineItems)
  {
    $this->lineItems = $lineItems;
  }
  /**
   * @return SubscriptionPurchaseLineItem[]
   */
  public function getLineItems()
  {
    return $this->lineItems;
  }
  /**
   * The purchase token of the old subscription if this subscription is one of
   * the following: * Re-signup of a canceled but non-lapsed subscription *
   * Upgrade/downgrade from a previous subscription. * Convert from prepaid to
   * auto renewing subscription. * Convert from an auto renewing subscription to
   * prepaid. * Topup a prepaid subscription.
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
   * Additional context for out of app purchases. This information is only
   * present for re-subscription purchases (subscription purchases made after
   * the previous subscription of the same product has expired) made through the
   * Google Play subscriptions center. This field will be removed after you
   * acknowledge the subscription.
   *
   * @param OutOfAppPurchaseContext $outOfAppPurchaseContext
   */
  public function setOutOfAppPurchaseContext(OutOfAppPurchaseContext $outOfAppPurchaseContext)
  {
    $this->outOfAppPurchaseContext = $outOfAppPurchaseContext;
  }
  /**
   * @return OutOfAppPurchaseContext
   */
  public function getOutOfAppPurchaseContext()
  {
    return $this->outOfAppPurchaseContext;
  }
  /**
   * Additional context around paused subscriptions. Only present if the
   * subscription currently has subscription_state SUBSCRIPTION_STATE_PAUSED.
   *
   * @param PausedStateContext $pausedStateContext
   */
  public function setPausedStateContext(PausedStateContext $pausedStateContext)
  {
    $this->pausedStateContext = $pausedStateContext;
  }
  /**
   * @return PausedStateContext
   */
  public function getPausedStateContext()
  {
    return $this->pausedStateContext;
  }
  /**
   * ISO 3166-1 alpha-2 billing country/region code of the user at the time the
   * subscription was granted.
   *
   * @param string $regionCode
   */
  public function setRegionCode($regionCode)
  {
    $this->regionCode = $regionCode;
  }
  /**
   * @return string
   */
  public function getRegionCode()
  {
    return $this->regionCode;
  }
  /**
   * Time at which the subscription was granted. Not set for pending
   * subscriptions (subscription was created but awaiting payment during
   * signup).
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * User profile associated with purchases made with 'Subscribe with Google'.
   *
   * @param SubscribeWithGoogleInfo $subscribeWithGoogleInfo
   */
  public function setSubscribeWithGoogleInfo(SubscribeWithGoogleInfo $subscribeWithGoogleInfo)
  {
    $this->subscribeWithGoogleInfo = $subscribeWithGoogleInfo;
  }
  /**
   * @return SubscribeWithGoogleInfo
   */
  public function getSubscribeWithGoogleInfo()
  {
    return $this->subscribeWithGoogleInfo;
  }
  /**
   * The current state of the subscription.
   *
   * Accepted values: SUBSCRIPTION_STATE_UNSPECIFIED,
   * SUBSCRIPTION_STATE_PENDING, SUBSCRIPTION_STATE_ACTIVE,
   * SUBSCRIPTION_STATE_PAUSED, SUBSCRIPTION_STATE_IN_GRACE_PERIOD,
   * SUBSCRIPTION_STATE_ON_HOLD, SUBSCRIPTION_STATE_CANCELED,
   * SUBSCRIPTION_STATE_EXPIRED, SUBSCRIPTION_STATE_PENDING_PURCHASE_CANCELED
   *
   * @param self::SUBSCRIPTION_STATE_* $subscriptionState
   */
  public function setSubscriptionState($subscriptionState)
  {
    $this->subscriptionState = $subscriptionState;
  }
  /**
   * @return self::SUBSCRIPTION_STATE_*
   */
  public function getSubscriptionState()
  {
    return $this->subscriptionState;
  }
  /**
   * Only present if this subscription purchase is a test purchase.
   *
   * @param TestPurchase $testPurchase
   */
  public function setTestPurchase(TestPurchase $testPurchase)
  {
    $this->testPurchase = $testPurchase;
  }
  /**
   * @return TestPurchase
   */
  public function getTestPurchase()
  {
    return $this->testPurchase;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionPurchaseV2::class, 'Google_Service_AndroidPublisher_SubscriptionPurchaseV2');
