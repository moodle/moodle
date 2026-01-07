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

class PriceStepUpConsentDetails extends \Google\Model
{
  /**
   * Unspecified consent state.
   */
  public const STATE_CONSENT_STATE_UNSPECIFIED = 'CONSENT_STATE_UNSPECIFIED';
  /**
   * The user has not yet provided consent.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The user has consented, and the new price is waiting to take effect.
   */
  public const STATE_CONFIRMED = 'CONFIRMED';
  /**
   * The user has consented, and the new price has taken effect.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * The deadline by which the user must provide consent. If consent is not
   * provided by this time, the subscription will be canceled.
   *
   * @var string
   */
  public $consentDeadlineTime;
  protected $newPriceType = Money::class;
  protected $newPriceDataType = '';
  /**
   * Output only. The state of the price step-up consent.
   *
   * @var string
   */
  public $state;

  /**
   * The deadline by which the user must provide consent. If consent is not
   * provided by this time, the subscription will be canceled.
   *
   * @param string $consentDeadlineTime
   */
  public function setConsentDeadlineTime($consentDeadlineTime)
  {
    $this->consentDeadlineTime = $consentDeadlineTime;
  }
  /**
   * @return string
   */
  public function getConsentDeadlineTime()
  {
    return $this->consentDeadlineTime;
  }
  /**
   * The new price which requires user consent.
   *
   * @param Money $newPrice
   */
  public function setNewPrice(Money $newPrice)
  {
    $this->newPrice = $newPrice;
  }
  /**
   * @return Money
   */
  public function getNewPrice()
  {
    return $this->newPrice;
  }
  /**
   * Output only. The state of the price step-up consent.
   *
   * Accepted values: CONSENT_STATE_UNSPECIFIED, PENDING, CONFIRMED, COMPLETED
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
class_alias(PriceStepUpConsentDetails::class, 'Google_Service_AndroidPublisher_PriceStepUpConsentDetails');
