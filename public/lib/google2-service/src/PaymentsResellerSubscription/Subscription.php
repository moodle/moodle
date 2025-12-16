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

namespace Google\Service\PaymentsResellerSubscription;

class Subscription extends \Google\Collection
{
  /**
   * The processing state is unspecified.
   */
  public const PROCESSING_STATE_PROCESSING_STATE_UNSPECIFIED = 'PROCESSING_STATE_UNSPECIFIED';
  /**
   * The subscription is being cancelled.
   */
  public const PROCESSING_STATE_PROCESSING_STATE_CANCELLING = 'PROCESSING_STATE_CANCELLING';
  /**
   * The subscription is recurring.
   */
  public const PROCESSING_STATE_PROCESSING_STATE_RECURRING = 'PROCESSING_STATE_RECURRING';
  /**
   * The subscription is being resumed.
   */
  public const PROCESSING_STATE_PROCESSING_STATE_RESUMING = 'PROCESSING_STATE_RESUMING';
  /**
   * The state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The subscription is created, a state before it is moved to STATE_ACTIVE.
   */
  public const STATE_STATE_CREATED = 'STATE_CREATED';
  /**
   * The subscription is active.
   */
  public const STATE_STATE_ACTIVE = 'STATE_ACTIVE';
  /**
   * The subscription is cancelled. This is the final state of the subscription,
   * as it can no longer be modified or reactivated.
   */
  public const STATE_STATE_CANCELLED = 'STATE_CANCELLED';
  /**
   * The subscription is in grace period. It can happen: 1) in manual extend
   * mode, the subscription is not extended by the partner at the end of current
   * cycle. 2) for outbound authorization enabled partners, a renewal purchase
   * order is rejected.
   */
  public const STATE_STATE_IN_GRACE_PERIOD = 'STATE_IN_GRACE_PERIOD';
  /**
   * The subscription is waiting to be cancelled by the next recurrence cycle.
   */
  public const STATE_STATE_CANCEL_AT_END_OF_CYCLE = 'STATE_CANCEL_AT_END_OF_CYCLE';
  /**
   * The subscription is suspended.
   */
  public const STATE_STATE_SUSPENDED = 'STATE_SUSPENDED';
  protected $collection_key = 'promotions';
  protected $cancellationDetailsType = SubscriptionCancellationDetails::class;
  protected $cancellationDetailsDataType = '';
  /**
   * Output only. System generated timestamp when the subscription is created.
   * UTC timezone.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time at which the subscription is expected to be extended,
   * in ISO 8061 format. UTC timezone. For example: "2019-08-31T17:28:54.564Z"
   *
   * @var string
   */
  public $cycleEndTime;
  /**
   * Output only. Indicates if the subscription is entitled to the end user.
   *
   * @var bool
   */
  public $endUserEntitled;
  /**
   * Output only. End of the free trial period, in ISO 8061 format. For example,
   * "2019-08-31T17:28:54.564Z". It will be set the same as createTime if no
   * free trial promotion is specified.
   *
   * @var string
   */
  public $freeTrialEndTime;
  protected $lineItemsType = SubscriptionLineItem::class;
  protected $lineItemsDataType = 'array';
  protected $migrationDetailsType = SubscriptionMigrationDetails::class;
  protected $migrationDetailsDataType = '';
  /**
   * Identifier. Resource name of the subscription. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}". This is available
   * for authorizeAddon, but otherwise is response only.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Identifier of the end-user in partner’s system. The value is
   * restricted to 63 ASCII characters at the maximum.
   *
   * @var string
   */
  public $partnerUserToken;
  /**
   * Output only. Describes the processing state of the subscription. See more
   * details at [the lifecycle of a subscription](/payments/reseller/subscriptio
   * n/reference/index/Receive.Notifications#payments-subscription-lifecycle).
   *
   * @var string
   */
  public $processingState;
  /**
   * Optional. Deprecated: consider using `line_items` as the input. Required.
   * Resource name that identifies the purchased products. The format will be
   * 'partners/{partner_id}/products/{product_id}'.
   *
   * @var string[]
   */
  public $products;
  protected $promotionSpecsType = SubscriptionPromotionSpec::class;
  protected $promotionSpecsDataType = 'array';
  /**
   * Optional. Deprecated: consider using the top-level `promotion_specs` as the
   * input. Optional. Resource name that identifies one or more promotions that
   * can be applied on the product. A typical promotion for a subscription is
   * Free trial. The format will be
   * 'partners/{partner_id}/promotions/{promotion_id}'.
   *
   * @var string[]
   */
  public $promotions;
  /**
   * Optional. The timestamp when the user transaction was made with the
   * Partner. Specify for the case of "bundle with choice", and it must be
   * before the provision_time (when the user makes a selection).
   *
   * @var string
   */
  public $purchaseTime;
  /**
   * Output only. The place where partners should redirect the end-user to after
   * creation. This field might also be populated when creation failed. However,
   * Partners should always prepare a default URL to redirect the user in case
   * this field is empty.
   *
   * @var string
   */
  public $redirectUri;
  /**
   * Output only. The time at which the subscription is expected to be renewed
   * by Google - a new charge will be incurred and the service entitlement will
   * be renewed. A non-immediate cancellation will take place at this time too,
   * before which, the service entitlement for the end user will remain valid.
   * UTC timezone in ISO 8061 format. For example: "2019-08-31T17:28:54.564Z"
   *
   * @var string
   */
  public $renewalTime;
  protected $serviceLocationType = Location::class;
  protected $serviceLocationDataType = '';
  /**
   * Output only. Describes the state of the subscription. See more details at
   * [the lifecycle of a subscription](/payments/reseller/subscription/reference
   * /index/Receive.Notifications#payments-subscription-lifecycle).
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System generated timestamp when the subscription is most
   * recently updated. UTC timezone.
   *
   * @var string
   */
  public $updateTime;
  protected $upgradeDowngradeDetailsType = SubscriptionUpgradeDowngradeDetails::class;
  protected $upgradeDowngradeDetailsDataType = '';

