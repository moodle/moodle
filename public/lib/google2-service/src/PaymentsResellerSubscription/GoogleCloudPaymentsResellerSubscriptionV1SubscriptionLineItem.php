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

class GoogleCloudPaymentsResellerSubscriptionV1SubscriptionLineItem extends \Google\Collection
{
  protected $collection_key = 'lineItemPromotionSpecs';
  protected $amountType = GoogleCloudPaymentsResellerSubscriptionV1Amount::class;
  protected $amountDataType = '';
  protected $bundleDetailsType = SubscriptionLineItemBundleDetails::class;
  protected $bundleDetailsDataType = '';
  /**
   * @var string
   */
  public $description;
  protected $finiteBillingCycleDetailsType = GoogleCloudPaymentsResellerSubscriptionV1FiniteBillingCycleDetails::class;
  protected $finiteBillingCycleDetailsDataType = '';
  /**
   * @var string
   */
  public $lineItemFreeTrialEndTime;
  /**
   * @var int
   */
  public $lineItemIndex;
  protected $lineItemPromotionSpecsType = GoogleCloudPaymentsResellerSubscriptionV1SubscriptionPromotionSpec::class;
  protected $lineItemPromotionSpecsDataType = 'array';
  protected $oneTimeRecurrenceDetailsType = GoogleCloudPaymentsResellerSubscriptionV1SubscriptionLineItemOneTimeRecurrenceDetails::class;
  protected $oneTimeRecurrenceDetailsDataType = '';
  /**
   * @var string
   */
  public $product;
  protected $productPayloadType = GoogleCloudPaymentsResellerSubscriptionV1ProductPayload::class;
  protected $productPayloadDataType = '';
  /**
   * @var string
   */
  public $recurrenceType;
  /**
   * @var string
   */
  public $state;

  /**
   * @param GoogleCloudPaymentsResellerSubscriptionV1Amount
   */
  public function setAmount(GoogleCloudPaymentsResellerSubscriptionV1Amount $amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1Amount
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * @param SubscriptionLineItemBundleDetails
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
   * @param string
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
   * @param GoogleCloudPaymentsResellerSubscriptionV1FiniteBillingCycleDetails
   */
  public function setFiniteBillingCycleDetails(GoogleCloudPaymentsResellerSubscriptionV1FiniteBillingCycleDetails $finiteBillingCycleDetails)
  {
    $this->finiteBillingCycleDetails = $finiteBillingCycleDetails;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1FiniteBillingCycleDetails
   */
  public function getFiniteBillingCycleDetails()
  {
    return $this->finiteBillingCycleDetails;
  }
  /**
   * @param string
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
   * @param int
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
   * @param GoogleCloudPaymentsResellerSubscriptionV1SubscriptionPromotionSpec[]
   */
  public function setLineItemPromotionSpecs($lineItemPromotionSpecs)
  {
    $this->lineItemPromotionSpecs = $lineItemPromotionSpecs;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1SubscriptionPromotionSpec[]
   */
  public function getLineItemPromotionSpecs()
  {
    return $this->lineItemPromotionSpecs;
  }
  /**
   * @param GoogleCloudPaymentsResellerSubscriptionV1SubscriptionLineItemOneTimeRecurrenceDetails
   */
  public function setOneTimeRecurrenceDetails(GoogleCloudPaymentsResellerSubscriptionV1SubscriptionLineItemOneTimeRecurrenceDetails $oneTimeRecurrenceDetails)
  {
    $this->oneTimeRecurrenceDetails = $oneTimeRecurrenceDetails;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1SubscriptionLineItemOneTimeRecurrenceDetails
   */
  public function getOneTimeRecurrenceDetails()
  {
    return $this->oneTimeRecurrenceDetails;
  }
  /**
   * @param string
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
   * @param GoogleCloudPaymentsResellerSubscriptionV1ProductPayload
   */
  public function setProductPayload(GoogleCloudPaymentsResellerSubscriptionV1ProductPayload $productPayload)
  {
    $this->productPayload = $productPayload;
  }
  /**
   * @return GoogleCloudPaymentsResellerSubscriptionV1ProductPayload
   */
  public function getProductPayload()
  {
    return $this->productPayload;
  }
  /**
   * @param string
   */
  public function setRecurrenceType($recurrenceType)
  {
    $this->recurrenceType = $recurrenceType;
  }
  /**
   * @return string
   */
  public function getRecurrenceType()
  {
    return $this->recurrenceType;
  }
  /**
   * @param string
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPaymentsResellerSubscriptionV1SubscriptionLineItem::class, 'Google_Service_PaymentsResellerSubscription_GoogleCloudPaymentsResellerSubscriptionV1SubscriptionLineItem');
