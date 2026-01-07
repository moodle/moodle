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

class SubscriptionPurchaseLineItem extends \Google\Model
{
  protected $autoRenewingPlanType = AutoRenewingPlan::class;
  protected $autoRenewingPlanDataType = '';
  protected $deferredItemRemovalType = DeferredItemRemoval::class;
  protected $deferredItemRemovalDataType = '';
  protected $deferredItemReplacementType = DeferredItemReplacement::class;
  protected $deferredItemReplacementDataType = '';
  /**
   * Time at which the subscription expired or will expire unless the access is
   * extended (ex. renews).
   *
   * @var string
   */
  public $expiryTime;
  protected $itemReplacementType = ItemReplacement::class;
  protected $itemReplacementDataType = '';
  /**
   * The order id of the latest successful order associated with this item. Not
   * present if the item is not owned by the user yet (e.g. the item being
   * deferred replaced to).
   *
   * @var string
   */
  public $latestSuccessfulOrderId;
  protected $offerDetailsType = OfferDetails::class;
  protected $offerDetailsDataType = '';
  protected $prepaidPlanType = PrepaidPlan::class;
  protected $prepaidPlanDataType = '';
  /**
   * The purchased product ID (for example, 'monthly001').
   *
   * @var string
   */
  public $productId;
  protected $signupPromotionType = SignupPromotion::class;
  protected $signupPromotionDataType = '';

  /**
   * The item is auto renewing.
   *
   * @param AutoRenewingPlan $autoRenewingPlan
   */
  public function setAutoRenewingPlan(AutoRenewingPlan $autoRenewingPlan)
  {
    $this->autoRenewingPlan = $autoRenewingPlan;
  }
  /**
   * @return AutoRenewingPlan
   */
  public function getAutoRenewingPlan()
  {
    return $this->autoRenewingPlan;
  }
  /**
   * Information for deferred item removal.
   *
   * @param DeferredItemRemoval $deferredItemRemoval
   */
  public function setDeferredItemRemoval(DeferredItemRemoval $deferredItemRemoval)
  {
    $this->deferredItemRemoval = $deferredItemRemoval;
  }
  /**
   * @return DeferredItemRemoval
   */
  public function getDeferredItemRemoval()
  {
    return $this->deferredItemRemoval;
  }
  /**
   * Information for deferred item replacement.
   *
   * @param DeferredItemReplacement $deferredItemReplacement
   */
  public function setDeferredItemReplacement(DeferredItemReplacement $deferredItemReplacement)
  {
    $this->deferredItemReplacement = $deferredItemReplacement;
  }
  /**
   * @return DeferredItemReplacement
   */
  public function getDeferredItemReplacement()
  {
    return $this->deferredItemReplacement;
  }
  /**
   * Time at which the subscription expired or will expire unless the access is
   * extended (ex. renews).
   *
   * @param string $expiryTime
   */
  public function setExpiryTime($expiryTime)
  {
    $this->expiryTime = $expiryTime;
  }
  /**
   * @return string
   */
  public function getExpiryTime()
  {
    return $this->expiryTime;
  }
  /**
   * Details of the item being replaced. This field is only populated if this
   * item replaced another item in a previous subscription and is only available
   * for 60 days after the purchase time.
   *
   * @param ItemReplacement $itemReplacement
   */
  public function setItemReplacement(ItemReplacement $itemReplacement)
  {
    $this->itemReplacement = $itemReplacement;
  }
  /**
   * @return ItemReplacement
   */
  public function getItemReplacement()
  {
    return $this->itemReplacement;
  }
  /**
   * The order id of the latest successful order associated with this item. Not
   * present if the item is not owned by the user yet (e.g. the item being
   * deferred replaced to).
   *
   * @param string $latestSuccessfulOrderId
   */
  public function setLatestSuccessfulOrderId($latestSuccessfulOrderId)
  {
    $this->latestSuccessfulOrderId = $latestSuccessfulOrderId;
  }
  /**
   * @return string
   */
  public function getLatestSuccessfulOrderId()
  {
    return $this->latestSuccessfulOrderId;
  }
  /**
   * The offer details for this item.
   *
   * @param OfferDetails $offerDetails
   */
  public function setOfferDetails(OfferDetails $offerDetails)
  {
    $this->offerDetails = $offerDetails;
  }
  /**
   * @return OfferDetails
   */
  public function getOfferDetails()
  {
    return $this->offerDetails;
  }
  /**
   * The item is prepaid.
   *
   * @param PrepaidPlan $prepaidPlan
   */
  public function setPrepaidPlan(PrepaidPlan $prepaidPlan)
  {
    $this->prepaidPlan = $prepaidPlan;
  }
  /**
   * @return PrepaidPlan
   */
  public function getPrepaidPlan()
  {
    return $this->prepaidPlan;
  }
  /**
   * The purchased product ID (for example, 'monthly001').
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
   * Promotion details about this item. Only set if a promotion was applied
   * during signup.
   *
   * @param SignupPromotion $signupPromotion
   */
  public function setSignupPromotion(SignupPromotion $signupPromotion)
  {
    $this->signupPromotion = $signupPromotion;
  }
  /**
   * @return SignupPromotion
   */
  public function getSignupPromotion()
  {
    return $this->signupPromotion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionPurchaseLineItem::class, 'Google_Service_AndroidPublisher_SubscriptionPurchaseLineItem');
