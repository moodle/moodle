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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1TransactionInfo extends \Google\Model
{
  /**
   * All the costs associated with the products. These can be manufacturing
   * costs, shipping expenses not borne by the end user, or any other costs,
   * such that: * Profit = value - tax - cost
   *
   * @var float
   */
  public $cost;
  /**
   * Required. Currency code. Use three-character ISO-4217 code.
   *
   * @var string
   */
  public $currency;
  /**
   * The total discount(s) value applied to this transaction. This figure should
   * be excluded from TransactionInfo.value For example, if a user paid
   * TransactionInfo.value amount, then nominal (pre-discount) value of the
   * transaction is the sum of TransactionInfo.value and
   * TransactionInfo.discount_value This means that profit is calculated the
   * same way, regardless of the discount value, and that
   * TransactionInfo.discount_value can be larger than TransactionInfo.value: *
   * Profit = value - tax - cost
   *
   * @var float
   */
  public $discountValue;
  /**
   * All the taxes associated with the transaction.
   *
   * @var float
   */
  public $tax;
  /**
   * The transaction ID with a length limit of 128 characters.
   *
   * @var string
   */
  public $transactionId;
  /**
   * Required. Total non-zero value associated with the transaction. This value
   * may include shipping, tax, or other adjustments to the total value that you
   * want to include.
   *
   * @var float
   */
  public $value;

  /**
   * All the costs associated with the products. These can be manufacturing
   * costs, shipping expenses not borne by the end user, or any other costs,
   * such that: * Profit = value - tax - cost
   *
   * @param float $cost
   */
  public function setCost($cost)
  {
    $this->cost = $cost;
  }
  /**
   * @return float
   */
  public function getCost()
  {
    return $this->cost;
  }
  /**
   * Required. Currency code. Use three-character ISO-4217 code.
   *
   * @param string $currency
   */
  public function setCurrency($currency)
  {
    $this->currency = $currency;
  }
  /**
   * @return string
   */
  public function getCurrency()
  {
    return $this->currency;
  }
  /**
   * The total discount(s) value applied to this transaction. This figure should
   * be excluded from TransactionInfo.value For example, if a user paid
   * TransactionInfo.value amount, then nominal (pre-discount) value of the
   * transaction is the sum of TransactionInfo.value and
   * TransactionInfo.discount_value This means that profit is calculated the
   * same way, regardless of the discount value, and that
   * TransactionInfo.discount_value can be larger than TransactionInfo.value: *
   * Profit = value - tax - cost
   *
   * @param float $discountValue
   */
  public function setDiscountValue($discountValue)
  {
    $this->discountValue = $discountValue;
  }
  /**
   * @return float
   */
  public function getDiscountValue()
  {
    return $this->discountValue;
  }
  /**
   * All the taxes associated with the transaction.
   *
   * @param float $tax
   */
  public function setTax($tax)
  {
    $this->tax = $tax;
  }
  /**
   * @return float
   */
  public function getTax()
  {
    return $this->tax;
  }
  /**
   * The transaction ID with a length limit of 128 characters.
   *
   * @param string $transactionId
   */
  public function setTransactionId($transactionId)
  {
    $this->transactionId = $transactionId;
  }
  /**
   * @return string
   */
  public function getTransactionId()
  {
    return $this->transactionId;
  }
  /**
   * Required. Total non-zero value associated with the transaction. This value
   * may include shipping, tax, or other adjustments to the total value that you
   * want to include.
   *
   * @param float $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return float
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1TransactionInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1TransactionInfo');
