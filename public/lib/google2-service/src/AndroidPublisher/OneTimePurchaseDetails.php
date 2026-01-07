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

class OneTimePurchaseDetails extends \Google\Model
{
  /**
   * The offer ID of the one-time purchase offer.
   *
   * @var string
   */
  public $offerId;
  protected $preorderDetailsType = PreorderDetails::class;
  protected $preorderDetailsDataType = '';
  /**
   * ID of the purchase option. This field is set for both purchase options and
   * variant offers. For purchase options, this ID identifies the purchase
   * option itself. For variant offers, this ID refers to the associated
   * purchase option, and in conjunction with offer_id it identifies the variant
   * offer.
   *
   * @var string
   */
  public $purchaseOptionId;
  /**
   * The number of items purchased (for multi-quantity item purchases).
   *
   * @var int
   */
  public $quantity;
  protected $rentalDetailsType = RentalDetails::class;
  protected $rentalDetailsDataType = '';

  /**
   * The offer ID of the one-time purchase offer.
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
   * The details of a pre-order purchase. Only set if it is a pre-order
   * purchase. Note that this field will be set even after pre-order is
   * fulfilled.
   *
   * @param PreorderDetails $preorderDetails
   */
  public function setPreorderDetails(PreorderDetails $preorderDetails)
  {
    $this->preorderDetails = $preorderDetails;
  }
  /**
   * @return PreorderDetails
   */
  public function getPreorderDetails()
  {
    return $this->preorderDetails;
  }
  /**
   * ID of the purchase option. This field is set for both purchase options and
   * variant offers. For purchase options, this ID identifies the purchase
   * option itself. For variant offers, this ID refers to the associated
   * purchase option, and in conjunction with offer_id it identifies the variant
   * offer.
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
   * The number of items purchased (for multi-quantity item purchases).
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
   * The details of a rent purchase. Only set if it is a rent purchase.
   *
   * @param RentalDetails $rentalDetails
   */
  public function setRentalDetails(RentalDetails $rentalDetails)
  {
    $this->rentalDetails = $rentalDetails;
  }
  /**
   * @return RentalDetails
   */
  public function getRentalDetails()
  {
    return $this->rentalDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimePurchaseDetails::class, 'Google_Service_AndroidPublisher_OneTimePurchaseDetails');
