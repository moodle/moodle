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

class SubscriptionLineItem extends \Google\Collection
{
  /**
   * The line item recurrence type is unspecified.
   */
  public const RECURRENCE_TYPE_LINE_ITEM_RECURRENCE_TYPE_UNSPECIFIED = 'LINE_ITEM_RECURRENCE_TYPE_UNSPECIFIED';
  /**
   * The line item recurs periodically.
   */
  public const RECURRENCE_TYPE_LINE_ITEM_RECURRENCE_TYPE_PERIODIC = 'LINE_ITEM_RECURRENCE_TYPE_PERIODIC';
  /**
   * The line item does not recur in the future.
   */
  public const RECURRENCE_TYPE_LINE_ITEM_RECURRENCE_TYPE_ONE_TIME = 'LINE_ITEM_RECURRENCE_TYPE_ONE_TIME';
  /**
   * Unspecified state.
   */
  public const STATE_LINE_ITEM_STATE_UNSPECIFIED = 'LINE_ITEM_STATE_UNSPECIFIED';
  /**
   * The line item is in ACTIVE state. If the subscription is cancelled or
   * suspended, the line item will not be charged even if the line item is
   * active.
   */
  public const STATE_LINE_ITEM_STATE_ACTIVE = 'LINE_ITEM_STATE_ACTIVE';
  /**
   * The line item is in INACTIVE state.
   */
  public const STATE_LINE_ITEM_STATE_INACTIVE = 'LINE_ITEM_STATE_INACTIVE';
  /**
   * The line item is new, and is not activated or charged yet.
   */
  public const STATE_LINE_ITEM_STATE_NEW = 'LINE_ITEM_STATE_NEW';
  /**
   * The line item is being activated in order to be charged. If a free trial
   * applies to the line item, the line item is pending a prorated charge at the
   * end of the free trial period, as indicated by
   * `line_item_free_trial_end_time`.
   */
  public const STATE_LINE_ITEM_STATE_ACTIVATING = 'LINE_ITEM_STATE_ACTIVATING';
  /**
   * The line item is being deactivated, and a prorated refund in being
   * processed.
   */
  public const STATE_LINE_ITEM_STATE_DEACTIVATING = 'LINE_ITEM_STATE_DEACTIVATING';
  /**
   * The line item is scheduled to be deactivated at the end of the current
   * cycle.
   */
  public const STATE_LINE_ITEM_STATE_WAITING_TO_DEACTIVATE = 'LINE_ITEM_STATE_WAITING_TO_DEACTIVATE';
  /**
   * Line item is being charged off-cycle.
   */
  public const STATE_LINE_ITEM_STATE_OFF_CYCLE_CHARGING = 'LINE_ITEM_STATE_OFF_CYCLE_CHARGING';
  protected $collection_key = 'lineItemPromotionSpecs';
  protected $amountType = Amount::class;
  protected $amountDataType = '';
  protected $bundleDetailsType = SubscriptionLineItemBundleDetails::class;
  protected $bundleDetailsDataType = '';
  /**
   * Output only. Description of this line item.
   *
   * @var string
   */
  public $description;
  protected $finiteBillingCycleDetailsType = FiniteBillingCycleDetails::class;
  protected $finiteBillingCycleDetailsDataType = '';
  /**
   * Output only. The free trial end time will be populated after the line item
   * is successfully processed. End time of the line item free trial period, in
   * ISO 8061 format. For example, "2019-08-31T17:28:54.564Z". It will be set
   * the same as createTime if no free trial promotion is specified.
   *
   * @var string
   */
  public $lineItemFreeTrialEndTime;
  /**
   * Output only. A unique index of the subscription line item.
   *
   * @var int
   */
  public $lineItemIndex;
  protected $lineItemPromotionSpecsType = SubscriptionPromotionSpec::class;
  protected $lineItemPromotionSpecsDataType = 'array';
  /**
   * Identifier. Resource name of the line item. Format:
   * partners/{partner}/subscriptions/{subscription}/lineItems/{lineItem}
   *
   * @var string
   */
  public $name;
  protected $oneTimeRecurrenceDetailsType = SubscriptionLineItemOneTimeRecurrenceDetails::class;
  protected $oneTimeRecurrenceDetailsDataType = '';
  /**
   * Required. Product resource name that identifies one the line item The
   * format is 'partners/{partner_id}/products/{product_id}'.
   *
   * @var string
   */
  public $product;
  protected $productPayloadType = ProductPayload::class;
  protected $productPayloadDataType = '';
  /**
   * Output only. The recurrence type of the line item.
   *
   * @var string
   */
  public $recurrenceType;
  /**
   * Output only. The state of the line item.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The price of the product/service in this line item. The amount
   * could be the wholesale price, or it can include a cost of sale based on the
   * contract.
   *
   * @param Amount $amount
   */
  public function setAmount(Amount $amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return Amount
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * Output only. The bundle details for the line item. Only populated if the
   * line item corresponds to a hard bundle.
   *
   * @param SubscriptionLineItemBundleDetails $bundleDetails
   */
  public function setBundleDetails(SubscriptionLineItemBundleDetails $bundleDetails)
  {
    $this->bundleDetails = $bundleDetails;
  }
  /**
   * @return SubscriptionLineItemBundleDetails
   */
  public function getBundleDetails()
  {
    return $this->bundleDetails;
  }
  /**
   * Output only. Description of this line item.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Details for a subscription line item with finite billing cycles.
   * If unset, the line item will be charged indefinitely. Used only with
   * LINE_ITEM_RECURRENCE_TYPE_PERIODIC.
   *
   * @param FiniteBillingCycleDetails $finiteBillingCycleDetails
   */
  public function setFiniteBillingCycleDetails(FiniteBillingCycleDetails $finiteBillingCycleDetails)
  {
    $this->finiteBillingCycleDetails = $finiteBillingCycleDetails;
  }
  /**
   * @return FiniteBillingCycleDetails
   */
  public function getFiniteBillingCycleDetails()
  {
    return $this->finiteBillingCycleDetails;
  }
  /**
   * Output only. The free trial end time will be populated after the line item
   * is successfully processed. End time of the line item free trial period, in
   * ISO 8061 format. For example, "2019-08-31T17:28:54.564Z". It will be set
   * the same as createTime if no free trial promotion is specified.
   *
   * @param string $lineItemFreeTrialEndTime
   */
  public function setLineItemFreeTrialEndTime($lineItemFreeTrialEndTime)
  {
    $this->lineItemFreeTrialEndTime = $lineItemFreeTrialEndTime;
  }
  /**
   * @return string
   */
  public function getLineItemFreeTrialEndTime()
  {
    return $this->lineItemFreeTrialEndTime;
  }
  /**
   * Output only. A unique index of the subscription line item.
   *
   * @param int $lineItemIndex
   */
  public function setLineItemIndex($lineItemIndex)
  {
    $this->lineItemIndex = $lineItemIndex;
  }
  /**
   * @return int
   */
  public function getLineItemIndex()
  {
    return $this->lineItemIndex;
  }
  /**
   * Optional. The promotions applied on the line item. It can be: - an
   * introductory pricing promotion. - a free trial promotion. This feature is
   * not enabled. If used, the request will be rejected. When used as input in
   * Create or Provision API, specify its resource name only.
   *
   * @param SubscriptionPromotionSpec[] $lineItemPromotionSpecs
   */
  public function setLineItemPromotionSpecs($lineItemPromotionSpecs)
  {
    $this->lineItemPromotionSpecs = $lineItemPromotionSpecs;
  }
  /**
   * @return SubscriptionPromotionSpec[]
   */
  public function getLineItemPromotionSpecs()
  {
    return $this->lineItemPromotionSpecs;
  }
  /**
   * Identifier. Resource name of the line item. Format:
   * partners/{partner}/subscriptions/{subscription}/lineItems/{lineItem}
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
   * Output only. Details only set for a ONE_TIME recurrence line item.
   *
   * @param SubscriptionLineItemOneTimeRecurrenceDetails $oneTimeRecurrenceDetails
   */
  public function setOneTimeRecurrenceDetails(SubscriptionLineItemOneTimeRecurrenceDetails $oneTimeRecurrenceDetails)
  {
    $this->oneTimeRecurrenceDetails = $oneTimeRecurrenceDetails;
  }
  /**
   * @return SubscriptionLineItemOneTimeRecurrenceDetails
   */
  public function getOneTimeRecurrenceDetails()
  {
    return $this->oneTimeRecurrenceDetails;
  }
  /**
   * Required. Product resource name that identifies one the line item The
   * format is 'partners/{partner_id}/products/{product_id}'.
   *
   * @param string $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @return string
   */
  public function getProduct()
  {
    return $this->product;
  }
  /**
   * Optional. Product specific payload for this line item.
   *
   * @param ProductPayload $productPayload
   */
  public function setProductPayload(ProductPayload $productPayload)
  {
    $this->productPayload = $productPayload;
  }
  /**
   * @return ProductPayload
   */
  public function getProductPayload()
  {
    return $this->productPayload;
  }
  /**
   * Output only. The recurrence type of the line item.
   *
   * Accepted values: LINE_ITEM_RECURRENCE_TYPE_UNSPECIFIED,
   * LINE_ITEM_RECURRENCE_TYPE_PERIODIC, LINE_ITEM_RECURRENCE_TYPE_ONE_TIME
   *
   * @param self::RECURRENCE_TYPE_* $recurrenceType
   */
  public function setRecurrenceType($recurrenceType)
  {
    $this->recurrenceType = $recurrenceType;
  }
  /**
   * @return self::RECURRENCE_TYPE_*
   */
  public function getRecurrenceType()
  {
    return $this->recurrenceType;
  }
  /**
   * Output only. The state of the line item.
   *
   * Accepted values: LINE_ITEM_STATE_UNSPECIFIED, LINE_ITEM_STATE_ACTIVE,
   * LINE_ITEM_STATE_INACTIVE, LINE_ITEM_STATE_NEW, LINE_ITEM_STATE_ACTIVATING,
   * LINE_ITEM_STATE_DEACTIVATING, LINE_ITEM_STATE_WAITING_TO_DEACTIVATE,
   * LINE_ITEM_STATE_OFF_CYCLE_CHARGING
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionLineItem::class, 'Google_Service_PaymentsResellerSubscription_SubscriptionLineItem');
