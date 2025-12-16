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

class ItemReplacement extends \Google\Model
{
  /**
   * Unspecified replacement mode.
   */
  public const REPLACEMENT_MODE_REPLACEMENT_MODE_UNSPECIFIED = 'REPLACEMENT_MODE_UNSPECIFIED';
  /**
   * The new plan will be prorated and credited from the old plan.
   */
  public const REPLACEMENT_MODE_WITH_TIME_PRORATION = 'WITH_TIME_PRORATION';
  /**
   * The user will be charged a prorated price for the new plan.
   */
  public const REPLACEMENT_MODE_CHARGE_PRORATED_PRICE = 'CHARGE_PRORATED_PRICE';
  /**
   * The new plan will replace the old one without prorating the time.
   */
  public const REPLACEMENT_MODE_WITHOUT_PRORATION = 'WITHOUT_PRORATION';
  /**
   * The user will be charged the full price for the new plan.
   */
  public const REPLACEMENT_MODE_CHARGE_FULL_PRICE = 'CHARGE_FULL_PRICE';
  /**
   * The old plan will be cancelled and the new plan will be effective after the
   * old one expires.
   */
  public const REPLACEMENT_MODE_DEFERRED = 'DEFERRED';
  /**
   * The plan will remain unchanged with this replacement.
   */
  public const REPLACEMENT_MODE_KEEP_EXISTING = 'KEEP_EXISTING';
  /**
   * The base plan ID of the subscription line item being replaced.
   *
   * @var string
   */
  public $basePlanId;
  /**
   * The offer ID of the subscription line item being replaced, if applicable.
   *
   * @var string
   */
  public $offerId;
  /**
   * The product ID of the subscription line item being replaced.
   *
   * @var string
   */
  public $productId;
  /**
   * The replacement mode applied during the purchase.
   *
   * @var string
   */
  public $replacementMode;

  /**
   * The base plan ID of the subscription line item being replaced.
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
   * The offer ID of the subscription line item being replaced, if applicable.
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
   * The product ID of the subscription line item being replaced.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * The replacement mode applied during the purchase.
   *
   * Accepted values: REPLACEMENT_MODE_UNSPECIFIED, WITH_TIME_PRORATION,
   * CHARGE_PRORATED_PRICE, WITHOUT_PRORATION, CHARGE_FULL_PRICE, DEFERRED,
   * KEEP_EXISTING
   *
   * @param self::REPLACEMENT_MODE_* $replacementMode
   */
  public function setReplacementMode($replacementMode)
  {
    $this->replacementMode = $replacementMode;
  }
  /**
   * @return self::REPLACEMENT_MODE_*
   */
  public function getReplacementMode()
  {
    return $this->replacementMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemReplacement::class, 'Google_Service_AndroidPublisher_ItemReplacement');
