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

namespace Google\Service\Walletobjects;

class TicketCost extends \Google\Model
{
  protected $discountMessageType = LocalizedString::class;
  protected $discountMessageDataType = '';
  protected $faceValueType = Money::class;
  protected $faceValueDataType = '';
  protected $purchasePriceType = Money::class;
  protected $purchasePriceDataType = '';

  /**
   * A message describing any kind of discount that was applied.
   *
   * @param LocalizedString $discountMessage
   */
  public function setDiscountMessage(LocalizedString $discountMessage)
  {
    $this->discountMessage = $discountMessage;
  }
  /**
   * @return LocalizedString
   */
  public function getDiscountMessage()
  {
    return $this->discountMessage;
  }
  /**
   * The face value of the ticket.
   *
   * @param Money $faceValue
   */
  public function setFaceValue(Money $faceValue)
  {
    $this->faceValue = $faceValue;
  }
  /**
   * @return Money
   */
  public function getFaceValue()
  {
    return $this->faceValue;
  }
  /**
   * The actual purchase price of the ticket, after tax and/or discounts.
   *
   * @param Money $purchasePrice
   */
  public function setPurchasePrice(Money $purchasePrice)
  {
    $this->purchasePrice = $purchasePrice;
  }
  /**
   * @return Money
   */
  public function getPurchasePrice()
  {
    return $this->purchasePrice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TicketCost::class, 'Google_Service_Walletobjects_TicketCost');
