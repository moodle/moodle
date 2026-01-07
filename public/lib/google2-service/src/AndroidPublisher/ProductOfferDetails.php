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

class ProductOfferDetails extends \Google\Collection
{
  /**
   * Consumption state unspecified. This value should never be set.
   */
  public const CONSUMPTION_STATE_CONSUMPTION_STATE_UNSPECIFIED = 'CONSUMPTION_STATE_UNSPECIFIED';
  /**
   * Yet to be consumed.
   */
  public const CONSUMPTION_STATE_CONSUMPTION_STATE_YET_TO_BE_CONSUMED = 'CONSUMPTION_STATE_YET_TO_BE_CONSUMED';
  /**
   * Consumed already.
   */
  public const CONSUMPTION_STATE_CONSUMPTION_STATE_CONSUMED = 'CONSUMPTION_STATE_CONSUMED';
  protected $collection_key = 'offerTags';
  /**
   * Output only. The consumption state of the purchase.
   *
   * @var string
   */
  public $consumptionState;
  /**
   * The offer ID. Only present for offers.
   *
   * @var string
   */
  public $offerId;
  /**
   * The latest offer tags associated with the offer. It includes tags inherited
   * from the purchase option.
   *
   * @var string[]
   */
  public $offerTags;
  /**
   * The per-transaction offer token used to make this purchase line item.
   *
   * @var string
   */
  public $offerToken;
  protected $preorderOfferDetailsType = PreorderOfferDetails::class;
  protected $preorderOfferDetailsDataType = '';
  /**
   * The purchase option ID.
   *
   * @var string
   */
  public $purchaseOptionId;
  /**
   * The quantity associated with the purchase of the inapp product.
   *
   * @var int
   */
  public $quantity;
  /**
   * The quantity eligible for refund, i.e. quantity that hasn't been refunded.
   * The value reflects quantity-based partial refunds and full refunds.
   *
   * @var int
   */
  public $refundableQuantity;
  protected $rentOfferDetailsType = RentOfferDetails::class;
  protected $rentOfferDetailsDataType = '';

  /**
   * Output only. The consumption state of the purchase.
   *
   * Accepted values: CONSUMPTION_STATE_UNSPECIFIED,
   * CONSUMPTION_STATE_YET_TO_BE_CONSUMED, CONSUMPTION_STATE_CONSUMED
   *
   * @param self::CONSUMPTION_STATE_* $consumptionState
   */
  public function setConsumptionState($consumptionState)
  {
    $this->consumptionState = $consumptionState;
  }
  /**
   * @return self::CONSUMPTION_STATE_*
   */
  public function getConsumptionState()
  {
    return $this->consumptionState;
  }
  /**
   * The offer ID. Only present for offers.
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
   * The latest offer tags associated with the offer. It includes tags inherited
   * from the purchase option.
   *
   * @param string[] $offerTags
   */
  public function setOfferTags($offerTags)
  {
    $this->offerTags = $offerTags;
  }
  /**
   * @return string[]
   */
  public function getOfferTags()
  {
    return $this->offerTags;
  }
  /**
   * The per-transaction offer token used to make this purchase line item.
   *
   * @param string $offerToken
   */
  public function setOfferToken($offerToken)
  {
    $this->offerToken = $offerToken;
  }
  /**
   * @return string
   */
  public function getOfferToken()
  {
    return $this->offerToken;
  }
  /**
   * Offer details for a preorder offer. This will only be set for preorders.
   *
   * @param PreorderOfferDetails $preorderOfferDetails
   */
  public function setPreorderOfferDetails(PreorderOfferDetails $preorderOfferDetails)
  {
    $this->preorderOfferDetails = $preorderOfferDetails;
  }
  /**
   * @return PreorderOfferDetails
   */
  public function getPreorderOfferDetails()
  {
    return $this->preorderOfferDetails;
  }
  /**
   * The purchase option ID.
   *
   * @param string $purchaseOptionId
   */
  public function setPurchaseOptionId($purchaseOptionId)
  {
    $this->purchaseOptionId = $purchaseOptionId;
  }
  /**
   * @return string
   */
  public function getPurchaseOptionId()
  {
    return $this->purchaseOptionId;
  }
  /**
   * The quantity associated with the purchase of the inapp product.
   *
   * @param int $quantity
   */
  public function setQuantity($quantity)
  {
    $this->quantity = $quantity;
  }
  /**
   * @return int
   */
  public function getQuantity()
  {
    return $this->quantity;
  }
  /**
   * The quantity eligible for refund, i.e. quantity that hasn't been refunded.
   * The value reflects quantity-based partial refunds and full refunds.
   *
   * @param int $refundableQuantity
   */
  public function setRefundableQuantity($refundableQuantity)
  {
    $this->refundableQuantity = $refundableQuantity;
  }
  /**
   * @return int
   */
  public function getRefundableQuantity()
  {
    return $this->refundableQuantity;
  }
  /**
   * Offer details about rent offers. This will only be set for rental line
   * items.
   *
   * @param RentOfferDetails $rentOfferDetails
   */
  public function setRentOfferDetails(RentOfferDetails $rentOfferDetails)
  {
    $this->rentOfferDetails = $rentOfferDetails;
  }
  /**
   * @return RentOfferDetails
   */
  public function getRentOfferDetails()
  {
    return $this->rentOfferDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductOfferDetails::class, 'Google_Service_AndroidPublisher_ProductOfferDetails');
