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

class LineItem extends \Google\Model
{
  protected $listingPriceType = Money::class;
  protected $listingPriceDataType = '';
  protected $oneTimePurchaseDetailsType = OneTimePurchaseDetails::class;
  protected $oneTimePurchaseDetailsDataType = '';
  protected $paidAppDetailsType = PaidAppDetails::class;
  protected $paidAppDetailsDataType = '';
  /**
   * The purchased product ID or in-app SKU (for example, 'monthly001' or
   * 'com.some.thing.inapp1').
   *
   * @var string
   */
  public $productId;
  /**
   * Developer-specified name of the product. Displayed in buyer's locale.
   * Example: coins, monthly subscription, etc.
   *
   * @var string
   */
  public $productTitle;
  protected $subscriptionDetailsType = SubscriptionDetails::class;
  protected $subscriptionDetailsDataType = '';
  protected $taxType = Money::class;
  protected $taxDataType = '';
  protected $totalType = Money::class;
  protected $totalDataType = '';

  /**
   * Item's listed price on Play Store, this may or may not include tax.
   * Excludes any discounts or promotions.
   *
   * @param Money $listingPrice
   */
  public function setListingPrice(Money $listingPrice)
  {
    $this->listingPrice = $listingPrice;
  }
  /**
   * @return Money
   */
  public function getListingPrice()
  {
    return $this->listingPrice;
  }
  /**
   * Details of a one-time purchase.
   *
   * @param OneTimePurchaseDetails $oneTimePurchaseDetails
   */
  public function setOneTimePurchaseDetails(OneTimePurchaseDetails $oneTimePurchaseDetails)
  {
    $this->oneTimePurchaseDetails = $oneTimePurchaseDetails;
  }
  /**
   * @return OneTimePurchaseDetails
   */
  public function getOneTimePurchaseDetails()
  {
    return $this->oneTimePurchaseDetails;
  }
  /**
   * Details of a paid app purchase.
   *
   * @param PaidAppDetails $paidAppDetails
   */
  public function setPaidAppDetails(PaidAppDetails $paidAppDetails)
  {
    $this->paidAppDetails = $paidAppDetails;
  }
  /**
   * @return PaidAppDetails
   */
  public function getPaidAppDetails()
  {
    return $this->paidAppDetails;
  }
  /**
   * The purchased product ID or in-app SKU (for example, 'monthly001' or
   * 'com.some.thing.inapp1').
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
   * Developer-specified name of the product. Displayed in buyer's locale.
   * Example: coins, monthly subscription, etc.
   *
   * @param string $productTitle
   */
  public function setProductTitle($productTitle)
  {
    $this->productTitle = $productTitle;
  }
  /**
   * @return string
   */
  public function getProductTitle()
  {
    return $this->productTitle;
  }
  /**
   * Details of a subscription purchase.
   *
   * @param SubscriptionDetails $subscriptionDetails
   */
  public function setSubscriptionDetails(SubscriptionDetails $subscriptionDetails)
  {
    $this->subscriptionDetails = $subscriptionDetails;
  }
  /**
   * @return SubscriptionDetails
   */
  public function getSubscriptionDetails()
  {
    return $this->subscriptionDetails;
  }
  /**
   * The tax paid for this line item.
   *
   * @param Money $tax
   */
  public function setTax(Money $tax)
  {
    $this->tax = $tax;
  }
  /**
   * @return Money
   */
  public function getTax()
  {
    return $this->tax;
  }
  /**
   * The total amount paid by the user for this line item, taking into account
   * discounts and tax.
   *
   * @param Money $total
   */
  public function setTotal(Money $total)
  {
    $this->total = $total;
  }
  /**
   * @return Money
   */
  public function getTotal()
  {
    return $this->total;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LineItem::class, 'Google_Service_AndroidPublisher_LineItem');
