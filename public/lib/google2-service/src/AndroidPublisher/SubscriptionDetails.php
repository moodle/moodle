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

class SubscriptionDetails extends \Google\Model
{
  /**
   * Offer phase unspecified. This value is not used.
   */
  public const OFFER_PHASE_OFFER_PHASE_UNSPECIFIED = 'OFFER_PHASE_UNSPECIFIED';
  /**
   * The order funds a base price period.
   */
  public const OFFER_PHASE_BASE = 'BASE';
  /**
   * The order funds an introductory pricing period.
   */
  public const OFFER_PHASE_INTRODUCTORY = 'INTRODUCTORY';
  /**
   * The order funds a free trial period.
   */
  public const OFFER_PHASE_FREE_TRIAL = 'FREE_TRIAL';
  /**
   * The base plan ID of the subscription.
   *
   * @var string
   */
  public $basePlanId;
  /**
   * The offer ID for the current subscription offer.
   *
   * @var string
   */
  public $offerId;
  /**
   * The pricing phase for the billing period funded by this order. Deprecated.
   * Use offer_phase_details instead.
   *
   * @var string
   */
  public $offerPhase;
  protected $offerPhaseDetailsType = OfferPhaseDetails::class;
  protected $offerPhaseDetailsDataType = '';
  /**
   * The end of the billing period funded by this order. This is a snapshot of
   * the billing/service period end time at the moment the order was processed,
   * and should be used only for accounting. To get the current end time of the
   * subscription service period, use purchases.subscriptionsv2.get.
   *
   * @var string
   */
  public $servicePeriodEndTime;
  /**
   * The start of the billing period funded by this order. This is a snapshot of
   * the billing/service period start time at the moment the order was
   * processed, and should be used only for accounting.
   *
   * @var string
   */
  public $servicePeriodStartTime;

  /**
   * The base plan ID of the subscription.
   *
   * @param string $basePlanId
   */
  public function setBasePlanId($basePlanId)
  {
    $this->basePlanId = $basePlanId;
  }
  /**
   * @return string
   */
  public function getBasePlanId()
  {
    return $this->basePlanId;
  }
  /**
   * The offer ID for the current subscription offer.
   *
   * @param string $offerId
   */
  public function setOfferId($offerId)
  {
    $this->offerId = $offerId;
  }
  /**
   * @return string
   */
  public function getOfferId()
  {
    return $this->offerId;
  }
  /**
   * The pricing phase for the billing period funded by this order. Deprecated.
   * Use offer_phase_details instead.
   *
   * Accepted values: OFFER_PHASE_UNSPECIFIED, BASE, INTRODUCTORY, FREE_TRIAL
   *
   * @param self::OFFER_PHASE_* $offerPhase
   */
  public function setOfferPhase($offerPhase)
  {
    $this->offerPhase = $offerPhase;
  }
  /**
   * @return self::OFFER_PHASE_*
   */
  public function getOfferPhase()
  {
    return $this->offerPhase;
  }
  /**
   * The pricing phase details for the entitlement period funded by this order.
   *
   * @param OfferPhaseDetails $offerPhaseDetails
   */
  public function setOfferPhaseDetails(OfferPhaseDetails $offerPhaseDetails)
  {
    $this->offerPhaseDetails = $offerPhaseDetails;
  }
  /**
   * @return OfferPhaseDetails
   */
  public function getOfferPhaseDetails()
  {
    return $this->offerPhaseDetails;
  }
  /**
   * The end of the billing period funded by this order. This is a snapshot of
   * the billing/service period end time at the moment the order was processed,
   * and should be used only for accounting. To get the current end time of the
   * subscription service period, use purchases.subscriptionsv2.get.
   *
   * @param string $servicePeriodEndTime
   */
  public function setServicePeriodEndTime($servicePeriodEndTime)
  {
    $this->servicePeriodEndTime = $servicePeriodEndTime;
  }
  /**
   * @return string
   */
  public function getServicePeriodEndTime()
  {
    return $this->servicePeriodEndTime;
  }
  /**
   * The start of the billing period funded by this order. This is a snapshot of
   * the billing/service period start time at the moment the order was
   * processed, and should be used only for accounting.
   *
   * @param string $servicePeriodStartTime
   */
  public function setServicePeriodStartTime($servicePeriodStartTime)
  {
    $this->servicePeriodStartTime = $servicePeriodStartTime;
  }
  /**
   * @return string
   */
  public function getServicePeriodStartTime()
  {
    return $this->servicePeriodStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionDetails::class, 'Google_Service_AndroidPublisher_SubscriptionDetails');
