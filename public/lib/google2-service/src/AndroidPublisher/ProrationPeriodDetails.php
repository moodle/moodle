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

class ProrationPeriodDetails extends \Google\Model
{
  /**
   * Offer phase unspecified. This value is not used.
   */
  public const ORIGINAL_OFFER_PHASE_OFFER_PHASE_UNSPECIFIED = 'OFFER_PHASE_UNSPECIFIED';
  /**
   * The order funds a base price period.
   */
  public const ORIGINAL_OFFER_PHASE_BASE = 'BASE';
  /**
   * The order funds an introductory pricing period.
   */
  public const ORIGINAL_OFFER_PHASE_INTRODUCTORY = 'INTRODUCTORY';
  /**
   * The order funds a free trial period.
   */
  public const ORIGINAL_OFFER_PHASE_FREE_TRIAL = 'FREE_TRIAL';
  /**
   * The last order id of the original subscription purchase prior to the plan
   * change. This is only populated if this proration period is from an
   * ugrade/downgrade from a previous subscription and carries the remaining
   * offer phase from the linked order of the previous subscription.
   *
   * @var string
   */
  public $linkedOrderId;
  /**
   * Represent the original offer phase from the purchased the line item if the
   * proration period contains any of them. For example, a proration period from
   * CHARGE_FULL_PRICE plan change may merge the 1st offer phase of the
   * subscription offer of the new product user purchased. In this case, the
   * original offer phase will be set here.
   *
   * @var string
   */
  public $originalOfferPhase;

  /**
   * The last order id of the original subscription purchase prior to the plan
   * change. This is only populated if this proration period is from an
   * ugrade/downgrade from a previous subscription and carries the remaining
   * offer phase from the linked order of the previous subscription.
   *
   * @param string $linkedOrderId
   */
  public function setLinkedOrderId($linkedOrderId)
  {
    $this->linkedOrderId = $linkedOrderId;
  }
  /**
   * @return string
   */
  public function getLinkedOrderId()
  {
    return $this->linkedOrderId;
  }
  /**
   * Represent the original offer phase from the purchased the line item if the
   * proration period contains any of them. For example, a proration period from
   * CHARGE_FULL_PRICE plan change may merge the 1st offer phase of the
   * subscription offer of the new product user purchased. In this case, the
   * original offer phase will be set here.
   *
   * Accepted values: OFFER_PHASE_UNSPECIFIED, BASE, INTRODUCTORY, FREE_TRIAL
   *
   * @param self::ORIGINAL_OFFER_PHASE_* $originalOfferPhase
   */
  public function setOriginalOfferPhase($originalOfferPhase)
  {
    $this->originalOfferPhase = $originalOfferPhase;
  }
  /**
   * @return self::ORIGINAL_OFFER_PHASE_*
   */
  public function getOriginalOfferPhase()
  {
    return $this->originalOfferPhase;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProrationPeriodDetails::class, 'Google_Service_AndroidPublisher_ProrationPeriodDetails');