  /**
   * Output only. Describes the details of a cancelled subscription. Only
   * applicable to subscription of state `STATE_CANCELLED`.
   *
   * @param SubscriptionCancellationDetails $cancellationDetails
   */
  public function setCancellationDetails(SubscriptionCancellationDetails $cancellationDetails)
  {
    $this->cancellationDetails = $cancellationDetails;
  }
  /**
   * @return SubscriptionCancellationDetails
   */
  public function getCancellationDetails()
  {
    return $this->cancellationDetails;
  }
  /**
   * Output only. System generated timestamp when the subscription is created.
   * UTC timezone.
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
   * Output only. The time at which the subscription is expected to be extended,
   * in ISO 8061 format. UTC timezone. For example: "2019-08-31T17:28:54.564Z"
   *
   * @param string $cycleEndTime
   */
  public function setCycleEndTime($cycleEndTime)
  {
    $this->cycleEndTime = $cycleEndTime;
  }
  /**
   * @return string
   */
  public function getCycleEndTime()
  {
    return $this->cycleEndTime;
  }
  /**
   * Output only. Indicates if the subscription is entitled to the end user.
   *
   * @param bool $endUserEntitled
   */
  public function setEndUserEntitled($endUserEntitled)
  {
    $this->endUserEntitled = $endUserEntitled;
  }
  /**
   * @return bool
   */
  public function getEndUserEntitled()
  {
    return $this->endUserEntitled;
  }
  /**
   * Output only. End of the free trial period, in ISO 8061 format. For example,
   * "2019-08-31T17:28:54.564Z". It will be set the same as createTime if no
   * free trial promotion is specified.
   *
   * @param string $freeTrialEndTime
   */
  public function setFreeTrialEndTime($freeTrialEndTime)
  {
    $this->freeTrialEndTime = $freeTrialEndTime;
  }
  /**
   * @return string
   */
  public function getFreeTrialEndTime()
  {
    return $this->freeTrialEndTime;
  }
  /**
   * Required. The line items of the subscription.
   *
   * @param SubscriptionLineItem[] $lineItems
   */
  public function setLineItems($lineItems)
  {
    $this->lineItems = $lineItems;
  }
  /**
   * @return SubscriptionLineItem[]
   */
  public function getLineItems()
  {
    return $this->lineItems;
  }
  /**
   * Output only. Describes the details of the migrated subscription. Only
   * populated if this subscription is migrated from another system.
   *
   * @param SubscriptionMigrationDetails $migrationDetails
   */
  public function setMigrationDetails(SubscriptionMigrationDetails $migrationDetails)
  {
    $this->migrationDetails = $migrationDetails;
  }
  /**
   * @return SubscriptionMigrationDetails
   */
  public function getMigrationDetails()
  {
    return $this->migrationDetails;
  }
  /**
   * Identifier. Resource name of the subscription. It will have the format of
   * "partners/{partner_id}/subscriptions/{subscription_id}". This is available
   * for authorizeAddon, but otherwise is response only.
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
   * Required. Identifier of the end-user in partner’s system. The value is
   * restricted to 63 ASCII characters at the maximum.
   *
   * @param string $partnerUserToken
   */
  public function setPartnerUserToken($partnerUserToken)
  {
    $this->partnerUserToken = $partnerUserToken;
  }
  /**
   * @return string
   */
  public function getPartnerUserToken()
  {
    return $this->partnerUserToken;
  }
  /**
   * Output only. Describes the processing state of the subscription. See more
   * details at [the lifecycle of a subscription](/payments/reseller/subscriptio
   * n/reference/index/Receive.Notifications#payments-subscription-lifecycle).
   *
   * Accepted values: PROCESSING_STATE_UNSPECIFIED, PROCESSING_STATE_CANCELLING,
   * PROCESSING_STATE_RECURRING, PROCESSING_STATE_RESUMING
   *
   * @param self::PROCESSING_STATE_* $processingState
   */
  public function setProcessingState($processingState)
  {
    $this->processingState = $processingState;
  }
  /**
   * @return self::PROCESSING_STATE_*
   */
  public function getProcessingState()
  {
    return $this->processingState;
  }
  /**
   * Optional. Deprecated: consider using `line_items` as the input. Required.
   * Resource name that identifies the purchased products. The format will be
   * 'partners/{partner_id}/products/{product_id}'.
   *
   * @param string[] $products
   */
  public function setProducts($products)
  {
    $this->products = $products;
  }
  /**
   * @return string[]
   */
  public function getProducts()
  {
    return $this->products;
  }
  /**
   * Optional. Subscription-level promotions. Only free trial is supported on
   * this level. It determines the first renewal time of the subscription to be
   * the end of the free trial period. Specify the promotion resource name only
   * when used as input.
   *
   * @param SubscriptionPromotionSpec[] $promotionSpecs
   */
  public function setPromotionSpecs($promotionSpecs)
  {
    $this->promotionSpecs = $promotionSpecs;
  }
  /**
   * @return SubscriptionPromotionSpec[]
   */
  public function getPromotionSpecs()
  {
    return $this->promotionSpecs;
  }
  /**
   * Optional. Deprecated: consider using the top-level `promotion_specs` as the
   * input. Optional. Resource name that identifies one or more promotions that
   * can be applied on the product. A typical promotion for a subscription is
   * Free trial. The format will be
   * 'partners/{partner_id}/promotions/{promotion_id}'.
   *
   * @param string[] $promotions
   */
  public function setPromotions($promotions)
  {
    $this->promotions = $promotions;
  }
  /**
   * @return string[]
   */
  public function getPromotions()
  {
    return $this->promotions;
  }
  /**
   * Optional. The timestamp when the user transaction was made with the
   * Partner. Specify for the case of "bundle with choice", and it must be
   * before the provision_time (when the user makes a selection).
   *
   * @param string $purchaseTime
   */
  public function setPurchaseTime($purchaseTime)
  {
    $this->purchaseTime = $purchaseTime;
  }
  /**
   * @return string
   */
  public function getPurchaseTime()
  {
    return $this->purchaseTime;
  }
  /**
   * Output only. The place where partners should redirect the end-user to after
   * creation. This field might also be populated when creation failed. However,
   * Partners should always prepare a default URL to redirect the user in case
   * this field is empty.
   *
   * @param string $redirectUri
   */
  public function setRedirectUri($redirectUri)
  {
    $this->redirectUri = $redirectUri;
  }
  /**
   * @return string
   */
  public function getRedirectUri()
  {
    return $this->redirectUri;
  }
  /**
   * Output only. The time at which the subscription is expected to be renewed
   * by Google - a new charge will be incurred and the service entitlement will
   * be renewed. A non-immediate cancellation will take place at this time too,
   * before which, the service entitlement for the end user will remain valid.
   * UTC timezone in ISO 8061 format. For example: "2019-08-31T17:28:54.564Z"
   *
   * @param string $renewalTime
   */
  public function setRenewalTime($renewalTime)
  {
    $this->renewalTime = $renewalTime;
  }
  /**
   * @return string
   */
  public function getRenewalTime()
  {
    return $this->renewalTime;
  }
  /**
   * Required. The location that the service is provided as indicated by the
   * partner.
   *
   * @param Location $serviceLocation
   */
  public function setServiceLocation(Location $serviceLocation)
  {
    $this->serviceLocation = $serviceLocation;
  }
  /**
   * @return Location
   */
  public function getServiceLocation()
  {
    return $this->serviceLocation;
  }
  /**
   * Output only. Describes the state of the subscription. See more details at
   * [the lifecycle of a subscription](/payments/reseller/subscription/reference
   * /index/Receive.Notifications#payments-subscription-lifecycle).
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_CREATED, STATE_ACTIVE,
   * STATE_CANCELLED, STATE_IN_GRACE_PERIOD, STATE_CANCEL_AT_END_OF_CYCLE,
   * STATE_SUSPENDED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. System generated timestamp when the subscription is most
   * recently updated. UTC timezone.
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
  /**
   * Optional. Details about the previous subscription that this new
   * subscription upgrades/downgrades from. Only populated if this subscription
   * is an upgrade/downgrade from another subscription.
   *
   * @param SubscriptionUpgradeDowngradeDetails $upgradeDowngradeDetails
   */
  public function setUpgradeDowngradeDetails(SubscriptionUpgradeDowngradeDetails $upgradeDowngradeDetails)
  {
    $this->upgradeDowngradeDetails = $upgradeDowngradeDetails;
  }
  /**
   * @return SubscriptionUpgradeDowngradeDetails
   */
  public function getUpgradeDowngradeDetails()
  {
    return $this->upgradeDowngradeDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subscription::class, 'Google_Service_PaymentsResellerSubscription_Subscription');
